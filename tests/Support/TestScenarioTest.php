<?php

namespace Tests\Support;

use Jass\Strategy\Bock;
use Jass\Strategy\Simple;
use PHPUnit\Framework\TestCase;

class TestScenarioTest extends TestCase
{
    public function testCanDescribeHandsAndAskStrategyForTheNextCard(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sa,sq,r6,r7')
            ->hand('Fritz', 'ra,rk,s6,s7')
            ->hand('Franz', 'r8,r9')
            ->strategies('Ueli', Bock::class, Simple::class);

        $this->assertSame('sa', $scenario->chosenShortcut('Ueli'));
    }

    public function testCanCheckIfACardIsValidInTheCurrentTrick(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Fritz', 'b6,ra')
            ->currentTrick([
                'Ueli' => 'ba',
            ]);

        $this->assertTrue($scenario->isValid('b6', 'Fritz'));
        $this->assertFalse($scenario->isValid('ra', 'Fritz'));
    }

    public function testCanPlayCardsToAdvanceTheScenario(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'r6,sa')
            ->hand('Fritz', 'ra,s6')
            ->currentPlayer('Ueli')
            ->play('r6');

        $this->assertSame('Fritz', $scenario->game()->currentPlayer->name);
        $this->assertFalse($scenario->isValid('s6'));
        $this->assertTrue($scenario->isValid('ra'));
    }

    public function testCanUseTrumpStyle(): void
    {
        $scenario = TestScenario::trump('r')
            ->hand('Fritz', 'b6,ra')
            ->currentTrick([
                'Ueli' => 'ba',
            ]);

        $this->assertTrue($scenario->isValid('ra', 'Fritz'));
    }
}
