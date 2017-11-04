<?php

namespace Jass\Knowledge;


use function Jass\CardSet\suits;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Game;
use function Jass\Game\startingHand;
use function Jass\Hand\suit;
use Jass\Knowledge;
use function Jass\Player\isInMyTeam;

class SuitsKnowledge implements Knowledge
{

    /** @var string[] */
    public $suitsOnlyInMyTeam = [];

    /** @var string[] */
    public $suitsOnlyIHave = [];

    /** @var array string[] */
    public $orderedSuits = [];

    static public function analyze(Game $game)
    {
        $knowledge = new SuitsKnowledge();

        $player = $game->currentPlayer;

        $suitCount = [
            Suit::SHIELD => 0,
            Suit::ROSE => 0,
            Suit::OAK => 0,
            Suit::BELL => 0,
        ];
        foreach ($game->playedTricks as $trick) {
            if (isInMyTeam($player, $trick->turns[0]->player)) {
                if ($trick->turns[1]->card->suit != $trick->leadingSuit &&
                    $trick->turns[2]->card->suit == $trick->leadingSuit &&
                    $trick->turns[3]->card->suit != $trick->leadingSuit) {
                    $knowledge->suitsOnlyInMyTeam[] = $trick->leadingSuit;
                }
            }
            foreach ($trick->turns as $turn) {
                $suit = $turn->card->suit;
                $suitCount[$suit]++;
            }

        }
        $knowledge->suitsOnlyInMyTeam = array_unique($knowledge->suitsOnlyInMyTeam);

        foreach ($suitCount as $suit => $count) {
            if (count(suit($player->hand, $suit)) + $count == Game::NUMBER_OF_CARDS) {
                $knowledge->suitsOnlyIHave[] = $suit;
            }
        }

        $startingHand = startingHand($game);
        foreach (suits() as $suit) {
            $cards = suit($startingHand, $suit);
            if ($cards) {
                $orderSum = array_sum(array_map(function (Card $card) use ($game) {
                    return $game->style->orderValue($card);
                }, $cards));
                $knowledge->orderedSuits[$suit] = $orderSum;
            }
        }
        asort($knowledge->orderedSuits);
        $knowledge->orderedSuits = array_keys(array_reverse($knowledge->orderedSuits));



        return $knowledge;
    }
}