<?php

namespace Tests\Strategy;


use Jass\Entity\Card\Suit;
use Jass\Strategy\Bock;
use Jass\Strategy\Simple;
use Jass\Strategy\Trump;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestScenario;

class TrumpStrategyTest extends TestCase
{
    public function testTrump(): void
    {
        $scenario = TestScenario::trump(Suit::ROSE)
            ->hand('Ueli', 'rj,r9,ra,ba,bk,o6,o7,o8,s6')
            ->hand('Fritz', 'rq,r10,oa,ok,o9,s7,s8,s9,s10')
            ->hand('Franz', 'r6,r7,r8,oq,oj,sa,sj,b6,b7')
            ->hand('Hans', 'b8,b9,b10,bj,bq,rk,sq,sk,o10');

        $this->assertSame('rj', $scenario->chooseShortcut(Trump::class));
        $scenario
            ->play('rj')
            ->play('r10')
            ->play('r6')
            ->play($scenario->choose(Simple::class));

        $this->assertSame('r9', $scenario->chooseShortcut(Trump::class));
        $scenario
            ->play('r9')
            ->play('rq')
            ->play('r7')
            ->play($scenario->choose(Simple::class));

        $this->assertNull($scenario->choose(Trump::class));
        $this->assertNotSame('ra', $scenario->chooseShortcut(Bock::class));

    }

    public function testOvertrumpsIfLowerThanAceOnFirstTrick(): void
    {
        $scenario = TestScenario::trump(Suit::ROSE)
            ->hand('Ueli', 'sa,r7,r8,rk,oj,sj,b6,b7')
            ->hand('Fritz', 'oq,o10,oa,ok,o9,s7,s8,s9,s10')
            ->hand('Franz', 'rj,r9,ra,ba,bk,o6,o7,o8,s6');

        $this->assertSame('rk', $scenario->chooseShortcut(Trump::class));
        $scenario
            ->play('rk')
            ->play('s7');
        $this->assertSame('rj', $scenario->chooseShortcut(Trump::class));

        $scenario = TestScenario::trump(Suit::ROSE)
            ->hand('Ueli', 'ra,r7,r8,rk,oj,sa,sj,b6,b7')
            ->hand('Fritz', 'oq,o10,oa,ok,o9,s7,s8,s9,s10')
            ->hand('Franz', 'rj,r9,sa,ba,bk,o6,o7,o8,s6');

        $this->assertSame('ra', $scenario->chooseShortcut(Trump::class));
        $scenario
            ->play('ra')
            ->play('s7');
        $this->assertNull($scenario->choose(Trump::class));

    }
}
