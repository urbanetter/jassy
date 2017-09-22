<?php

namespace Tests;


use function Jass\CardSet\bySuitsAndValues;
use function Jass\CardSet\values;
use Jass\Entity\Card\Suit;
use Jass\Entity\Player;
use Jass\Entity\Card;
use Jass\Entity\Trick;
use function Jass\Strategy\card;
use function Jass\Strategy\firstCardOfTrick;
use function Jass\Strategy\seeTrick;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;
use Tests\Strategy\RoseIsMyFavoriteSuit;

class IntelligenceTest extends TestCase
{
    public function testRoses()
    {
        $style = new TopDown();

        $ueli = new Player();
        $ueli->hand = bySuitsAndValues([Suit::ROSE], values());
        $ueli->strategies = [RoseIsMyFavoriteSuit::class];


        $expected = Card::from(Suit::ROSE, Card\Value::ACE);
        $this->assertEquals($expected, firstCardOfTrick($ueli, $style));

        $trick = new Trick();
        seeTrick($ueli, $trick, $style);

        $this->assertArrayHasKey('hello', $ueli->brain);
        $this->assertEquals('world', $ueli->brain['hello']);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Could not figure out next card for player Ueli');
        card($ueli, $trick, $style);
    }
}