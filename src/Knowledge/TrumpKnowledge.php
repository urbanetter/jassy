<?php

namespace Jass\Knowledge;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Game;
use Jass\Entity\Trick;
use function Jass\Hand\suit;
use Jass\Knowledge;
use Jass\Style\Trump;
use function Jass\Trick\playedCards;
use function Jass\Trick\winner;

class TrumpKnowledge implements Knowledge
{
    public bool $isTrumpGame;

    public string $choosingTeam;

    /** @var Card[] */
    public array $played = [];

    public bool $shouldLeadWithTrump;

    public Suit $suit;

    /** @var Card[] */
    public array $hand;

    public bool $possibleMatch;

    static public function analyze(Game $game)
    {
        $knowledge = new TrumpKnowledge();

        $knowledge->isTrumpGame = $game->style instanceof Trump;

        $firstTrick = $game->playedTricks[0] ?? $game->currentTrick ?? new Trick();
        $knowledge->choosingTeam = $game->currentPlayer->team;
        if ($firstTrick->turns) {
            $knowledge->choosingTeam = $firstTrick->turns[0]->player->team;
        }

        if (!$game->style instanceof Trump) {
            return $knowledge;
        }
        $player = $game->currentPlayer;
        $trumpSuit = $knowledge->suit = $game->style->trumpSuit;
        $trumpHand = $knowledge->hand = array_values(suit($player->hand, $trumpSuit));
        $trumpPlayed = [];

        $suits = SuitsKnowledge::analyze($game);

        foreach ($game->playedTricks as $trick) {
            $trumpPlayed = array_merge($trumpPlayed, suit(playedCards($trick), $trumpSuit));
        }
        $knowledge->played = $trumpPlayed;

        if (
            count($trumpPlayed) + count($trumpHand) >= (Game::NUMBER_OF_CARDS - 1) || // if only one trump is left we try our luck
            in_array($trumpSuit, $suits->suitsOnlyInMyTeam) ||
            in_array($trumpSuit, $suits->suitsOnlyIHave)
        ) {
            $knowledge->shouldLeadWithTrump = false;
        } else {
            $knowledge->shouldLeadWithTrump = true;
        }

        $knowledge->possibleMatch = true;
        foreach ($game->playedTricks as $trick) {
            if (winner($trick, $game->style->orderFunction())->team != $player->team) {
                $knowledge->possibleMatch = false;
            }
        }

        return $knowledge;
    }
}