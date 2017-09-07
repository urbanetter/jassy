<?php

namespace Jass\Entity;


class Player
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Card[]
     */
    public $hand;

    /**
     * @var string
     */
    public $team;

    /**
     * @var array
     */
    public $brain;

    /**
     * @var Ability[]
     */
    public $abilities;

    /**
     * @var Strategy[]
     */
    public $strategies;

    /**
     * @param string $name
     * @param string $team
     */
    public function __construct($name = 'Ueli', $team = 'Team Ueli')
    {
        $this->name = $name;
        $this->team = $team;
    }

    public function __toString()
    {
        return $this->name;
    }
}