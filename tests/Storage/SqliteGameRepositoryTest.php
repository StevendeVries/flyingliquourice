<?php

namespace Wecamp\FlyingLiqourice\Storage;

use Mockery as m;
use Wecamp\FlyingLiqourice\Domain\Game;
use Wecamp\FlyingLiqourice\Domain\GameIdentifier;

final class SqliteGameRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\PDO
     */
    private $pdo;

    /**
     * @var SqliteGameRepository
     */
    private $repository;

    public function setUp()
    {
        $this->pdo        = $this->getMock(MockPDO::class, ['prepare']);
        $this->repository = new SqliteGameRepository($this->pdo);
    }

    /**
     * @test
     */
    public function it_should_save_a_game()
    {
        $game = Game::create();

        $stmt = $this->getMockBuilder('stdClass')
            ->setMethods(['execute'])
            ->getMock();

        $this->pdo->expects($this->at(0))
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO games (id, data) VALUES (:id, :data)'))
            ->willReturn($stmt);

        $stmt->expects($this->any())
            ->method('execute')
            ->with(
                $this->equalTo(
                    [
                        ':id' => (string) $game->id(),
                        ':data' => json_encode($game->toArray())
                    ]
                )
            );

        $this->pdo->expects($this->at(1))
            ->method('prepare')
            ->with($this->equalTo('UPDATE games SET data = :data WHERE id = :id'))
            ->willReturn($stmt);

        $this->repository->save($game);
    }

    /**
     * @test
     */
    public function it_should_get_a_game()
    {
        $game       = Game::create();
        $identifier = $game->id();

        $stmt = $this->getMockBuilder('stdClass')
            ->setMethods(['execute', 'rowCount', 'fetchColumn'])
            ->getMock();

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT data FROM games WHERE id = :id'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo(
                    [
                        ':id' => (string) $identifier
                    ]
                )
            );

        //        $stmt->expects($this->once())
        //            ->method('rowCount')
        //            ->willReturn(1);

        $stmt->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(
                json_encode($game->toArray())
            );

        $result = $this->repository->get($identifier);
        $this->assertInstanceOf(Game::class, $result);
        $this->assertEquals($game->toArray(), $result->toArray());
    }
}

class MockPDO extends \PDO
{
    public function __construct()
    {
    }
}
