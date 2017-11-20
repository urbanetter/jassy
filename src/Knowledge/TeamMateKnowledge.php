<?php

namespace Jass\Knowledge;


use Jass\Entity\Game;
use function Jass\Game\teamMate;
use Jass\Knowledge;
use function Jass\Player\isInMyTeam;
use function Jass\Trick\leadingTurn;
use function Jass\Trick\playedCards;
use function Jass\Trick\playerTurn;

class TeamMateKnowledge implements Knowledge
{
    /** @var string[] */
    public $badSuits = [];

    /** @var string[] */
    public $goodSuits = [];

    /** @var string[] */
    public $tossedSuits = [];

    static public function analyze(Game $game)
    {
        $knowledge = new TeamMateKnowledge();

        $player = $game->currentPlayer;
        $bock = BockKnowledge::analyze($game);
        $teamMate = teamMate($game, $player);

        // no card of a suit = bad suit
        foreach ($game->playedTricks as $trick) {
            $teamMateTurn = playerTurn($trick, $teamMate);
            if (
                $teamMateTurn->card->suit !== $trick->leadingSuit
            ) {
                $knowledge->badSuits[] = $trick->leadingSuit;
            }
        }

        // "azieh"
        $playedCards = [];
        foreach ($game->playedTricks as $trick) {
            $leadingTurn = leadingTurn($trick);

            if (
                $leadingTurn->player !== $player &&
                isInMyTeam($player, $leadingTurn->player) &&
                !$bock->isBockByPlayedCards($leadingTurn->card, $playedCards)
            ) {
                $knowledge->goodSuits[] = $leadingTurn->card->suit;
            }

            $playedCards = array_merge($playedCards, playedCards($trick));
        }

        // "verrüere"
        foreach ($game->playedTricks as $trick) {
            $leadingTurn = leadingTurn($trick);
            if ($leadingTurn->player !== $player) {
                continue;
            }
            $teamMateTurn = playerTurn($trick, $teamMate);
            if ($teamMateTurn->card->suit === $trick->leadingSuit) {
                continue;
            }
            if (in_array($teamMateTurn->card->suit, $knowledge->tossedSuits)) {
                continue;
            }
            $knowledge->tossedSuits[] = $teamMateTurn->card->suit;
        }

        return $knowledge;
    }
}