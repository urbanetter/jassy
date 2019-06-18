<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Knowledge\TrickKnowledge;
use Jass\Strategy;
use Jass\Hand;

class Simple implements Strategy
{
    public function chooseCard(Game $game): ?Card
    {
        $trick = TrickKnowledge::analyze($game);
        $player = $game->currentPlayer;
        if ($trick->canLead) {
            $card = Hand\highest($player->hand, $game->style->orderFunction())->withHint('Just my highest card.');
        } else {
            if (Hand\canFollowSuit($player->hand, $trick->leadingSuit)) {
                $card = Hand\highest(Hand\suit($player->hand, $trick->leadingSuit), $game->style->orderFunction());
                if ($game->style->orderValue($trick->bestCard) > $game->style->orderValue($card)) {
                    $card =  Hand\lowest(Hand\suit($player->hand, $trick->leadingSuit), $game->style->orderFunction())->withHint('Lowest card of this suit.');
                }
            } else {
                $card = Hand\lowest($player->hand, $game->style->orderFunction())->withHint('Lowest card');
            }

        }
        return $card;
    }
}