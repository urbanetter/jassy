<?php

namespace Tests\Ability;


use Jass\Ability\KnowsPlayedCards;
use function Jass\Player\byNames;
use Jass\Style\TopDown;
use Jass\Entity\Card;
use function Jass\Trick\byShortcuts;
use PHPUnit\Framework\TestCase;

class KnowsPlayedCardsTest extends TestCase
{
    public function testKnowledge()
    {
        $players = byNames('Ueli, Fritz, Hans, Köbi');
        $style = new TopDown();

        $ueli = $players[0];

        $trick = byShortcuts($players, 'ra, rk, r6, rq');
        KnowsPlayedCards::seeTrick($ueli, $trick, $style);

        $trick = byShortcuts($players, 's7, sk, s6, sq');
        KnowsPlayedCards::seeTrick($ueli, $trick, $style);

        $this->assertTrue(in_array(Card::shortcut('ra'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('rk'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('r6'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('rq'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('s7'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('sk'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('s6'), $ueli->brain['playedCards']));
        $this->assertTrue(in_array(Card::shortcut('sq'), $ueli->brain['playedCards']));
    }
}