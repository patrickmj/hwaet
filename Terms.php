<?php


class Term {
    protected string $term;
    protected int $votes;
    protected array $ipAddresses;
    protected array $sameAs;

    public function __construct(string $term, array $termData ) {
        $this->term = $term;
        $this->votes = $termData['votes'];
        $this->ipAddresses = $termData['ipAddresses'];
        $this->sameAs = $termData['sameAs'];
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


    public function updateSameAs(string $sameAsTerm): void {
        $this->sameAs[] = $sameAsTerm;
    }

    public function incrementVotes() {
        $this->votes++;
    }

    public function toArray(): array {
        $dataArray = [
            "votes" => $this->votes,
            "ipAddresses" => $this->ipAddresses,
            "sameAs" => $this->sameAs,
        ];

        return $dataArray;
    }
}
