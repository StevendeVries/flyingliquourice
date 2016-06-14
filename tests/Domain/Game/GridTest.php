<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

final class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_throws_exception_when_shooting_outside_grid()
    {
        $grid = Grid::generate();

        $this->setExpectedException(CoordsNotInGridException::class);

        $grid->shoot(Coords::create(100, 100));
    }

    /**
     * @test
     */
    public function it_shoots_inside_grid()
    {
        $grid = Grid::generate();

        $grid->shoot(Coords::create(1, 1));
    }
}
