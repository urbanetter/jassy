<?php

namespace Jass\Strategy;

use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Strategy;
use Jass\Style\Style;

/**
 * @param string[] $classNames
 * @return Strategy[]
 */
function strategyClasses($classNames)
{
    return array_map(function ($className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Unknown class ' . $className);
        }
        /** @var Strategy $strategy */
        $strategy = new $className();
        if (!$strategy instanceof Strategy) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not implement Jass\Strategy, but it must!');
        }

        return $strategy;
    }, $classNames);
}

/**
 * @param string[] $classNames
 * @return Ability[]
 */
function abilityClasses($classNames)
{
    return array_map(function ($className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Unknown class ' . $className);
        }
        /** @var Ability $ability */
        $ability = new $className();
        if (!$ability instanceof Ability) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not implement Jass\Ability, but it must!');
        }

        return $ability;
    }, $classNames);
}

function firstCardOfTrick(Player $player, Style $style)
{
    foreach (strategyClasses($player->strategies) as $strategy) {
        if (($card = $strategy->firstCardOfTrick($player, $style)) !== null) {
            return $card;
        }
    }
    throw new \LogicException('Could not figure out first card for player ' . $player);
}

function card(Player $player, Trick $trick, Style $style)
{
    foreach (strategyClasses($player->strategies) as $strategy) {
        if ($card = $strategy->card($player, $trick, $style) !== null) {
            return $card;
        }
    }
    throw new \LogicException('Could not figure out next card for player ' . $player);
}

function seeTrick(Player $player, Trick $trick, Style $style)
{
    $strategies = strategyClasses($player->strategies);
    $abilityNames = array_reduce($strategies, function($abilities, Strategy $strategy) {
        return array_merge($abilities, $strategy->abilities());
    }, []);
    $abilityNames = array_unique($abilityNames);
    foreach (abilityClasses($abilityNames) as $ability) {
        $ability->seeTrick($player, $trick, $style);
    }
}
