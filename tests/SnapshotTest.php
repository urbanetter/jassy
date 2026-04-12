<?php

namespace Tests;

use Jass\Entity\Game;
use function Jass\Game\loadSnapshot;
use Jass\Snapshot;
use Jass\SnapshotPlayer;
use Jass\Strategy\Bock;
use Jass\Strategy\Simple;
use PHPUnit\Framework\TestCase;

class SnapshotTest extends TestCase
{
    public function testCanBuildSnapshotWithFluentDsl() : void
    {
        $snapshot = Snapshot::new()
            ->style('top down')
            ->beginningPlayer('Ueli')
            ->player('Ueli', fn (SnapshotPlayer $player) => $player
                ->teamMate('Franz')
                ->strategies('bock', 'simple')
                ->hand('r10,r9,r8,r7,r6,o6,b6'))
            ->player('Fritz', fn (SnapshotPlayer $player) => $player
                ->teamMate('Hans')
                ->strategies('simple')
                ->hand('rk,o10,o9,o8,o7,b7,b8,b9'))
            ->player('Franz', fn (SnapshotPlayer $player) => $player
                ->teamMate('Ueli')
                ->strategies('bock', 'simple')
                ->hand('rq,b10,bj,bq,bk,ba,s6,s7'))
            ->player('Hans', fn (SnapshotPlayer $player) => $player
                ->teamMate('Fritz')
                ->strategies('simple')
                ->hand('rj,oa,ok,oq,oj,s10,s9,s8'))
            ->playedCards('sa,sk,sq,sj,ra')
            ->toArray();

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
        ], $snapshot);
    }

    public function testBuiltSnapshotCanBeLoadedIntoGame() : void
    {
        $snapshot = Snapshot::new()
            ->style('top down')
            ->beginningPlayer('Ueli')
            ->player('Ueli', fn (SnapshotPlayer $player) => $player
                ->teamMate('Franz')
                ->strategies('bock', 'simple')
                ->hand('r10,r9,r8,r7,r6,o6,b6'))
            ->player('Fritz', fn (SnapshotPlayer $player) => $player
                ->teamMate('Hans')
                ->strategies('simple')
                ->hand('rk,o10,o9,o8,o7,b7,b8,b9'))
            ->player('Franz', fn (SnapshotPlayer $player) => $player
                ->teamMate('Ueli')
                ->strategies('bock', 'simple')
                ->hand('rq,b10,bj,bq,bk,ba,s6,s7'))
            ->player('Hans', fn (SnapshotPlayer $player) => $player
                ->teamMate('Fritz')
                ->strategies('simple')
                ->hand('rj,oa,ok,oq,oj,s10,s9,s8'))
            ->playedCards('sa,sk,sq,sj,ra');

        $game = loadSnapshot(new Game(), $snapshot->toArray());

        $this->assertEquals('Fritz', $game->currentPlayer->name);
        $this->assertEquals('ra', $game->currentTrick->turns[0]->card->toShortcut());
        $this->assertEquals([Bock::class, Simple::class], $game->players[0]->strategies);
    }
}
