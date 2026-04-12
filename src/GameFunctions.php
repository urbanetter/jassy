<?php

namespace Jass\Game;

use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\last;
use Jass\Message\TestGame;
use Jass\Message\Turn;
use Jass\MessageHandler;
use Jass\Strategy as StrategyContract;
use Jass\Style;
use Jass\Style\BottomUp;
use Jass\Style\TopDown;
use Jass\Style\Trump;
use function Jass\Trick\points;
use function Jass\Trick\addTurn;
use function Jass\Trick\winner;
use function Jass\Player\nextPlayer;

function isFinished(Game $game) : bool
{
    return count($game->playedTricks) == Game::NUMBER_OF_CARDS;
}

function hasStarted(Game $game) : bool
{
    return count($game->playedTricks) > 0 || $game->currentTrick;
}

function isReady(Game $game) : bool
{
    return
        $game->players
        && count($game->players) == Game::NUMBER_OF_PLAYERS
        && $game->players[3] instanceof Player
        && $game->style instanceof Style
        && $game->currentPlayer instanceof Player
    ;
}

/**
 * @param Game $game
 * @return string[] team names
 */
function teams(Game $game): array
{
    return [$game->players[0]->team, $game->players[1]->team];
}

function teamMatched(string $team, Game $game) : bool
{
    if (!isFinished($game)) {
        return false;
    }

    $tricksWon = array_filter($game->playedTricks, function(Trick $trick) use ($team, $game) {
        return winner($trick, $game->style)->team == $team;
    });

    return count($tricksWon) === count($game->playedTricks);
}

function teamWonLastTrick(string $team, Game $game) : bool
{
    if (!isFinished($game)) {
        return false;
    }

    /** @var Trick $lastTrick */
    $lastTrick = last($game->playedTricks);
    return winner($lastTrick, $game->style)->team == $team;
}

function teamPoints(string $team, Game $game) : int
{
    $points = array_reduce($game->playedTricks, function($sum, Trick $trick) use ($game, $team) {
        if (winner($trick, $game->style)->team == $team) {
            $sum += points($trick, $game->style);
        }
        return $sum;
    }, 0);

    // winning last trick adds 5 points
    if (teamWonLastTrick($team, $game)) {
        $points += 5;
    }

    // winning all tricks adds 100 points
    if (teamMatched($team, $game)) {
        $points += 100;
    }

    return $points;
}

/**
 * @param Game $game
 * @return Card[]
 */
function startingHand(Game $game): array
{
    $player = $game->currentPlayer;
    return array_reduce($game->playedTricks, function($cards, Trick $trick) use ($player) {
        foreach ($trick->turns as $turn) {
            if ($turn->player === $player) {
                $cards[] = $turn->card;
            }
        }
        return $cards;
    }, $player->hand);
}

function teamMate(Game $game, Player $player) : ?Player
{
    foreach ($game->players as $candidate) {
        if ($player !== $candidate && $candidate->team === $player->team) {
            return $candidate;
        }
    }
    return null;
}

function playCard(Game $game, Card|string $card) : Game
{
    if (is_string($card)) {
        $card = Card::shortcut($card);
    }

    $turn = new Turn();
    $turn->card = $card;

    $messageHandler = new MessageHandler();

    return $messageHandler->handle($game, $turn);
}

function snapshot(Game $game) : array
{
    $players = [];
    foreach ($game->players as $player) {
        $players[$player->name] = [
            'teamMate' => teamMate($game, $player)?->name,
            'strategies' => array_map(
                fn (string $strategyClass) => strategyName($strategyClass),
                $player->strategies ?? [],
            ),
            'hand' => cardsToShortcuts($player->hand ?? []),
        ];
    }

    return [
        'players' => $players,
        'beginningPlayer' => beginningPlayer($game)?->name,
        'style' => $game->style?->name,
        'playedCards' => cardsToShortcuts(playedCards($game)),
    ];
}

function loadSnapshot(Game $game, array $snapshot) : Game
{
    $game->style = styleByName($snapshot['style']);
    $game->players = array_map(fn (string $name) => new Player($name), array_keys($snapshot['players']));
    foreach ($game->players as $player) {
        $playerSnapshot = $snapshot['players'][$player->name];
        $player->hand = shortcutsToCards($playerSnapshot['hand'] ?? []);
        $player->strategies = array_map(
            fn (string $strategyName) => strategyClass($strategyName),
            $playerSnapshot['strategies'] ?? [],
        );
    }

    foreach ($game->players as $player) {
        $teamMateName = $snapshot['players'][$player->name]['teamMate'] ?? null;
        if (!is_string($teamMateName)) {
            continue;
        }
        $teamMate = playerByName($game, $teamMateName);
        if (!$teamMate) {
            throw new \LogicException('Team mate ' . $teamMateName . ' is not part of the snapshot players.');
        }
        $player->team = teamName($game, $player, $teamMate);
    }

    $beginningPlayer = playerByName($game, $snapshot['beginningPlayer']);
    if (!$beginningPlayer) {
        throw new \LogicException('Beginning player is not part of the snapshot players.');
    }

    $game->playedTricks = [];
    $game->currentTrick = null;
    $game->currentPlayer = $beginningPlayer;

    foreach (shortcutsToCards($snapshot['playedCards']) as $card) {
        $trick = $game->currentTrick = $game->currentTrick ?? new Trick();
        addTurn($trick, $game->currentPlayer, $card);

        if (count($trick->turns) === Game::NUMBER_OF_PLAYERS) {
            $game->currentPlayer = winner($trick, $game->style);
            $game->playedTricks[] = $trick;
            $game->currentTrick = null;
        } else {
            $game->currentPlayer = nextPlayer($game->currentPlayer, $game->players);
        }
    }

    return $game;
}

function testGame($cards = [], $style = null, $strategies = null, $opponentStrategies = null) : Game
{
    $game = new Game();

    $testGame = new TestGame();

    $testGame->cards = $cards;
    $testGame->style = $style;
    $testGame->strategies = $strategies;
    $testGame->opponentStrategies = $opponentStrategies;

    $messageHandler = new MessageHandler();

    return $messageHandler->handle($game, $testGame);
}

function beginningPlayer(Game $game) : ?Player
{
    if ($game->playedTricks) {
        return $game->playedTricks[0]->turns[0]->player ?? null;
    }

    if ($game->currentTrick !== null && count($game->currentTrick->turns) > 0) {
        return $game->currentTrick->turns[0]->player;
    }

    return $game->currentPlayer;
}

/**
 * @return Card[]
 */
function playedCards(Game $game) : array
{
    $cards = [];
    foreach ($game->playedTricks as $trick) {
        foreach ($trick->turns as $turn) {
            $cards[] = $turn->card;
        }
    }
    foreach ($game->currentTrick?->turns ?? [] as $turn) {
        $cards[] = $turn->card;
    }

    return $cards;
}

/**
 * @param Card[] $cards
 * @return string[]
 */
function cardsToShortcuts(array $cards) : array
{
    return array_values(array_map(fn (Card $card) => $card->toShortcut(), $cards));
}

/**
 * @param string[] $shortcuts
 * @return Card[]
 */
function shortcutsToCards(array $shortcuts) : array
{
    return array_map(fn (string $shortcut) => Card::shortcut($shortcut), $shortcuts);
}

function playerByName(Game $game, string $name) : ?Player
{
    foreach ($game->players as $player) {
        if ($player->name === $name) {
            return $player;
        }
    }
    return null;
}

function teamName(Game $game, Player $player, Player $teamMate) : string
{
    $playerIndex = array_search($player, $game->players, true);
    $teamMateIndex = array_search($teamMate, $game->players, true);

    if ($playerIndex < $teamMateIndex) {
        return $player->name . ' + ' . $teamMate->name;
    }

    return $teamMate->name . ' + ' . $player->name;
}

function strategyName(string $strategyClass) : string
{
    if (!is_subclass_of($strategyClass, StrategyContract::class)) {
        throw new \LogicException('Strategy class ' . $strategyClass . ' is not supported.');
    }

    return $strategyClass::name();
}

function strategyClass(string $strategyName) : string
{
    foreach (strategyClasses() as $strategyClass) {
        if ($strategyClass::name() === $strategyName) {
            return $strategyClass;
        }
    }

    throw new \LogicException('Strategy ' . $strategyName . ' is not supported.');
}

/**
 * @return array<class-string<StrategyContract>>
 */
function strategyClasses() : array
{
    return [
        \Jass\Strategy\Bock::class,
        \Jass\Strategy\Simple::class,
        \Jass\Strategy\TeamMate::class,
        \Jass\Strategy\Trump::class,
    ];
}

function styleByName(string $styleName) : Style
{
    return match ($styleName) {
        'top down' => new TopDown(),
        'bottom up' => new BottomUp(),
        default => trumpStyleByName($styleName),
    };
}

function trumpStyleByName(string $styleName) : Trump
{
    $prefix = 'trump ';
    if (!str_starts_with($styleName, $prefix)) {
        throw new \LogicException('Style ' . $styleName . ' is not supported.');
    }

    return new Trump(Suit::fromLabel(substr($styleName, strlen($prefix))));
}
