<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Knowledge\SuitsKnowledge;
use Jass\Strategy;
use Jass\Hand;

class TeamOnlySuits implements Strategy
{
    public function chooseCard(Game $game): ?Card
    {
        $suits = SuitsKnowledge::analyze($game)->suitsOnlyInMyTeam;
        $hand = $game->currentPlayer->hand;
        foreach ($suits as $suit) {
            if (Hand\canFollowSuit($hand, $suit)) {
                return Hand\lowest($hand, $game->style->orderFunction());
            }
        }
        return null;
    }
}