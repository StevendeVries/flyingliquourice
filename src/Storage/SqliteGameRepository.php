<?php

namespace Wecamp\FlyingLiqourice\Storage;

use Wecamp\FlyingLiqourice\Domain\Game;
use Wecamp\FlyingLiqourice\Domain\GameRepository as DomainGameRepository;
use Wecamp\FlyingLiqourice\Domain\Identifier;

final class SqliteGameRepository implements DomainGameRepository
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param Game $game
     */
    public function save(Game $game)
    {
        $stmt = $this->pdo->prepare('INSERT INTO games (id, data) VALUES (:id, :data)');
        $stmt->execute(
            [
                ':id' => (string) $game->id(),
                ':data' => json_encode($game->toArray())
            ]
        );
        $stmt = $this->pdo->prepare('UPDATE games SET data = :data WHERE id = :id');
        $stmt->execute(
            [
                ':id' => (string) $game->id(),
                ':data' => json_encode($game->toArray())
            ]
        );
    }

    /**
     * @param Identifier $identifier
     * @return Game
     */
    public function get(Identifier $identifier)
    {
        $stmt = $this->pdo->prepare('SELECT data FROM games WHERE id = :id');
        $stmt->execute([':id' => (string) $identifier]);
        if (($column = $stmt->fetchColumn()) == null) {
            throw new \InvalidArgumentException('No game found with that identifier. [' . ((string) $identifier) . ']');
        }

        return Game::fromArray(
            json_decode(
                $column,
                true
            )
        );
    }
}
