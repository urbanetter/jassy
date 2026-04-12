<?php

namespace Tests;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Game;
use function Jass\Game\loadSnapshot;
use function Jass\Game\playCard;
use function Jass\Game\snapshot;
use function Jass\Game\testGame;
use Jass\Strategy\Bock;
use Jass\Strategy\Simple;
use PHPUnit\Framework\TestCase;

class GameFunctionsTest extends TestCase
{
    public function testPlayCard()
    {
        $player1Cards = byShortcuts('sk,sq,o6,b6,b7,b8,b9,b10,bj');
        $player2Cards = byShortcuts('sa,ra,rk,rq,rj,r10,r9,r8,r7');

        $game = testGame([
            $player1Cards,
            $player2Cards,
            byShortcuts('o7'),
            byShortcuts('o8'),
        ]);

        $this->assertEquals($player1Cards, $game->currentPlayer->hand);
        $game = playCard($game, 'o6');

        $this->assertEquals($player2Cards, $game->currentPlayer->hand);

        $this->expectException(\LogicException::class);
        playCard($game, 'sk');

    }

    public function testSnapshot()
    {
        $game = testGame(
            [
                byShortcuts('sa,ra,r10,r9,r8,r7,r6,o6,b6'),
                byShortcuts('sk,rk,o10,o9,o8,o7,b7,b8,b9'),
                byShortcuts('sq,rq,b10,bj,bq,bk,ba,s6,s7'),
                byShortcuts('sj,rj,oa,ok,oq,oj,s10,s9,s8'),
            ],
            strategies: [Bock::class, Simple::class],
            opponentStrategies: [Simple::class],
        );

        playCard($game, 'sa');
        playCard($game, 'sk');
        playCard($game, 'sq');
        playCard($game, 'sj');
        playCard($game, 'ra');

        $this->assertEquals([
            'players' => [
                'Ueli' => [
                    'teamMate' => 'Franz',
                    'strategies' => ['bock', 'simple'],
                    'hand' => ['r10', 'r9', 'r8', 'r7', 'r6', 'o6', 'b6'],
                ],
                'Fritz' => [
                    'teamMate' => 'Hans',
                    'strategies' => ['simple'],
                    'hand' => ['rk', 'o10', 'o9', 'o8', 'o7', 'b7', 'b8', 'b9'],
                ],
                'Franz' => [
                    'teamMate' => 'Ueli',
                    'strategies' => ['bock', 'simple'],
                    'hand' => ['rq', 'b10', 'bj', 'bq', 'bk', 'ba', 's6', 's7'],
                ],
                'Hans' => [
                    'teamMate' => 'Fritz',
                    'strategies' => ['simple'],
                    'hand' => ['rj', 'oa', 'ok', 'oq', 'oj', 's10', 's9', 's8'],
                ],
            ],
            'beginningPlayer' => 'Ueli',
            'style' => 'top down',
            'playedCards' => ['sa', 'sk', 'sq', 'sj', 'ra'],
        ], snapshot($game));
    }

    public function testLoadSnapshot()
    {
        $game = new Game();

        loadSnapshot($game, [
            'players' => [
                'Ueli' => [
                    'teamMate' => 'Franz',
                    'strategies' => ['bock', 'simple'],
                    'hand' => ['r10', 'r9', 'r8', 'r7', 'r6', 'o6', 'b6'],
                ],
                'Fritz' => [
                    'teamMate' => 'Hans',
                    'strategies' => ['simple'],
                    'hand' => ['rk', 'o10', 'o9', 'o8', 'o7', 'b7', 'b8', 'b9'],
                ],
                'Franz' => [
                    'teamMate' => 'Ueli',
                    'strategies' => ['bock', 'simple'],
                    'hand' => ['rq', 'b10', 'bj', 'bq', 'bk', 'ba', 's6', 's7'],
                ],
                'Hans' => [
                    'teamMate' => 'Fritz',
                    'strategies' => ['simple'],
                    'hand' => ['rj', 'oa', 'ok', 'oq', 'oj', 's10', 's9', 's8'],
                ],
            ],
            'beginningPlayer' => 'Ueli',
            'style' => 'top down',
            'playedCards' => ['sa', 'sk', 'sq', 'sj', 'ra'],
        ]);

        $this->assertCount(1, $game->playedTricks);
        $this->assertCount(1, $game->currentTrick->turns);
        $this->assertEquals('ra', $game->currentTrick->turns[0]->card->toShortcut());
        $this->assertEquals('Ueli', $game->currentTrick->turns[0]->player->name);
        $this->assertEquals('Fritz', $game->currentPlayer->name);
        $this->assertEquals('top down', $game->style->name);
        $this->assertEquals('Ueli + Franz', $game->players[0]->team);
        $this->assertEquals('Ueli + Franz', $game->players[2]->team);
        $this->assertEquals([Bock::class, Simple::class], $game->players[0]->strategies);
        $this->assertEquals(
            ['rk', 'o10', 'o9', 'o8', 'o7', 'b7', 'b8', 'b9'],
            array_map(fn ($card) => $card->toShortcut(), $game->players[1]->hand),
        );
    }
}
