<?php

namespace Jass;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style\Style;

class Intelligence
{
    private $functionChains = [];

    public function registerPlayerIntelligence(Player $player, $strategyNamespace = 'Jass\\Strategy', $abilityNamespace = 'Jass\\Ability')
    {
        $entity = md5($player);
        $functions = ['firstCardOfTrick', 'card'];

        $abilities = [];
        foreach ($player->strategies as $strategyName) {
            $className = $strategyNamespace . '\\' . $strategyName;
            if (!class_exists($className)) {
                throw new \InvalidArgumentException($className . ' is not a valid Strategy');
            }
            foreach ($functions as $function) {
                if (is_callable([$className, $function])) {
                    $this->functionChains[$entity][$function][] = [$className, $function];
                }
            }
            $abilitiesFunction = [$className, 'abilities'];
            if (is_callable($abilitiesFunction)) {
                $abilities = array_merge($abilities, $abilitiesFunction());
            }
        }

        $abilities = array_unique($abilities);
        foreach ($abilities as $abilityName) {
            $className = $abilityNamespace . '\\' . $abilityName;
            if (!class_exists($className)) {
                throw new \InvalidArgumentException($className . ' is not a valid Ability');
            }
            $seeTrick = [$className, 'seeTrick'];
            if (is_callable($seeTrick)) {
                $this->functionChains[$entity]['abilities'][] = $seeTrick;
            }
        }


    }

    public function firstCard(Player $player, Style $style) : Card
    {
        $entity = md5($player);
        $card = $this->first($entity, 'firstCardOfTrick', [$player, $style]);
        if (!$card) {
            throw new \LogicException('Could not figure out first card for player ' . $player);
        }
        return $card;
    }

    public function card(Player $player, Trick $trick, Style $style) : Card
    {
        $entity = md5($player);
        $card = $this->first($entity, 'card', [$player, $trick, $style]);
        if (is_null($card)) {
            throw new \LogicException('Could not figure out next card for player ' . $player);
        }
        return $card;
    }

    public function seeTrick(Player $player, Trick $trick, Style $style)
    {
        $entity = md5($player);
        $this->all($entity, 'abilities', [$player, $trick, $style]);
    }

    protected function all(string $entity, string $chainName, $args)
    {
        foreach ($this->functionChains[$entity][$chainName] as $function) {
            $function(...$args);
        }
    }

    protected function first(string $entity, string $chainName, $args)
    {
        foreach ($this->functionChains[$entity][$chainName] as $function) {
            $result = $function(...$args);
            if (!is_null($result)) {
                return $result;
            }
        }
        return null;
    }
}