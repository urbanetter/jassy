<?php

namespace Tests\Ability;


use Jass\Ability\RecognisesVerrueren;
use Jass\Entity\Card\Suit;
use function Jass\Player\byNames;
use Jass\Style\TopDown;
use function Jass\Trick\byShortcuts;
use PHPUnit\Framework\TestCase;

class RecognisesVerruerenTest extends TestCase
{
    public function testRecognition()
    {
        $players = byNames('Ueli, Fritz, Hans, Köbi');
        $style = new TopDown();

        $ueli = $players[0];

        $trick = byShortcuts($players, 'ra, rk, r6, rq');
        RecognisesVerrueren::seeTrick($ueli, $trick, $style);
        $this->assertEquals(0, count($ueli->brain[RecognisesVerrueren::SUITS]));

        $trick = byShortcuts($players, 'r7, rk, r6, rq');
        RecognisesVerrueren::seeTrick($ueli, $trick, $style);
        $this->assertEquals(0, count($ueli->brain[RecognisesVerrueren::SUITS]));

        $trick = byShortcuts($players, 'ra, rk, b6, rq');
        RecognisesVerrueren::seeTrick($ueli, $trick, $style);
        $this->assertContains(Suit::BELL, $ueli->brain[RecognisesVerrueren::SUITS]);

        $trick = byShortcuts($players, 'ra, rk, s6, rq');
        RecognisesVerrueren::seeTrick($ueli, $trick, $style);
        $this->assertContains(Suit::BELL, $ueli->brain[RecognisesVerrueren::SUITS]);
        $this->assertContains(Suit::SHIELD, $ueli->brain[RecognisesVerrueren::SUITS]);

    }
}