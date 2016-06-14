<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

use Assert\Assertion;

final class Score
{
    /**
     * @var FireResult[]
     */
    private $fireResults;

    /**
     * @param FireResult[] $fireResults
     * @return static
     */
    public static function create(array $fireResults = [])
    {
        return new static($fireResults);
    }

    /**
     * @param FireResult[] $fireResults
     */
    private function __construct(array $fireResults)
    {
        Assertion::allIsInstanceOf($fireResults, FireResult::class);

        $this->fireResults = $fireResults;
    }

    /**
     * @param FireResult $fireResult
     */
    public function track(FireResult $fireResult)
    {
        $this->fireResults[] = $fireResult;
    }

    public function __toString()
    {
        return (string) $this->score();
    }

    public function score()
    {
        return ($this->hits() + ($this->sank() * 3)) / ($this->misses() + 1);
    }

    /**
     * @return int
     */
    public function hits()
    {
        $hits = 0;
        foreach ($this->fireResults as $fireResult) {
            if ($fireResult->isHit()) {
                $hits++;
            }
        }
        return $hits;
    }

    /**
     * @return int
     */
    public function misses()
    {
        $misses = 0;
        foreach ($this->fireResults as $fireResult) {
            if ($fireResult->isMiss()) {
                $misses++;
            }
        }
        return $misses;
    }

    /**
     * @return int
     */
    public function shots()
    {
        return count($this->fireResults);
    }

    /**
     * @return int
     */
    public function sank()
    {
        $sank = 0;
        foreach ($this->fireResults as $fireResult) {
            if ($fireResult->isSank()) {
                $sank++;
            }
        }
        return $sank;
    }
}
