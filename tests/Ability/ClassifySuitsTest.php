<?php

namespace Tests\Ability;


use Jass\Ability\ClassifySuits;
use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class ClassifySuitsTest extends TestCase
{
    public function testRecognizesBadSuits()
    {
        $player = new Player();
        $style = new TopDown();
        $trick = new Trick();

        $player->hand = byShortcuts('sq, sj, bj, rk, rq, r6');

        ClassifySuits::seeTrick($player, $trick, $style);
        $this->assertContains(Card\Suit::BELL, $player->brain[ClassifySuits::BAD_SUITS]);
        $this->assertContains(Card\Suit::SHIELD, $player->brain[ClassifySuits::BAD_SUITS]);

    }

}