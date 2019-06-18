<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Knowledge\BockKnowledge;
use Jass\Knowledge\TrickKnowledge;
use Jass\Knowledge\TrumpKnowledge;
use Jass\Strategy;

class Bock implements Strategy
{
    public function chooseCard(Game $game): ?Card
    {
        $trick = TrickKnowledge::analyze($game);
        if ($trick->canLead) {
            $bock = BockKnowledge::analyze($game);
            $trump = TrumpKnowledge::analyze($game);
            foreach ($bock->bockCards as $card) {
                if (in_array($card, $game->currentPlayer->hand)) {
                    if ($trump->isTrumpGame && !$trump->shouldLeadWithTrump && $card->suit === $trump->suit) {
                        continue;
                    }
                    return $card->withHint('This should be bock.');
                }
            }
        }
        return null;
    }
}