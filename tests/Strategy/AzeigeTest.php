<?php

namespace Tests\Strategy;


use Jass\Ability\RecognisesAzeige;
use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Player;
use Jass\Entity\Trick;
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
        $franz->hand = byShortcuts('s7, ok, oq, oj');
        $firstCard = Azeige::firstCardOfTrick($ueli, $style);
        $this->assertEquals(Card::shortcut('s6'), $firstCard);

        $firstTrick = new Trick();
        $firstTrick = addTurn($firstTrick, $ueli, $firstCard);
        $firstTrick = addTurn($firstTrick, $fritz, Card::shortcut('s10'));
        $firstTrick = addTurn($firstTrick, $franz, Card::shortcut('sa'));
        $firstTrick = addTurn($firstTrick, $hans, Card::shortcut('r6'));

        RecognisesAzeige::seeTrick($franz, $firstTrick, $style);

        $this->assertArrayHasKey(RecognisesAzeige::SUIT_WANTED_BY_PARTNER, $franz->brain);
        $this->assertEquals(Suit::SHIELD, $franz->brain[RecognisesAzeige::SUIT_WANTED_BY_PARTNER]);
        $this->assertEquals($franz, winner($firstTrick, $style->orderFunction()));

        RecognisesAzeige::seeTrick($ueli, $firstTrick, $style);

        $this->assertArrayNotHasKey(RecognisesAzeige::SUIT_WANTED_BY_PARTNER, $ueli->brain);
        $this->assertArrayHasKey(RecognisesAzeige::SUIT_WANTED_BY_ME, $ueli->brain);
        $this->assertEquals(Suit::SHIELD, $ueli->brain[RecognisesAzeige::SUIT_WANTED_BY_ME]);

        $card = Azeige::firstCardOfTrick($franz, $style);
        $this->assertEquals(Card::shortcut('s7'), $card);

        $secondTrick = new Trick();
        $secondTrick = addTurn($secondTrick, $franz, $card);
        $secondTrick = addTurn($secondTrick, $hans, Card::shortcut('s8'));
        $secondTrick = addTurn($secondTrick, $ueli, Card::shortcut('sk'));
        $secondTrick = addTurn($secondTrick, $fritz, Card::shortcut('o9'));

        RecognisesAzeige::seeTrick($ueli, $secondTrick, $style);

        $this->assertArrayNotHasKey(RecognisesAzeige::SUIT_WANTED_BY_PARTNER, $ueli->brain);



    }

    public function testDoesNotWorkWhenThereIsStillAWinningCard()
    {
        $ueli = new Player();
        $style = new TopDown();

        $ueli->hand = byShortcuts('ba, sk, sq, s6');

        $this->assertNull(Azeige::firstCardOfTrick($ueli, $style));
    }


}