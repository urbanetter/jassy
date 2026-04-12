<?php

namespace Tests\Strategy;


use Jass\Strategy\Simple;
use Tests\Support\TestScenario;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testSimpleStrategy(): void
    {
        $scenario = TestScenario::topDown()
            ->hand('Ueli', 'sa,sq,r6,r7')
            ->hand('Fritz', 'ra,rk,s6,s7')
            ->hand('Franz', 'r8,r9');

        $this->assertSame('sa', $scenario->chooseShortcut(Simple::class));

        $scenario->play('r6');

        $this->assertSame('ra', $scenario->chooseShortcut(Simple::class));

        $scenario->play('ra');

        $this->assertSame('r8', $scenario->chooseShortcut(Simple::class));
    }
}
