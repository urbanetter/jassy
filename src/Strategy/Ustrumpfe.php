<?php

namespace Jass\Strategy;


use Jass\Ability\TeamOnlySuits;
use Jass\Ability\TrickCounter;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\highest;
use function Jass\Hand\suit;
use Jass\Strategy;
use Jass\Style;

class Ustrumpfe implements Strategy
{

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        if (!$style instanceof Style\Trump) {
            throw new \InvalidArgumentException('Strategy only makes sense with style "trump".');
        }
        
        $trickCount = $player->brain[TrickCounter::TRICK_COUNTER] ?? 1;
        $choosingTeam = $player->brain[TrickCounter::CHOOSING_TEAM] ?? false;

        if ($trickCount == 1 || $choosingTeam) {
            if (!in_array($style->trumpSuit, $player->brain[TeamOnlySuits::TEAM_ONLY_SUITS])) {
                return highest(suit($player->hand, $style->trumpSuit), $style->orderFunction());
            }
        }

        return null;
    }

    public static function card(Player $player, Trick $trick, Style $style): ?Card
    {
        return null;
    }

    public static function abilities()
    {
        return [TeamOnlySuits::class, TrickCounter::class];
    }
}