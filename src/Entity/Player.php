<?php

namespace Jass\Entity;


class Player
{
    public string $name;

    /**
     * @var Card[]
     */
    public ?array $hand = null;

    public ?string $team = null;

    /**
     * @var string[]
     */
    public ?array $strategies = null;

    public function __construct(string $name = 'Ueli')
    {
        $this->name = $name;
    }

    public function __toString() : string
    {
        return $this->name;
    }
}
