<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

use Assert\Assertion;

class Grid
{
    const DEFAULT_WIDTH  = 10;
    const DEFAULT_HEIGHT = 10;

    /**
     * @var array
     */
    private static $defaultShipSizes = [6,4,4,3,3,3,2,2,2,2];

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var Fields
     */
    private $fields;

    public function __construct($width, $height, Fields $fields)
    {
        Assertion::integer($width);
        Assertion::integer($height);

        $this->width  = $width;
        $this->height = $height;
        $this->fields = $fields;
    }

    /**
     * @param array $grid
     * @return static
     */
    public static function fromArray(array $grid)
    {
        return new static($grid['width'], $grid['height'], Fields::fromArray($grid['fields']));
    }

    /**
     * @param int $width
     * @param int $height
     * @param array $shipSizes
     * @return static
     */
    public static function generate(
        $width = null,
        $height = null,
        array $shipSizes = null
    ) {
        return new static(
            ($width === null) ? static::DEFAULT_WIDTH : $width,
            ($height === null) ? static::DEFAULT_HEIGHT : $height,
            FieldsGenerator::generate(
                ($width === null) ? static::DEFAULT_WIDTH : $width,
                ($height === null) ? static::DEFAULT_HEIGHT : $height,
                ($shipSizes === null) ? static::$defaultShipSizes : $shipSizes
            )
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['width' => $this->width, 'height' => $this->height, 'fields'=> $this->fields->toArray()];
    }

    /**
     * Checks if coordinates are inside the field
     *
     * @param Coords $coords
     * @return bool
     */
    public function has(Coords $coords)
    {
        $x = $coords->x();

        if ($x < 0 || $x >= $this->width) {
            return false;
        }

        $y = $coords->y();

        if ($y < 0 || $y >= $this->height) {
            return false;
        }

        return true;
    }

    /**
     * @param Coords $coords
     * @return bool
     */
    public function hasShipAt(Coords $coords)
    {
        $this->ensureCoordsInGrid($coords);

        /** @var Field $field */
        foreach ($this->fields as $field) {
            if ($field->at($coords) && $field->occupied()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Coords $coords
     */
    public function shoot(Coords $coords)
    {
        $this->ensureCoordsInGrid($coords);

        $this->fields->shoot($coords);
    }

    /**
     * @param Coords $coords
     * @return bool
     */
    public function didShipSankAt(Coords $coords)
    {
        $this->ensureCoordsInGrid($coords);

        return $this->fields->didShipSankAt($coords);
    }

    /**
     * @param Coords $coords
     * @return Coords
     */
    public function startPointOfShipAt(Coords $coords)
    {
        $this->ensureCoordsInGrid($coords);

        return $this->fields->startPointOfShipAt($coords);
    }

    /**
     * @param Coords $coords
     * @return Coords
     */
    public function endPointOfShipAt(Coords $coords)
    {
        $this->ensureCoordsInGrid($coords);

        return $this->fields->endPointOfShipAt($coords);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->fields;
    }

    /**
     * @param Coords $coords
     */
    private function ensureCoordsInGrid(Coords $coords)
    {
        if (!$this->has($coords)) {
            throw new CoordsNotInGridException();
        }
    }

    /**
     * @return bool
     */
    public function didAllShipsSink()
    {
        return $this->fields->didAllShipsSink();
    }

    /**
     * @return Ship[]
     */
    public function ships()
    {
        return $this->fields->ships();
    }
}
