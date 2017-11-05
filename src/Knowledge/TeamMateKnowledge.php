<?php

namespace Jass\Knowledge;


use Jass\Entity\Game;
use Jass\Knowledge;
use function Jass\Player\isInMyTeam;
use function Jass\Trick\playedCards;

class TeamMateKnowledge implements Knowledge
{
    /** @var string[] */
    public $badSuits = [];

    /** @var string[] */
    public $goodSuits = [];


    static public function analyze(Game $game)
    {
        $knowledge = new TeamMateKnowledge();

        $player = $game->currentPlayer;
        $bock = BockKnowledge::analyze($game);

        foreach ($game->playedTricks as $trick) {
            if (
                $trick->turns[0]->player === $player &&
                $trick->turns[2]->card->suit != $trick->leadingSuit
            ) {
                $knowledge->badSuits[] = $trick->turns[2]->card->suit;
            }
        }

        $playedCards = [];
        foreach ($game->playedTricks as $trick) {
            $startingPlayer = $trick->turns[0]->player;
            $leadingCard = $trick->turns[0]->card;

            if (
                $startingPlayer !== $player &&
                isInMyTeam($player, $startingPlayer) &&
                !$bock->isBockByPlayedCards($leadingCard, $playedCards)
            ) {
                $knowledge->goodSuits[] = $leadingCard->suit;
            }

            $playedCards = array_merge($playedCards, playedCards($trick));
        }

        return $knowledge;
    }
}