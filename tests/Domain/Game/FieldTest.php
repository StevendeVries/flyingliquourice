<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

final class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_field_with_a_sunken_ship()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 3
        ];
        $ship = Ship::fromArray($data);

        $field = Field::generate(1, 1);
        $field->place($ship);

        $this->assertEquals(true, $field->occupied());
        $this->assertEquals(true, $field->hasSunkenShip());
    }

    /**
     * @test
     */
    public function it_creates_a_field_with_a_ship()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 1
        ];
        $ship  = Ship::fromArray($data);
        $field = Field::generate(1, 1);
        $field->place($ship);
        $this->assertEquals(true, $field->occupied());
        $this->assertEquals(false, $field->hasSunkenShip());
    }

    /**
     * @test
     */
    public function it_creates_a_field_without_a_ship()
    {
        $field = Field::generate(1, 1);
        $this->assertEquals(false, $field->occupied());
        $this->assertEquals(false, $field->hasSunkenShip());
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_creates_a_field_with_incorect_data()
    {
        $field = Field::generate('abc', 'abc');
        $this->assertEquals(false, $field->occupied());
        $this->assertEquals(false, $field->hasSunkenShip());
    }

    /**
     * @test
     */
    public function it_sinks_a_ship_in_a_field()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 2
        ];
        $ship  = Ship::fromArray($data);
        $field = Field::generate(1, 1);
        $field->place($ship);
        $this->assertEquals(true, $field->occupied());
        $this->assertEquals(false, $field->hasSunkenShip());

        $field->shoot();

        $this->assertEquals(true, $field->hasSunkenShip());
    }

    /**
     * @test
     */
    public function it_shoots_a_field_which_already_been_hit()
    {
        $data = [
            'startPoint'    => ['x'=>2,'y'=>2],
            'endPoint'      => ['x'=>2,'y'=>4],
            'hits'          => 2
        ];
        $ship  = Ship::fromArray($data);
        $field = Field::generate(1, 1);
        $field->shoot();
        $field->place($ship);
        $this->setExpectedException(FieldAlreadyBeenShotException::class);

        $field->shoot();
    }

    /**
     * @test
     */
    public function it_asks_ship_startpoint_on_a_field_without_a_ship()
    {
        $field = Field::generate(1, 1);

        $this->setExpectedException(NoShipAtThisFieldException::class);

        $field->startPointOfShip();
    }

    /**
     * @test
     */
    public function it_asks_ship_endpoint_on_a_field_without_a_ship()
    {
        $field = Field::generate(1, 1);

        $this->setExpectedException(NoShipAtThisFieldException::class);

        $field->endPointOfShip();
    }
}
