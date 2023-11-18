<?php
namespace Hwaet;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('Term.php');

use \DOMDocument;
use Hwaet\Term as Term;

if (isset($_GET['term'])) {
    $termKey = $_GET['term'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    //@todo remove. only for devving
    if($ipAddress == '127.0.0.1') {
        $ipAddress = rand(1, 10000 );
    }
    $hwaet = new Hwaet($termKey, $ipAddress);
} else {
    $hwaet = new Hwaet();
}

$html = new DOMDocument();
$html->loadHTMLFile('templates/hwaet.html');
$mainNode = $html->getElementById('main');
$infoNode = $html->getElementById('info');





/**
 * Hwaet
 * 
 * The simple controller to accept new terms, upvote them, and 
 * look for duplicate terms from the same IP address (cheeky devils!)
 * 
 */
class Hwaet {
    protected string $termsFilePath = 'data/terms.json';
    protected ?string $termKey = null;
    protected ?string $ipAddress = null;
    protected array $terms = [];
    protected DOMDocument $hwaetPage;


    public function __construct(string $termKey = null, string $ipAddress = null) {
        $this->loadTerms();
        
        $this->hwaetPage = new DOMDocument();
        $this->hwaetPage->loadHTMLFile('templates/hwaet.html');
        $mainPanel = $this->hwaetPage->getElementById('main');
        $infoPanel = $this->hwaetPage->getElementById('info');
    
        // if params aren't set, there's no new term to register so just stop after
        // loading the terms so they can be returned to D3 with no other actions
        if (is_null(($termKey)) && is_null($ipAddress)) {
            $termsData = $this->viewTerms();
            $termsData = json_encode($termsData, JSON_PRETTY_PRINT);
            echo $termsData;
            return;
        }

        $this->termKey = $termKey;
        $this->ipAddress = $ipAddress;
        
        $status = $this->termStatus();
        switch($status['status']) {
            case 'new':
                $this->createTerm();
                break;
            case 'cheekyDevil':
                $currentTerm = $this->terms[$status['termKey']];
                
                break;
            case 'exists':
                $currentTerm = $this->terms[$status['termKey']];
                $currentTerm->incrementVotes();
                $currentTerm->addIpAddress($this->ipAddress);
                break;
        }

        $viewTerms = $this->viewTerms();

        $this->updateTerms();
    }
    /**
     * viewTerms
     *
     * Loop through the terms and return the data array
     * 
     * 
     * @return array the array of terms data
     */
    public function viewTerms(): array {
        $returnTerms = [];
        foreach($this->terms as $termKey=>$term) {
            $returnTerms[$termKey] = [
                'votes' => $term->getVotes(),
                //the js will have to know the user's ip address
                //and check against this array for cheeky devils
                //'ipAddresses' => $term->getIpAddresses(),
                'related' => $term->getRelated(),
            ];
        }

        return $returnTerms;
    }

    protected function sortTerms($termA, $termB) {

    }

    protected function loadTerms() {
        $termsArray = json_decode(file_get_contents($this->termsFilePath), true);
        foreach($termsArray as $termKey=>$termData) {
            $storedTerm = new Term($termKey, $termData);
            $this->terms[$termKey] = $storedTerm;
        }
    }

    protected function termStatus() {
        if(array_key_exists($this->termKey, $this->terms)) {
            $term = $this->terms[$this->termKey];
            if($term->ipExists($this->ipAddress)) {
                return ['status' => 'cheekyDevil', 'termKey'=>$this->termKey];
            }
            return ['status'=>'exists', 'termKey'=>$this->termKey];
        } else {
            return ['status'=>'new', 'termKey'=>$this->termKey];
        }
    }

    protected function createTerm() {
        $termData = [
            "votes" => 1,
            "ipAddresses" => [$this->ipAddress],
            "related" => []
        ];

        $newTerm = new Term($this->termKey, $termData);
        $this->terms[$this->termKey] = $newTerm;
    }

    //would be different in a SQL world, but this isn't (yet) that world
    protected function updateTerms(): void {
        $toUpdateArray = []; 
        foreach($this->terms as $term=>$termObject) {
            $toUpdateArray[$term] = $termObject->toArray();
        }
        $toUpdateJson = json_encode($toUpdateArray, JSON_PRETTY_PRINT);
        file_put_contents($this->termsFilePath, $toUpdateJson);
    }
}
