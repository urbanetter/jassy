<?php

namespace Jass;

use Closure;

class Snapshot
{
    private array $players = [];

    private ?string $beginningPlayer = null;

    private ?string $style = null;

    /** @var string[] */
    private array $playedCards = [];

    public static function new() : self
    {
        return new self();
    }

    public function player(string $name, Closure $setup) : self
    {
        $player = new SnapshotPlayer($name);
        $setup($player);

        $this->players[$name] = $player->toArray();

        return $this;
    }

    public function beginningPlayer(string $name) : self
    {
        $this->beginningPlayer = $name;

        return $this;
    }

    public function style(string $name) : self
    {
        $this->style = $name;

        return $this;
    }

    public function playedCards(string|array $cards) : self
    {
        $this->playedCards = $this->cardShortcuts($cards);

        return $this;
    }

    public function toArray() : array
    {
        return [
            'players' => $this->players,
            'beginningPlayer' => $this->beginningPlayer,
            'style' => $this->style,
            'playedCards' => $this->playedCards,
        ];
    }

    private function cardShortcuts(string|array $cards) : array
    {
        if (is_array($cards)) {
            return array_values($cards);
        }

        if (trim($cards) === '') {
            return [];
        }

        return array_map(fn (string $card) => trim($card), explode(',', $cards));
    }
}
