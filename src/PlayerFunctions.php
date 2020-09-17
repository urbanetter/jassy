<?php

namespace Jass\Player;

use Jass\Entity\Player;
use LogicException;

/**
 * @param $names
 * @return Player[]
 */
function byNames(string $names) : array
{
    $names = explode(',', $names);
    if (!count($names) == 4) {
        throw new LogicException('4 names separarated by comma needed');
    }

    $result = [];
    foreach ($names as $name) {
        $result[] = new Player(trim($name));
    }

    $team1 = $result[0]->name . " + " . $result[2]->name;
    $result[0]->team = $team1;
    $result[2]->team = $team1;

    $team2 = $result[1]->name . " + " . $result[3]->name;
    $result[1]->team = $team2;
    $result[3]->team = $team2;

    return $result;
}

function isInMyTeam(Player $myself, Player $other) : bool
{
    return $myself->team == $other->team;
}

function nextPlayer(Player $player, array $players) : Player
{
    $index = (int) array_search($player, $players);
    $index = ($index == count($players) - 1) ? 0 : $index + 1;

    return $players[$index];
}