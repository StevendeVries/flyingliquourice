<?php

namespace Wecamp\FlyingLiqourice\Domain;

use Rhumsaa\Uuid\Uuid;

final class GameIdentifierTest extends \PHPUnit_Framework_TestCase
{
    const PATTERN_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    /**
     * @test
     */
    public function it_creates_an_id_from_uuid()
    {
        $uuid = Uuid::uuid4()->toString();

        $this->assertEquals($uuid, (string) GameIdentifier::fromString($uuid));
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_an_exception_if_provided_value_is_not_uuid()
    {
        GameIdentifier::fromString('123');
    }

    /**
     * @test
     */
    public function it_generates_uuid()
    {
        $this->assertRegExp(self::PATTERN_UUID, (string) GameIdentifier::generate());
    }

    /**
     * @test
     */
    public function it_can_be_cast_to_a_string()
    {
        $uuid = Uuid::uuid4()->toString();

        $this->assertSame($uuid, (string) GameIdentifier::fromString($uuid));
    }

    /**
     * @test
     */
    public function it_can_be_compared_with_another_id()
    {
        $anId         = GameIdentifier::fromString('de305d54-75b4-431b-adb2-eb6b9e546014');
        $anotherId    = GameIdentifier::fromString('de305d54-75b4-431b-adb2-eb6b9e546014');
        $yetAnotherId = GameIdentifier::fromString('00000000-0000-0000-0000-000000000000');

        $this->assertTrue($anId->equals($anId));
        $this->assertTrue($anId->equals($anotherId));
        $this->assertTrue($anotherId->equals($anId));
        $this->assertFalse($anId->equals($yetAnotherId));
        $this->assertFalse($yetAnotherId->equals($anotherId));
    }
}
