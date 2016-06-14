<?php

namespace Wecamp\FlyingLiqourice\Domain;

use Rhumsaa\Uuid\Uuid;
use Wecamp\FlyingLiqourice\Domain\Game\Coords;
use Wecamp\FlyingLiqourice\Domain\Game\Grid;
use Wecamp\FlyingLiqourice\Domain\Game\FireResult;
use Wecamp\FlyingLiqourice\Domain\Game\GameIsLockedException;

final class GameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_return_the_status()
    {
        $game   = Game::create();
        $coords = Coords::fromArray(['x' => 1, 'y' => 1]);

        $fireResult = $game->fire($coords);
        $status     = $game->status();

        $this->assertCount(1, $status);
        $this->assertInstanceOf(FireResult::class, $status[0]);
        $this->assertSame($fireResult, $status[0]);
    }

    /**
     * @test
     */
    public function it_wins_the_game()
    {
        $gridData = [
            'fields' => [
                [
                    'coords' => [
                        'x' => 0,
                        'y' => 0
                    ],
                    'shot' => false
                ],
                [
                    'coords' => [
                        'x' => 0,
                        'y' => 1
                    ],
                    'shot' => false
                ]
            ],
            'ships' => [
                [
                    'startPoint' => [
                        'x' => 0,
                        'y' => 0
                    ],
                    'endPoint' => [
                        'x' => 0,
                        'y' => 1
                    ],
                    'hits' => 0
                ]
            ]
        ];

        $data = [
            'width' => 10,
            'height' => 10,
            'fields' => $gridData
        ];

        $gameData = ['id' => Uuid::uuid4()->toString(), 'locked' => false, 'grid' => $data, 'fireResults' => []];
        $game     = Game::fromArray($gameData);
        $result   = $game->fire(Coords::create(0, 0));

        $this->assertEquals('HIT 0.0', (string)$result);

        $result = $game->fire(Coords::create(0, 1));

        $this->assertEquals('WIN 0.1 0.0 0.1', (string)$result);
    }

    /**
     * @test
     */
    public function it_sinks_a_ship()
    {
        $gridData = [
            'fields' => [
                [
                    'coords' => [
                        'x' => 0,
                        'y' => 0
                    ],
                    'shot' => false
                ],
                [
                    'coords' => [
                        'x' => 0,
                        'y' => 1
                    ],
                    'shot' => false
                ],
                [
                    'coords' => [
                        'x' => 1,
                        'y' => 0
                    ],
                    'shot' => false
                ],
                [
                    'coords' => [
                        'x' => 1,
                        'y' => 1
                    ],
                    'shot' => false
                ]
            ],
            'ships' => [
                [
                    'startPoint' => [
                        'x' => 0,
                        'y' => 0
                    ],
                    'endPoint' => [
                        'x' => 0,
                        'y' => 1
                    ],
                    'hits' => 0
                ],
                [
                    'startPoint' => [
                        'x' => 1,
                        'y' => 0
                    ],
                    'endPoint' => [
                        'x' => 1,
                        'y' => 1
                    ],
                    'hits' => 0
                ]
            ]
        ];

        $data = [
            'width' => 10,
            'height' => 10,
            'fields' => $gridData
        ];

        $gameData = ['id' => Uuid::uuid4()->toString(), 'locked' => false, 'grid' => $data, 'fireResults' => []];
        $game     = Game::fromArray($gameData);
        $result   = $game->fire(Coords::create(0, 0));

        $this->assertEquals('HIT 0.0', (string)$result);

        $result = $game->fire(Coords::create(0, 1));

        $this->assertEquals('SANK 0.1 0.0 0.1', (string)$result);
    }

    public function it_should_return_the_fire_results_in_the_correct_order()
    {
        $game = Game::create();

        $fireResults = [
            $game->fire(Coords::fromArray(['x' => 1, 'y' => 1])),
            $game->fire(Coords::fromArray(['x' => 5, 'y' => 5])),
            $game->fire(Coords::fromArray(['x' => 2, 'y' => 2])),
            $game->fire(Coords::fromArray(['x' => 4, 'y' => 4])),
            $game->fire(Coords::fromArray(['x' => 3, 'y' => 3])),
        ];

        $status = $game->status();

        $this->assertSame($fireResults, $status);
    }

    /**
     * @test
     */
    public function it_should_return_the_grid_as_a_string()
    {
        $game = Game::create();

        $this->assertInternalType('string', (string) $game);
        $this->assertNotEmpty((string) $game);
    }

    /**
     * @test
     */
    public function it_should_not_allow_to_fire_in_a_locked_game()
    {
        $game = Game::create();
        $game->surrender();

        $this->setExpectedException(GameIsLockedException::class);
        $game->fire(Coords::create(1, 1));
    }

    /**
     * @test
     */
    public function it_should_win_the_game_when_all_ships_have_sunk()
    {
        $game = Game::create(1, 2, [2]);

        $result = $game->fire(Coords::fromArray(['x' => 0, 'y' => 0]));
        $this->assertFalse($result->isWon());

        $result  = $game->fire(Coords::fromArray(['x' => 0, 'y' => 1]));
        $this->assertTrue($result->isWon());
    }
}
