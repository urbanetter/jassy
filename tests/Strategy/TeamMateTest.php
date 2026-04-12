<?php

namespace Tests\Strategy;


use Jass\Entity\Card\Suit;
use Jass\Strategy\TeamMate;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestScenario;

class TeamMateTest extends TestCase
{
    public function testTeamOnly(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sa,s6,b6,b7,b8,b9,b10,bj,bq')
            ->hand('Fritz', 'o6,o7,o8,o9,o10,oj,oq,ok,oa')
            ->hand('Franz', 'ba,bk,s7,s8,s9,s10,sj,sq,sk')
            ->hand('Hans', 'r6,r7,r8,r9,r10,rj,rq,rk,ra');

        $scenario
            ->play('sa')
            ->play('o6')
            ->play('s7')
            ->play('r6');

        $this->assertSame('s6', $scenario->chooseShortcut(TeamMate::class));

    }

    public function testGoodSuit(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sk,sq,sj,s10,s9,b6,b7,b8,b9')
            ->hand('Fritz', 's6')
            ->hand('Franz', 'ra,rk,rq,rj,o6,o7,o8,s7,sa')
            ->hand('Hans', 's8');

        $this->assertSame('s9', $scenario->chooseShortcut(TeamMate::class));

        $scenario
            ->play('s10')
            ->play('s6')
            ->play('sa')
            ->play('s8');

        $this->assertSame('s7', $scenario->chooseShortcut(TeamMate::class));
    }

    public function testBadSuits(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sa,ba,sq,sj,s10,oj,b6,b7,r6')
            ->hand('Fritz', 's6,bk,r7,r8')
            ->hand('Franz', 'ra,rk,rq,rj,o6,o7,o8,o9,o10')
            ->hand('Hans', 's7,bq');

        $scenario
            ->play('sa')
            ->play('s6');

        $actual = $scenario->choose(TeamMate::class);
        $this->assertEquals(Suit::OAK, $actual->suit); // toss away oak

        $scenario
            ->play('o6')
            ->play('s7');

        $scenario
            ->play('ba')
            ->play('bk');

        $actual = $scenario->choose(TeamMate::class);
        $this->assertEquals(Suit::OAK, $actual->suit); // toss away oak

        $scenario
            ->play('o7')
            ->play('bq');

        $actual = $scenario->choose(TeamMate::class);

        // player 1 now knows team mate does not have shield and bell
        // since oak is tossed away player 1 should play suit rose
        $this->assertEquals(Suit::ROSE, $actual->suit);

    }
}
