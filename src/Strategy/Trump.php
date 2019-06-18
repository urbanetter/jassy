<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Game;
use function Jass\Hand\highest;
use function Jass\Hand\suit;
use Jass\Knowledge\BockKnowledge;
use Jass\Knowledge\TeamMateKnowledge;
use Jass\Knowledge\TrickKnowledge;
use Jass\Knowledge\TrumpKnowledge;
use Jass\Strategy;

class Trump implements Strategy
{

    public function chooseCard(Game $game): ?Card
    {
        $trump = TrumpKnowledge::analyze($game);
        if (!$trump->isTrumpGame) {
            return null;
        }

        $player = $game->currentPlayer;
        $trick = TrickKnowledge::analyze($game);

        if ($trick->canLead) {
            if ($trump->shouldLeadWithTrump) {
                return highest(suit($player->hand, $trump->suit), $game->style->orderFunction());
            }
            return null;
        } else {
            $teamMate = TeamMateKnowledge::analyze($game);
            if ($trick->isFirst) {
                if (
                    $trick->leadingTurn &&
                    $trick->leadingTurn->card->suit === $trump->suit &&
                    $trick->leadingTurn->player === $teamMate->player &&
                    $game->style->orderValue($trick->leadingTurn->card) < $game->style->orderValue(Card::from($trump->suit, Card\Value::ACE))
                ) {
                    $myHighest = highest($trump->hand, $game->style->orderFunction());
                    if ($myHighest && $game->style->orderValue($myHighest) > $game->style->orderValue($trick->leadingTurn->card)) {
                        return $myHighest;
                    }
                }
                return null;
            }

            // if we can still win the match, try to win the trick
            $bock = BockKnowledge::analyze($game);
            if (
                $trump->possibleMatch && // this also means that our team mate was playing the leading turn
                $trick->leadingTurn && !in_array($trick->leadingTurn->card, $bock->bockCards) &&
                $trump->hand
            ) {
                return highest($trump->hand, $game->style->orderFunction());
            }

            // if we have a bock card after winning this we try to win the trick
            $bockCards = array_intersect($bock->bockWithTrick($game->currentTrick), $player->hand);
            if (
                $trump->hand &&
                $bockCards &&
                $trick->leadingTurn &&
                !(
                    $trick->leadingTurn->player === $teamMate->player &&
                    in_array($trick->leadingTurn->card, $bock->bockCards)
                )
            ) {
                return highest($trump->hand, $game->style->orderFunction());
            }

            return null;
        }
    }
}