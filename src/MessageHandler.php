<?php

namespace Jass;

use Jass\Entity\Game;
use Jass\Entity\Trick;
use Jass\Message\Deal;
use Jass\Message\Message;
use Jass\Message\PlayerSetup;
use Jass\Message\StyleSetup;
use Jass\Message\Turn;
use function Jass\Player\nextPlayer;
use function Jass\Strategy\seeTrick;
use function Jass\Trick\addTurn;
use function Jass\Trick\winner;


class MessageHandler
{
    private $messages = [
        PlayerSetup::class => "playerSetup",
        StyleSetup::class => "styleSetup",
        Deal::class => "deal",
        Turn::class => "turn",
    ];

    public function handle(Game $game, Message $message) : Game
    {
        $className = get_class($message);

        if (!isset($this->messages[$className])) {
            throw new \InvalidArgumentException("Message $className not supported");
        }

        $functionName = $this->messages[$className];
        return $this->$functionName($game, $message);
    }

    public function playerSetup(Game $game, PlayerSetup $message)
    {
        if (\Jass\Game\hasStarted($game)) {
            throw new \LogicException('Not allowed to change player when game has already started');
        }

        $game->players = $message->players;

        if ($message->starter && in_array($message->starter, $message->players)) {
            $game->currentPlayer = $message->starter;
        } else {
            $game->currentPlayer = $game->players[array_rand($game->players)];
        }

        return $game;
    }

    public function styleSetup(Game $game, StyleSetup $message)
    {
        if (\Jass\Game\hasStarted($game)) {
            throw new \LogicException('Not allowed to change style when game has already started');
        }

        if ($message->style instanceof Style) {
            $game->style = $message->style;
        }

        if ($message->strategies) {
            foreach ($game->players as $i => $player) {
                if (isset($message->strategies[$i])) {
                    $game->players[$i]->strategies = $message->strategies[$i];
                }
            }
        }

        return $game;
    }

    public function deal(Game $game, Deal $message)
    {
        if (\Jass\Game\hasStarted($game)) {
            throw new \LogicException('Not allowed to deal cards when game has already started');
        }

        if ($game->players && count($game->players) != Game::NUMBER_OF_PLAYERS) {
            throw new \LogicException('Game not ready to deal. Set players before.');
        }

        $cards = $message->cards;

        foreach ($game->players as $player) {
            $player->hand = array_splice($cards, 0, Game::NUMBER_OF_CARDS);
        }

        return $game;
    }

    public function turn(Game $game, Turn $turn)
    {
        if (!\Jass\Game\isReady($game)) {
            throw new \LogicException('Game is not ready to get stared.');
        }

        if (\Jass\Game\isFinished($game)) {
            throw new \LogicException('Game is already finished');
        }

        $player = $game->currentPlayer;
        $card = $turn->card;
        $trick = ($game->currentTrick) ? $game->currentTrick : new Trick();

        if (!in_array($card, $player->hand)) {
            throw new \LogicException('Card' . $card . ' is not in hand of player ' . $player);
        }

        if (!$game->style->isValidCard($trick, $player->hand, $card)) {
            throw new \LogicException('Card ' . $card . ' not allowed in game style ' . $game->style);
        }

        // remove card from hand from player
        $index = array_search($card, $player->hand);
        unset($player->hand[$index]);

        // add turn to trick
        $trick = addTurn($trick, $player, $card);

        // check if trick is finished
        if (\Jass\Trick\isFinished($trick)) {
            foreach ($game->players as $player) {
                seeTrick($player, $trick, $game->style);
            }
            $game->currentPlayer = winner($trick, $game->style->orderFunction());
            $game->playedTricks[] = $trick;
            $game->currentTrick = null;
        } else {
            $game->currentPlayer = nextPlayer($game->currentPlayer, $game->players);
        }

        return $game;
    }

}