<?php

namespace Jass\Knowledge;

use Jass\Entity\Game;
use Jass\Knowledge;
use Jass\Entity\Card;
use function Jass\CardSet\bySuit;
use function Jass\CardSet\suits;
use function Jass\Hand\highest;
use function Jass\Hand\suit;
use function Jass\Trick\playedCards;


class BockKnowledge implements Knowledge
{
    /** @var  Card[] */
    public $bockCards = [];

    static public function analyze(Game $game)
    {
        $knowledge = new BockKnowledge();

        $playedCards = [];
        foreach ($game->playedTricks as $trick) {
            $playedCards = array_merge($playedCards, playedCards($trick));
        }

        foreach (suits() as $suit) {
            $allCards = bySuit($suit);
            $notPlayed = array_diff($allCards, suit($playedCards, $suit));
            $knowledge->bockCards[$suit] = highest($notPlayed, $game->style->orderFunction());
        }

        return $knowledge;
    }


}