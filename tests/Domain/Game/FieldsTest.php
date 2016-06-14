<?php

namespace Wecamp\FlyingLiqourice\Domain\Game;

final class FieldsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_fields_without_ships()
    {
        $fields   = [];
        $fields[] = Field::generate(1, 1);
        $fields[] = Field::generate(1, 2);

        $fieldsClass = Fields::create($fields);

        $fieldsClass->shoot(Coords::create(1, 1));

        $this->assertEquals(false, $fieldsClass->didShipSankAt(Coords::create(1, 1)));

        $this->assertEquals(true, $fieldsClass->didAllShipsSink());
    }

    /**
     * @test
     */
    public function it_shoots_outside_fields_without_ships()
    {
        $fields   = [];
        $fields[] = Field::generate(1, 1);
        $fields[] = Field::generate(1, 2);

        $fieldsClass = Fields::create($fields);


        $fieldsClass->shoot(Coords::create(5, 5));

        $this->assertEquals(false, $fieldsClass->didShipSankAt(Coords::create(5, 5)));

        $this->setExpectedException(NoShipAtTheseCoordsException::class);

        $fieldsClass->startPointOfShipAt(Coords::create(5, 5));
    }

    /**
     * @test
     */
    public function it_shoots_a_ship_in_fields_with_a_ship()
    {
        $fields = [];

        $data = [
            'startPoint' => ['x' => 1, 'y' => 1],
            'endPoint' => ['x' => 1, 'y' => 2],
            'hits' => 0
        ];
        $ship = Ship::fromArray($data);

        $field = Field::generate(1, 1);
        $field->place($ship);
        $fields[] = $field;

        $field = Field::generate(1, 2);
        $field->place($ship);
        $fields[] = $field;

        $fieldsClass = Fields::create($fields);

        $fieldsClass->shoot(Coords::create(1, 1));

        $this->assertEquals(false, $fieldsClass->didShipSankAt(Coords::create(1, 1)));

        $this->assertEquals(false, $fieldsClass->didAllShipsSink());
    }

    /**
     * @test
     */
    public function it_sinks_a_ship_in_fields_with_ships()
    {
        $fields = [];

        $data = [
            'startPoint' => ['x' => 0, 'y' => 0],
            'endPoint' => ['x' => 0, 'y' => 1],
            'hits' => 0
        ];
        $ship = Ship::fromArray($data);

        $field = Field::generate(0, 0);
        $field->place($ship);
        $fields[] = $field;

        $field = Field::generate(0, 1);
        $field->place($ship);
        $fields[] = $field;

        $data = [
            'startPoint' => ['x' => 1, 'y' => 0],
            'endPoint' => ['x' => 1, 'y' => 1],
            'hits' => 0
        ];
        $ship = Ship::fromArray($data);

        $field = Field::generate(1, 0);
        $field->place($ship);
        $fields[] = $field;

        $field = Field::generate(1, 1);
        $field->place($ship);
        $fields[] = $field;

        $fieldsClass = Fields::create($fields);

        $fieldsClass->shoot(Coords::create(0, 0));
        $fieldsClass->shoot(Coords::create(0, 1));

        $this->assertTrue($fieldsClass->didShipSankAt(Coords::create(0, 1)));

        $this->assertFalse($fieldsClass->didAllShipsSink());
    }

    /**
     * @test
     */
    public function it_sinks_a_ship_in_fields_with_a_ship()
    {
        $fields = [];

        $data = [
            'startPoint' => ['x' => 1, 'y' => 1],
            'endPoint' => ['x' => 1, 'y' => 2],
            'hits' => 1
        ];
        $ship = Ship::fromArray($data);

        $field = Field::generate(1, 1);
        $field->place($ship);
        $fields[] = $field;

        $field = Field::generate(1, 2);
        $field->place($ship);
        $fields[] = $field;

        $fieldsClass = Fields::create($fields);

        $fieldsClass->shoot(Coords::create(1, 1));

        $this->assertEquals(true, $fieldsClass->didShipSankAt(Coords::create(1, 1)));

        $this->assertEquals(true, $fieldsClass->didAllShipsSink());
    }
}
