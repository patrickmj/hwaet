<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once('Terms.php');

if (isset($_GET['term'])) {
    $termKey = $_GET['term'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if($ipAddress == '127.0.0.1') {
        $ipAddress = rand(1, 10000 );
    }
    $hwaet = new Hwaet($termKey, $ipAddress);
}

class Hwaet {
    protected string $termsFilePath = 'data/terms.json';
    protected string $termKey;
    protected string $ipAddress;
    protected array $terms = [];

    public function __construct(string $termKey, string $ipAddress) {
        $this->loadTerms();
        $this->termKey = $termKey;
        $this->ipAddress = $ipAddress;
        $status = $this->termStatus();
        echo $status['status'];
        switch($status['status']) {
            case 'new':
                $this->createTerm();
                break;
            case 'cheekyDevil':
                $term = $this->terms[$status['termKey']];
                break;
            case 'exists':
                $term = $this->terms[$status['termKey']];
                $term->incrementVotes();
                $term->addIpAddress($this->ipAddress);
                break;
        }






        $this->updateTerms();
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
        echo 'createTerm' . PHP_EOL;
        $termData = [
            "votes" => 1,
            "ipAddresses" => [$this->ipAddress],
            //not worrying about whether there's a sameAs here,
            //since that's something for moderators to monitor and
            //update manually elsewhere
            "sameAs" => []
        ];

        $newTerm = new Term($this->termKey, $termData);
        $this->terms[$this->termKey] = $newTerm;
    }

    //would be different in a SQL world
    protected function updateTerms(): void {
        $toUpdateArray = []; 
        foreach($this->terms as $term=>$termObject) {
            $toUpdateArray[$term] = $termObject->toArray();
        }
        $toUpdateJson = json_encode($toUpdateArray, JSON_PRETTY_PRINT);
        file_put_contents($this->termsFilePath, $toUpdateJson);
    }
}
