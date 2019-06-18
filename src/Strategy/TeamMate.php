<?php

namespace Jass\Strategy;

use Jass\Entity\Card;
use Jass\Entity\Game;
use function Jass\Hand\canFollowSuit;
use function Jass\Hand\first;
use function Jass\Hand\last;
use function Jass\Hand\lowest;
use function Jass\Hand\suit;
use function Jass\Hand\suits;
use Jass\Knowledge\BockKnowledge;
use Jass\Knowledge\SuitsKnowledge;
use Jass\Knowledge\TeamMateKnowledge;
use Jass\Knowledge\TrickKnowledge;
use Jass\Strategy;

class TeamMate implements Strategy
{

    public function chooseCard(Game $game): ?Card
    {
        $trick = TrickKnowledge::analyze($game);
        $suits = SuitsKnowledge::analyze($game);
        $bock = BockKnowledge::analyze($game);
        $teamMate = TeamMateKnowledge::analyze($game);

        $player = $game->currentPlayer;
        $allSuits = \Jass\CardSet\suits();
        $handSuits = suits($player->hand);
        if ($trick->canLead) {
            if ($teamMate->goodSuits) {
                $suitsToPlay = array_intersect($handSuits, $teamMate->goodSuits);
                if (count($suitsToPlay) === 1) {
                    $suit = first($suitsToPlay);
                    return lowest(suit($player->hand, $suit), $game->style->orderFunction())->withHint('I think my teammate wanted this suit.');
                }
            }
            if ($teamMate->badSuits || $teamMate->tossedSuits) {
                $suitsMightBeGood = array_diff($allSuits, $teamMate->badSuits, $teamMate->tossedSuits);
                $suitsToPlay = array_intersect($handSuits, $suitsMightBeGood);
                if (count($suitsToPlay) === 1) {
                    $suit = first($suitsToPlay);
                    return lowest(suit($player->hand, $suit), $game->style->orderFunction())->withHint('My teammate tossed all suits but this one.');
                }
            }
            if ($suits->suitsOnlyInMyTeam) {
                $suitsToPlay = array_intersect($handSuits, $suits->suitsOnlyInMyTeam);
                if (count($suitsToPlay) === 1) {
                    $suit = first($suitsToPlay);
                    return lowest(suit($player->hand, $suit), $game->style->orderFunction())->withHint('I think only my teammate and I have this suit.');
                }
            }
            // show best suit as good suit to team mate
            if ($bock->suitPotential) {
                $suit = first(array_keys($bock->suitPotential));
                return lowest(suit($player->hand, $suit), $game->style->orderFunction())->withHint('I show to my teammate that this is a good suite.');
            }
        } else {
            if (!canFollowSuit($player->hand, $trick->leadingSuit) && $bock->suitPotential) {
                $suit = last(array_keys($bock->suitPotential));
                return lowest(suit($player->hand, $suit), $game->style->orderFunction())->withHint('I toss this suit.');
            }

        }

        return null;
    }
}