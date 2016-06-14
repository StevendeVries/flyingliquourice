<?php

namespace Wecamp\FlyingLiqourice\Domain;

use Assert\Assertion;
use Wecamp\FlyingLiqourice\Domain\Game\Coords;
use Wecamp\FlyingLiqourice\Domain\Game\FireResult;
use Wecamp\FlyingLiqourice\Domain\Game\GameIsLockedException;
use Wecamp\FlyingLiqourice\Domain\Game\Grid;
use Wecamp\FlyingLiqourice\Domain\Game\Score;

final class Game
{
    /**
     * @var FireResult[]
     */
    private $fireResults = [];

    /**
     * @var Identifier
     */
    private $id;

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var bool
     */
    private $locked;

    /**
     * @var Score
     */
    private $score;

    /**
     * Creates a new game.
     *
     * @param int   $width
     * @param int   $height
     * @param array $shipSizes
     *
     * @return static
     */
    public static function create($width = null, $height = null, array $shipSizes = null)
    {
        return new static(
            GameIdentifier::generate(),
            Grid::generate($width, $height, $shipSizes),
            Score::create()
        );
    }

    /**
     * @param Coords $coords
     *
     * @return FireResult
     */
    public function fire(Coords $coords)
    {
        if ($this->locked()) {
            throw new GameIsLockedException;
        }

        $this->grid->shoot($coords);
        if (!$this->grid->hasShipAt($coords)) {
            $miss = FireResult::miss($coords);
            $this->trackFireResult($miss);

            return $miss;
        }

        if ($this->grid->didAllShipsSink()) {
            $this->lock();
            $win = FireResult::win(
                $coords,
                $this->grid->startPointOfShipAt($coords),
                $this->grid->endPointOfShipAt($coords)
            );

            $this->trackFireResult($win);

            return $win;
        }

        if ($this->grid->didShipSankAt($coords)) {
            $sank = FireResult::sank(
                $coords,
                $this->grid->startPointOfShipAt($coords),
                $this->grid->endPointOfShipAt($coords)
            );

            $this->trackFireResult($sank);

            return $sank;
        }

        $hit = FireResult::hit($coords);

        $this->trackFireResult($hit);

        return $hit;
    }

    /**
     * @return FireResult[]
     */
    public function status()
    {
        return $this->fireResults;
    }

    /**
     * @return Game\Ship[]
     */
    public function surrender()
    {
        $this->lock();

        return $this->grid->ships();
    }

    /**
     * Recreates a game from an array.
     *
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data)
    {
        $fireResults = [];
        foreach ($data['fireResults'] as $fireResult) {
            $fireResults[] = FireResult::fromArray($fireResult);
        }

        return new static(
            GameIdentifier::fromString($data['id']),
            Grid::fromArray($data['grid']),
            Score::create($fireResults),
            $fireResults,
            $data['locked']
        );
    }

    /**
     * Get the identifier of this game.
     *
     * @return Identifier
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Converts this game to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $fireResults = [];
        foreach ($this->fireResults as $fireResult) {
            $fireResults[] = $fireResult->toArray();
        }

        return [
            'id'          => (string) $this->id,
            'grid'        => $this->grid->toArray(),
            'fireResults' => $fireResults,
            'locked'      => $this->locked
        ];
    }

    /**
     * @return Game\Ship[]
     */
    public function ships()
    {
        return $this->grid->ships();
    }

    public function score()
    {
        return $this->score;
    }

    public function __toString()
    {
        return ((string) $this->grid) . PHP_EOL;
    }

    /**
     * @param Identifier   $id
     * @param Grid         $grid
     * @param Score        $score
     * @param FireResult[] $fireResults
     * @param bool         $locked
     */
    private function __construct(Identifier $id, Grid $grid, Score $score, array $fireResults = [], $locked = false)
    {
        Assertion::boolean($locked);
        Assertion::allIsInstanceOf($fireResults, FireResult::class);

        $this->id          = $id;
        $this->grid        = $grid;
        $this->score       = $score;
        $this->fireResults = $fireResults;
        $this->locked      = $locked;
    }

    private function lock()
    {
        $this->locked = true;
    }

    /**
     * @return bool
     */
    private function locked()
    {
        return $this->locked;
    }

    /**
     * @param FireResult $fireResult
     */
    private function trackFireResult(FireResult $fireResult)
    {
        $this->fireResults[] = $fireResult;
        $this->score->track($fireResult);
    }
}
