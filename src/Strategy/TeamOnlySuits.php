<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\canFollowSuit;
use function Jass\Hand\lowest;
use function Jass\Hand\suit;
use Jass\Strategy;
use Jass\Style;
use Jass\Ability\TeamOnlySuits as Ability;

class TeamOnlySuits implements Strategy
{

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        $teamOnlySuits = $player->brain[Ability::TEAM_ONLY_SUITS] ?? [];
        foreach ($teamOnlySuits as $suit) {
            if (canFollowSuit($player->hand, $suit)) {
                return lowest(suit($player->hand, $suit), $style->orderFunction());
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
        return [Ability::class];
    }
}