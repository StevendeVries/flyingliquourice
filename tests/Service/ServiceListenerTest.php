<?php
/**
 * Created by PhpStorm.
 * User: henri
 * Date: 8/26/15
 * Time: 2:28 PM
 */

namespace Wecamp\FlyingLiqourice\Service;

use Rhumsaa\Uuid\Uuid;
use Wecamp\FlyingLiqourice\Domain\Game;

class ServiceListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_the_game_id()
    {
        $repository = $this->getMockBuilder('stdClass')
            ->setMethods(['get', 'save'])
            ->getMock();

        $token   = 'START';
        $id      = Uuid::uuid4()->toString();
        $service = new ServiceListener($token, $id, $repository);
        $this->assertEquals($id, $service->id());
    }

    /**
     * @test
     */
    public function it_creates_a_new_game()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'START';
        $service = new ServiceListener($token, '', $repository);

        $this->assertStringStartsWith('STARTED ', $service->run());
    }

    /**
     * @test
     */
    public function it_resumed_a_game()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'RESUME ' . $game->id();
        $service = new ServiceListener($token, $game->id(), $repository);
        $this->assertEquals('RESUMED ' . $game->id(), $service->run());
    }

    /**
     * @test
     */
    public function it_shows_status_of_a_game()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'STATUS ' . $game->id();
        $service = new ServiceListener($token, $game->id(), $repository);
        $this->assertStringStartsWith('STATUS', $service->run());
    }

    /**
     * @test
     */
    public function it_ends_a_game()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'surrender';
        $service = new ServiceListener($token, $game->id(), $repository);
        $this->assertStringStartsWith('SURRENDERED', $service->run());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_fires_on_a3()
    {
        $game       = Game::create();
        $repository = $this->repository($game);
        //$id = Uuid::uuid4()->toString();
        $token   = 'fire 12.34';
        $service = new ServiceListener($token, $game->id(), $repository);
        $this->assertEquals('"Shot has been fired on 12-34"', $service->run());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_runs_a_non_existing_command()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'unknown command';
        $service = new ServiceListener($token, $game->id(), $repository);
        $service->run();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_runs_non_existing_command_run()
    {
        $game       = Game::create();
        $repository = $this->repository($game);

        $token   = 'run';
        $service = new ServiceListener($token, $game->id(), $repository);
        $service->run();
    }

    private function repository($game)
    {
        $repository = $this->getMockBuilder('stdClass')
            ->setMethods(['get', 'save'])
            ->getMock();

        $repository->expects($this->any())
            ->method('get')
            ->willReturn($game);

        $repository->expects($this->any())
            ->method('save')
            ->willReturn(null);

        return $repository;
    }
}
