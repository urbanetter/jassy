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
     * @var string[]
     */
    public $strategies;

    /**
     * @param string $name
     */
    public function __construct($name = 'Ueli')
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}