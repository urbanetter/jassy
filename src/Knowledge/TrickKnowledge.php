<?php

namespace Jass\Knowledge;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Game;
use Jass\Entity\Trick;
use Jass\Entity\Turn;
use Jass\Hand;
use Jass\Knowledge;
use function Jass\Trick\leadingTurn;
use function Jass\Trick\playedCards;

class TrickKnowledge implements Knowledge
{
    public bool $canLead;

    public ?Suit $leadingSuit;

    public ?Turn $leadingTurn;

    public int $playerOfTurn;

    public bool $leadingTurnInMyTeam;

    /** @var  Card[] */
    public array $playedCards;

    public ?Card $bestCard;

    public bool $isFirst;

    static public function analyze(Game $game)
    {
        $knowledge = new TrickKnowledge();
        $trick = $game->currentTrick ?? new Trick();
        $knowledge->canLead = !($trick->turns);
        $knowledge->leadingSuit = $trick->leadingSuit;
        $knowledge->playerOfTurn = ($knowledge->canLead) ? 1 : count($trick->turns) + 1;
        $knowledge->leadingTurnInMyTeam = ($knowledge->playerOfTurn % 2) == 1;
        $knowledge->playedCards = playedCards($trick);

        $candidates = ($knowledge->leadingSuit) ? Hand\suit($knowledge->playedCards, $knowledge->leadingSuit) : $knowledge->playedCards;
        $knowledge->bestCard = $candidates ? Hand\highest($candidates, $game->style) : null;

        $knowledge->isFirst = count($game->playedTricks) == 0;
        $knowledge->leadingTurn = leadingTurn($trick);

        return $knowledge;
    }
}
