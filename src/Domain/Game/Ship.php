<?php


namespace Wecamp\FlyingLiqourice\Domain\Game;

use Assert\Assertion;

class Ship
{
    const MINIMUM_SIZE = 2;

    /**
     * @var Coords
     */
    private $startPoint;

    /**
     * @var Coords
     */
    private $endPoint;

    /**
     * @var int
     */
    private $hits = 0;

    private function __construct(Coords $startPoint, Coords $endPoint, $hits = 0)
    {
        Assertion::integer($hits);
        Assertion::greaterOrEqualThan($hits, 0);

        $size = $startPoint->distance($endPoint) + 1;
        Assertion::greaterOrEqualThan($size, static::MINIMUM_SIZE);
        Assertion::lessOrEqualThan($hits, $size);

        $this->startPoint = $startPoint;
        $this->endPoint   = $endPoint;
        $this->hits       = $hits;
    }

    /**
     * @param array $ship
     * @return static
     */
    public static function fromArray(array $ship)
    {
        return new static(
            Coords::fromArray($ship['startPoint']),
            Coords::fromArray($ship['endPoint']),
            $ship['hits']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'startPoint' => $this->startPoint->toArray(),
            'endPoint' => $this->endPoint->toArray(),
            'hits' => $this->hits
        ];
    }

    /**
     * @param Coords $startPoint
     * @param Coords $endPoint
     * @return static
     */
    public static function create(Coords $startPoint, Coords $endPoint)
    {
        return new static($startPoint, $endPoint);
    }

    public function hit()
    {
        $this->hits++;
    }

    /**
     * @return bool
     */
    public function hasSunk()
    {
        $size = $this->startPoint->distance($this->endPoint) + 1;

        return ($this->hits >= $size);
    }

    /**
     * @return Coords
     */
    public function startPoint()
    {
        return $this->startPoint;
    }

    /**
     * @return Coords
     */
    public function endPoint()
    {
        return $this->endPoint;
    }

    /**
     * @param Coords $coords
     * @return bool
     */
    public function on(Coords $coords)
    {
        if ($coords->x() == $this->startPoint->x()
            && $coords->y() >= $this->startPoint->y()
            && $coords->y() <= $this->endPoint->y()
        ) {
            return true;
        }

        if ($coords->y() == $this->startPoint->y()
            && $coords->x() >= $this->startPoint->x()
            && $coords->x() <= $this->endPoint->x()
        ) {
            return true;
        }

        return false;
    }
}
