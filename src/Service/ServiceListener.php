<?php

namespace Wecamp\FlyingLiqourice\Service;

use Wecamp\FlyingLiqourice\Domain\Game\Coords;
use Wecamp\FlyingLiqourice\Domain\Game;
use Wecamp\FlyingLiqourice\Domain\GameIdentifier;
use Wecamp\FlyingLiqourice\Storage\SqliteGameRepository;

class ServiceListener
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $id;

    /**
     * @var SqliteGameRepository
     */
    private $repository;

    public function __construct($token, $id = '', $repository)
    {
        $this->token = strtolower($token);

        $this->id = $id;

        $this->repository = $repository;
    }

    /**
     * Run the command in the token
     * @return string
     */
    public function run()
    {
        if (!empty($this->id)) {
            $identifier = GameIdentifier::fromString($this->id());
            $game       = $this->repository()->get($identifier);

            echo 'Re initializing game id: ' . $game->id() . PHP_EOL;
        }
        $tokenized = explode(' ', $this->token);

        $command  = trim($tokenized[0]);
        $argument = '';
        if (count($tokenized) == 2) {
            $argument = trim($tokenized[1]);
        }

        if (!in_array($command, ['start', 'resume', 'f', 'fire', 'status', 'surrender', 'field', 'help'])) {
            throw new Game\InvalidCommandException('Wrong command given');
        }

        return $this->$command($argument);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return SqliteGameRepository
     */
    private function repository()
    {
        return $this->repository;
    }

    /**
     * @param string $id
     * @return string
     */
    private function start($size = '')
    {
        if (strlen($size) !== 0) {
            $size = explode(':', $size);
            $game = Game::create((int) $size[0], (int) $size[1]);
        } else {
            $game = Game::create();
            echo 'New game started: ' . $game->id() . PHP_EOL;
        }

        $this->id = $game->id();
        $this->repository()->save($game);

        echo (string) $game;
        return 'STARTED ' . $game->id();
    }

    /**
     * @param string $id
     * @return string
     */
    private function resume($id = '')
    {
        $identifier = GameIdentifier::fromString($id);
        $game       = $this->repository()->get($identifier);
        echo 'Game resumed: ' . $game->id() . PHP_EOL;

        $this->id = $game->id();
        $this->repository()->save($game);

        echo (string) $game;
        return 'RESUMED ' . $game->id();
    }

    /**
     * Get the status of the current game
     * @return string
     */
    private function status()
    {
        $identifier = GameIdentifier::fromString($this->id());
        $game       = $this->repository()->get($identifier);

        echo 'Get game status: ' . $game->id() . PHP_EOL;
        echo (string) $game;

        $result = '';
        $status = $game->status();
        foreach ($status as $fireResult) {
            $result .= '- ' . $fireResult . PHP_EOL;
        }

        return 'STATUS' . PHP_EOL
            . '- SCORE ' . $game->score() . PHP_EOL
            . $result;
    }

    /**
     * Quit the game
     * @return string
     */
    private function surrender()
    {
        $identifier = GameIdentifier::fromString($this->id());
        $game       = $this->repository()->get($identifier);

        $game->surrender();
        $this->repository()->save($game);

        echo 'Surrendering game ' . $game->id() . PHP_EOL;
        echo (string) $game;

        $result = 'SURRENDERED';
        foreach ($game->ships() as $ship) {
            $result .= '- SHIP ' . $ship->startPoint() . ' ' . $ship->endPoint() . PHP_EOL;
        }
        return $result;
    }

    /**
     * Shortcut for fire
     * @param $location
     * @return string
     */
    private function f($location)
    {
        return $this->fire($location);
    }

    /**
     * fire on $location
     * @param string $location
     * @return string
     */
    private function fire($location)
    {
        $identifier = GameIdentifier::fromString($this->id());
        $game       = $this->repository()->get($identifier);

        $coordElements = explode('.', $location);
        $coords        = Coords::create((int) $coordElements[0], (int) $coordElements[1]);
        $result        = $game->fire($coords);

        $this->repository()->save($game);

        echo 'Firing on ' . $location . ' in game: ' . $game->id() . PHP_EOL;
        echo (string) $game;
        return $result;
    }

    private function field()
    {
        $identifier = GameIdentifier::fromString($this->id());
        $game       = $this->repository()->get($identifier);

        $this->repository()->save($game);
        $gameString = (string) $game;
        $gameString = str_replace(['=', 'v', '^', '<', '>', 'ǁ'], ' ', $gameString);
        return 'FIELD ' . PHP_EOL . $gameString . PHP_EOL; // . PHP_EOL . $game;
    }

    private function help()
    {
        $help = 'Battleship commands:' . PHP_EOL;
        $help .= '' . PHP_EOL;
        $help .= 'START [X:Y]   | ' . 'Start a game, optional give the X and Y size, defaults to 10x10' . PHP_EOL;
        $help .= 'RESUME <ID>   | ' . 'Restart a game with the given ID' . PHP_EOL;
        $help .= 'STATUS        | ' . 'Show the status of the game' . PHP_EOL;
        $help .= 'FIRE <X.Y>    | ' . 'Fire on the given coords' . PHP_EOL;
        $help .= 'FIELD         | ' . 'Show the current field with all shots on it' . PHP_EOL;
        $help .= 'SURRENDER     | ' . 'Give up the game and lose' . PHP_EOL;

        return $help;
    }
}
