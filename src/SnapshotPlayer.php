<?php

namespace Jass;

class SnapshotPlayer
{
    private ?string $teamMate = null;

    /** @var string[] */
    private array $strategies = [];

    /** @var string[] */
    private array $hand = [];

    public function __construct(
        private readonly string $name,
    ) {
    }

    public function teamMate(string $name) : self
    {
        $this->teamMate = $name;

        return $this;
    }

    public function strategies(string ...$names) : self
    {
        $this->strategies = $names;

        return $this;
    }

    public function hand(string|array $cards) : self
    {
        $this->hand = $this->cardShortcuts($cards);

        return $this;
    }

    public function toArray() : array
    {
        return [
            'teamMate' => $this->teamMate,
            'strategies' => $this->strategies,
            'hand' => $this->hand,
        ];
    }

    public function name() : string
    {
        return $this->name;
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
