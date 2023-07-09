<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $hwaet = new Hwaet($term, $ipAddress);
}

class Hwaet {
    private string $termsFileName = 'data/terms.js';
    private string $ipAddressVotesFileName = 'data/ipAddressVotes.js';
    private string $votesFileName = 'data/votes.js';
    protected array $terms = [];
    protected array $ipAddressVotes = [];
    protected array $votes = [];
    protected string $ipAddress;

    public function __construct(string $term, string $ipAddress) {
        $this->init($ipAddress);
        $this->upvoteTerm($term);
    }

    public function init(string $ipAddress) {
        $this->terms = json_decode(file_get_contents($this->termsFileName), true);
        $this->ipAddressVotes = json_decode(file_get_contents($this->ipAddressVotesFileName), true);
        $this->votes = json_decode(file_get_contents($this->votesFileName), true);
        $this->ipAddress = $ipAddress;
    }

    public function addIpAddressVote(string $ipAddress, int $termId) {
        $this->ipAddressVotes[$ipAddress][] = $termId;
        print_r($this->ipAddressVotes[$ipAddress]);
    }

    public function checkIpAddressVote(string $ipAddress, int $termId) {
        print_r($this->ipAddressVotes);
        echo $ipAddress;
        //die();
        if(array_key_exists($ipAddress, $this->ipAddressVotes)) {
            echo "cheeky devil!";
        } else {
            echo "ok";
            $this->addIpAddressVote($ipAddress, $termId);
        }
    }

    /**
     * adds a term if it doesn't already exist
     * otherwise, returns its ID after checking if it's a cheeky devil's IP address
     */
    public function upvoteTerm(string $upvoteTerm) {
        $termExists = false;
        $termId = null;
        foreach ($this->terms as $term => $termData) {
            if ($termData['term'] == $upvoteTerm) {
                $termExists = true;
                $termId = $termData['id']; 
                break;
            }
        }
        
        if ($termExists) {
            //vscode thinks this is still null b/c it doesn't know the
            //logic of when it gets set above,
            //hence the check for whether it's null to make it STFU
            if (! is_null($termId)) {
                $this->checkIpAddressVote($this->ipAddress, $termId);
                $this->addVote($termId);
            }
        } else {
            $termsLength = count($this->terms);
            $this->terms[] = ['id' => $termsLength,
                              'term' => $upvoteTerm,
                              'sameAs' => []
                             ];
                             
            //put a lock on the file during the transaction?
            //need to test with many people upvoting at same time
            //in effect, attempt a DOS attack
            file_put_contents($this->termsFileName, json_encode($this->terms));
        }
    }

    public function addVote(int $termId) {
       $this->votes[$termId]++;
       file_put_contents($this->votesFileName, json_encode($this->votes));
    }
}
