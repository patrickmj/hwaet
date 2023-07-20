<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if($ipAddress == '127.0.0.1') {
        $ipAddress = rand(1, 10000 );
    }
    echo $ipAddress;
    $hwaet = new Hwaet($term, $ipAddress);
}

class Hwaet {
    protected string $termsFilePath = 'data/terms.json';
    protected array $terms;

    public function __construct(string $term, string $ipAddress) {
        $this->init($term, $ipAddress);
    }

    protected function init(string $term, string $ipAddress) {
        $termsArray = json_decode(file_get_contents($this->termsFilePath), true);
        foreach($termsArray as $term=>$termData) {
            $term = new Term($term, $termData);
            $this->terms[] = $term;
        }
    }

    protected function createTerm(string $term, string $ipAddress) {
        $termData = [
            "votes" => 1,
            "cheekyIps" => [],
            //not worrying about whether there's a sameAs here,
            //since that's something for moderators to monitor and
            //update manually elsewhere
            "sameAs" => []
        ];

        $newTerm = new Term($term, $termData);
        $this->terms[$term] = $newTerm;
    }

    //would be different in a SQL world
    protected function updateTerms(): void {
        $toUpdateArray = []; 
        foreach($this->terms as $term=>$termObject) {
            $toUpdateArray[$term] = $termObject->toArray();
        }
        file_put_contents($this->termsFilePath, $toUpdateArray);
    }
}

class Term {
    protected string $term;
    protected int $votes;
    protected array $cheekyIps;
    protected array $sameAs;

    public function __construct(string $term, array $termData ) {
        $this->term = $term;
        $this->votes = $termData['votes'];
        $this->cheekyIps = $termData['cheekyIps'];
        $this->sameAs = $termData['sameAs'];
    }

    public function updateCheekyIps(string $ipAddress): void {
        $this->cheekyIps[] = $ipAddress;
    }

    public function updateSameAs(string $sameAsTerm): void {
        $this->sameAs[] = $sameAsTerm;
    }

    public function incrementVotes() {
        $this->votes++;
    }

    public function toArray(): array {
        $dataArray = [
            "votes" => $this->votes,
            "cheekyIps" => $this->cheekyIps,
            "sameAs" => $this->sameAs,
        ];

        return $dataArray;
    }
}
