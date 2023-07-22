<?php


class Term {
    protected string $term;
    protected int $votes;
    protected array $ipAddresses;
    protected array $see;

    public function __construct(string $term, array $termData ) {
        $this->term = $term;
        $this->votes = $termData['votes'];
        $this->ipAddresses = $termData['ipAddresses'];
        $this->see = $termData['see'];
    }

    public function ipExists(string $ipAddress): bool {
        if(in_array($ipAddress, $this->ipAddresses)) {
            return true;
        }
        return false;
    }

    public function addIpAddress(string $ipAddress): void {
        $this->ipAddresses[] = $ipAddress;
    }


    public function updatesee(string $seeTerm): void {
        $this->see[] = $seeTerm;
    }

    public function incrementVotes() {
        $this->votes++;
    }

    public function toArray(): array {
        $dataArray = [
            "votes" => $this->votes,
            "ipAddresses" => $this->ipAddresses,
            "see" => $this->see,
        ];

        return $dataArray;
    }
}
