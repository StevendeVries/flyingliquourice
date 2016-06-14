<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

final class ShipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_ship_from_correct_data()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 0
        ];
        $ship = Ship::fromArray($data);
        $this->assertEquals($data, $ship->toArray());
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_an_exception_if_provided_invalid_data()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => '1'
        ];
        Ship::fromArray($data);
    }

    /**
     * @test
     */
    public function it_hits_a_ship_but_dont_sink_it()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 0
        ];
        $ship = Ship::fromArray($data);

        $ship->hit();

        $this->assertFalse($ship->hasSunk());
    }

    /**
     * @test
     */
    public function it_sinks_a_ship()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 0
        ];
        $ship = Ship::fromArray($data);

        $ship->hit();
        $ship->hit();
        $ship->hit();

        $this->assertTrue($ship->hasSunk());
    }

    /**
     * @test
     */
    public function it_hits_a_sunken_ship()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 0
        ];
        $ship = Ship::fromArray($data);

        $ship->hit();
        $ship->hit();
        $ship->hit();
        $ship->hit();

        $this->assertTrue($ship->hasSunk());
    }
}
