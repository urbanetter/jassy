<?php

namespace Tests\Strategy;


use Jass\Strategy\Bock;
use Tests\Support\TestScenario;
use PHPUnit\Framework\TestCase;

class BockTest extends TestCase
{
    public function testBock(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sk,sq,oj,b6,b7,b8,b9,b10,bj')
            ->hand('Fritz', 'sa,ra,rk,rq,rj,r10,r9,r8,r7')
            ->hand('Franz', 'o7')
            ->hand('Hans', 'o8');

        $this->assertNull($scenario->choose(Bock::class));

        $scenario
            ->play('oj')
            ->play('sa')
            ->play('o7')
            ->play('o8');

        $this->assertSame('sk', $scenario->chooseShortcut(Bock::class));

    }
}
