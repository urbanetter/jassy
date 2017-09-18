<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Trick;
use function Jass\Hand\playCardOfHand;
use function Jass\Player\byNames;
use Jass\Strategy\Azeige;
use Jass\Style\TopDown;
use function Jass\Trick\addTurn;
use function Jass\Trick\winner;
use PHPUnit\Framework\TestCase;

class AzeigeTest extends TestCase
{
    public function testUseCase()
    {
        list($ueli, $fritz, $franz, $hans) = byNames('Ueli, Fritz, Franz, Hans');
        $style = new TopDown();

        $ueli->hand = byShortcuts('sk, sq, s6, rj, bk');
        $franz->hand = byShortcuts('sa, s7, ok, oq, oj');
        $firstCard = Azeige::firstCardOfTrick($ueli, $style);
        $this->assertEquals(Card::shortcut('s6'), $firstCard);

        $firstTrick = new Trick();
        $firstTrick = addTurn($firstTrick, $ueli, $firstCard);
        $firstTrick = addTurn($firstTrick, $fritz, Card::shortcut('s10'));

        $this->assertNull(Azeige::card($franz, $firstTrick, $style));

        $this->assertArrayHasKey(Azeige::SUIT_WANTED_BY_PARTNER, $franz->brain);
        $this->assertEquals(Suit::SHIELD, $franz->brain[Azeige::SUIT_WANTED_BY_PARTNER]);

        $firstTrick = addTurn($firstTrick, $franz, Card::shortcut('sa'));
        $franz->hand = playCardOfHand($franz->hand, Card::shortcut('sa'));
        $firstTrick = addTurn($firstTrick, $hans, Card::shortcut('s8'));

        $this->assertEquals($franz, winner($firstTrick, $style->orderFunction()));

        $card = Azeige::firstCardOfTrick($franz, $style);
        $this->assertEquals(Card::shortcut('s7'), $card);

    }
}