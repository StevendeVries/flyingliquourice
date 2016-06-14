<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

final class CoordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calculates_distances_between_coords()
    {
        $coords = Coords::create(1, 1);

        $this->assertEquals(2, $coords->distance(Coords::create(1, 3)));

        $this->assertEquals(2, $coords->distance(Coords::create(3, 1)));
    }

    /**
     * @test
     */
    public function it_throws_exception_when_calculating_distance_between_diaginal_coords()
    {
        $coords = Coords::create(1, 1);

        $this->setExpectedException(CannotCalculateDistanceBetweenDiagonalCoordsException::class);

        $this->assertEquals(2, $coords->distance(Coords::create(3, 3)));
    }

    /**
     * @test
     */
    public function it_asks_coords_of_field_below_to_given_coords_with_distance()
    {
        $coords = Coords::create(1, 3);

        $this->assertEquals(Coords::create(1, 5), $coords->below(2));
    }

    /**
     * @test
     */
    public function it_asks_coords_of_field__right_to_given_coords_with_distance()
    {
        $coords = Coords::create(1, 3);

        $this->assertEquals(Coords::create(3, 3), $coords->right(2));
    }
}
