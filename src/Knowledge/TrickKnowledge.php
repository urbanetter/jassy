<?php

namespace Jass\Knowledge;


use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Entity\Trick;
use Jass\Hand;
use Jass\Knowledge;
use function Jass\Trick\playedCards;

class TrickKnowledge implements Knowledge
{
    /** @var  bool */
    public $canLead;

    /** @var  string */
    public $leadingSuit;

    /** @var  int */
    public $playerOfTurn;

    /** @var  bool */
    public $leadingTurnInMyTeam;

    /** @var  Card[] */
    public $playedCards;

    /** @var  Card */
    public $bestCard;

    static public function analyze(Game $game) : TrickKnowledge
    {
        $knowledge = new TrickKnowledge();
        $trick = $game->currentTrick ?? new Trick();
        $knowledge->canLead = !($trick->turns);
        $knowledge->leadingSuit = $trick->leadingSuit;
        $knowledge->playerOfTurn = ($knowledge->canLead) ? 1 : count($trick->turns) + 1;
        $knowledge->leadingTurnInMyTeam = ($knowledge->playerOfTurn % 2) == 1;
        $knowledge->playedCards = playedCards($trick);
        $knowledge->bestCard = Hand\highest(Hand\suit($knowledge->playedCards, $knowledge->leadingSuit), $game->style->orderFunction());

        return $knowledge;
    }
}