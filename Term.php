<?php
namespace Hwaet;


/**
 * Term
 * 
 * An individual term to use in a display of terms
 * to be voted on by the audience of Hwaet! users
 * 
 * Stored as a JSON object thusly
 *     "no": {
 *        "votes": 3,
 *       "ipAddresses": [
 *           "7362",
 *           "3025",
 *           "7940"
 *       ],
 *       "related": []
 *   }
 */
class Term {
    protected string $term;
    protected int $votes;
    protected array $ipAddresses;
    protected array $related = [];

    public function __construct(string $term, array $termData ) {
        $this->term = $term;
        $this->votes = $termData['votes'];
        $this->ipAddresses = $termData['ipAddresses'];
        $this->related = $termData['related'];
    }

    public function getVotes(): int {
        return $this->votes;
    }

    public function getRelated(): array {
        return $this->related;
    }

    public function ipExists(string $ipAddress): bool {
        if(in_array($ipAddress, $this->ipAddresses)) {
            return true;
        }
        return false;
    }

    public function getIpAddresses(): array {
        return $this->ipAddresses;
    }

    public function addIpAddress(string $ipAddress): void {
        $this->ipAddresses[] = $ipAddress;
    }


    public function updateRelated(string $related): void {
        $this->related[] = $related;
    }

    public function incrementVotes() {
        $this->votes++;
    }

    public function toArray(): array {
        $dataArray = [
            "votes" => $this->votes,
            "ipAddresses" => $this->ipAddresses,
            "related" => $this->related,
        ];

        return $dataArray;
    }
}
