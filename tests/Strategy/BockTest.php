<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Strategy\Bock;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class BockTest extends TestCase
{
    public function testBock()
    {
        $player = new Player('Ueli');
        $style = new TopDown();
        $player->hand = byShortcuts('rq, rj, r10, s6, s7, s8, s9, s10, sj');

        $this->assertNull(Bock::firstCardOfTrick($player, $style));

        $player->brain['playedCards'] = [Card::shortcut('ra')];

        $this->assertNull(Bock::firstCardOfTrick($player, $style));

        $player->brain['playedCards'] = [Card::shortcut('ra'), Card::shortcut('rk')];

        $this->assertEquals(Card::shortcut('rq'), Bock::firstCardOfTrick($player, $style));

    }
}