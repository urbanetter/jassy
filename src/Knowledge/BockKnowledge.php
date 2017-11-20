<?php

namespace Jass\Knowledge;

use Jass\Entity\Game;
use function Jass\CardSet\bySuit;
use function Jass\CardSet\suits;
use Jass\Entity\Trick;
use function Jass\Hand\highest;
use function Jass\Hand\ordered;
use function Jass\Hand\suit;
use function Jass\Trick\playedCards;
use Jass\Knowledge;
use Jass\Entity\Card;


class BockKnowledge implements Knowledge
{
    /** @var  Card[] */
    public $bockCards = [];

    /** @var int[] how many cards need to be played until there is a bock card */
    public $suitPotential = [];

    private $orderFunction;

    private $playedCards = [];

    static public function analyze(Game $game)
    {
        $knowledge = new BockKnowledge();

        $playedCards = [];
        foreach ($game->playedTricks as $trick) {
            $playedCards = array_merge($playedCards, playedCards($trick));
        }
        $knowledge->playedCards = $playedCards;

        foreach (suits() as $suit) {
            $allCards = bySuit($suit);
            $notPlayed = array_diff($allCards, suit($playedCards, $suit));

            $knowledge->bockCards[$suit] = highest($notPlayed, $game->style->orderFunction());

            $handCards = suit($game->currentPlayer->hand, $suit);
            if ($handCards) {
                $highestHandCard = highest($handCards, $game->style->orderFunction());
                $byNeededCards = array_reverse(ordered($notPlayed, $game->style->orderFunction()));
                $knowledge->suitPotential[$suit] = array_search($highestHandCard, $byNeededCards);
            }
        }

        asort($knowledge->suitPotential);

        $knowledge->orderFunction = $game->style->orderFunction();

        return $knowledge;
    }

    /**
     * @param Card $candidate
     * @param Card[] $playedCards
     * @return bool
     */
    public function isBockByPlayedCards(Card $candidate, $playedCards)
    {
        $allCards = bySuit($candidate->suit);
        $notPlayed = array_diff($allCards, suit($playedCards, $candidate->suit));

        return highest($notPlayed, $this->orderFunction) === $candidate;
    }

    public function bockWithTrick(Trick $trick)
    {
        $playedCards = array_merge($this->playedCards, playedCards($trick));

        $result = [];
        foreach (suits() as $suit) {
            $allCards = bySuit($suit);
            $notPlayed = array_diff($allCards, suit($playedCards, $suit));

            $result[$suit] = highest($notPlayed, $this->orderFunction);
        }
        return $result;
    }
}