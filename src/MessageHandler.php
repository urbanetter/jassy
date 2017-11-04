<?php

namespace Jass;

use function Jass\CardSet\jassSet;
use Jass\Entity\Card;
use Jass\Entity\Game as GameEntity;
use Jass\Entity\Trick as TrickEntity;
use function Jass\Hand\first;
use Jass\Message\Deal;
use Jass\Message\Message;
use Jass\Message\PlayerSetup;
use Jass\Message\StyleSetup;
use Jass\Message\TestGame;
use Jass\Message\Turn;
use function Jass\Player\byNames;
use Jass\Strategy\Simple;
use Jass\Style\TopDown;
use Jass\Trick;
use function Jass\Player\nextPlayer;


class MessageHandler
{
    private $messages = [
        PlayerSetup::class => "playerSetup",
        StyleSetup::class => "styleSetup",
        Deal::class => "deal",
        Turn::class => "turn",
        TestGame::class => "testGame"
    ];

    public function handle(GameEntity $game, Message $message) : GameEntity
    {
        $className = get_class($message);

        if (!isset($this->messages[$className])) {
            throw new \InvalidArgumentException("Message $className not supported");
        }

        $functionName = $this->messages[$className];
        return $this->$functionName($game, $message);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function playerSetup(GameEntity $game, PlayerSetup $message) : GameEntity
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function styleSetup(GameEntity $game, StyleSetup $message) : GameEntity
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function deal(GameEntity $game, Deal $message) : GameEntity
    {
        if (\Jass\Game\hasStarted($game)) {
            throw new \LogicException('Not allowed to deal cards when game has already started');
        }

        if ($game->players && count($game->players) != GameEntity::NUMBER_OF_PLAYERS) {
            throw new \LogicException('Game not ready to deal. Set players before.');
        }

        $cards = $message->cards;

        foreach ($game->players as $player) {
            $player->hand = array_splice($cards, 0, GameEntity::NUMBER_OF_CARDS);
        }

        return $game;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function turn(GameEntity $game, Turn $turn) : GameEntity
    {
        if (!\Jass\Game\isReady($game)) {
            throw new \LogicException('Game is not ready to get stared.');
        }

        if (\Jass\Game\isFinished($game)) {
            throw new \LogicException('Game is already finished');
        }

        $player = $game->currentPlayer;
        $card = $turn->card;
        $trick = $game->currentTrick = $game->currentTrick ?? new TrickEntity();

        if (!in_array($card, $player->hand)) {
            throw new \LogicException('Card ' . $card . ' is not in hand of player ' . $player);
        }

        if (!$game->style->isValidCard($trick, $player->hand, $card)) {
            throw new \LogicException('Card ' . $card . ' not allowed in game style ' . $game->style);
        }

        // remove card from hand from player
        $index = array_search($card, $player->hand);
        unset($player->hand[$index]);

        // add turn to trick
        $trick = Trick\addTurn($trick, $player, $card);

        // check if trick is finished
        if (Trick\isFinished($trick)) {
            $game->currentPlayer = Trick\winner($trick, $game->style->orderFunction());
            $game->playedTricks[] = $trick;
            $game->currentTrick = null;
        } else {
            $game->currentPlayer = nextPlayer($game->currentPlayer, $game->players);
        }

        return $game;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function testGame(GameEntity $game, TestGame $testGame) : GameEntity
    {
        $game->players = byNames('Ueli, Fritz, Franz, Hans');
        $game->style = $testGame->style ?? new TopDown();
        $game->players[0]->strategies = $testGame->strategies ?? [Simple::class];
        $game->players[1]->strategies = $testGame->opponentStrategies ?? [Simple::class];
        $game->players[2]->strategies = $testGame->strategies ?? [Simple::class];
        $game->players[3]->strategies = $testGame->opponentStrategies ?? [Simple::class];

        $cards = $testGame->cards;
        if (first($cards) instanceof Card) {
            $cards = [0 => $cards];
        }

        $usedCards = [];
        foreach ($cards as $card) {
            $usedCards = array_merge($usedCards, $card);
        }

        $allCards = jassSet();
        $unusedCards = array_diff($allCards, $usedCards);
        shuffle($unusedCards);

        foreach ($game->players as $id => $player) {
            $player->hand = $cards[$id] ?? [];
            $missingCards = GameEntity::NUMBER_OF_CARDS - count($player->hand);
            if ($missingCards) {
                $player->hand = array_merge($player->hand, array_splice($unusedCards, 0, $missingCards));
            }
        }

        $game->currentPlayer = $game->players[0];

        return $game;
    }

}