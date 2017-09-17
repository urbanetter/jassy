<?php

namespace Tests;


use function Jass\CardSet\bySuitsAndValues;
use function Jass\CardSet\values;
use Jass\Entity\Card\Suit;
use Jass\Entity\Player;
use Jass\Entity\Card;
use Jass\Entity\Trick;
use Jass\Intelligence;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class IntelligenceTest extends TestCase
{
    public function testRoses()
    {
        $intelligence = new Intelligence();
        $style = new TopDown();

        $ueli = new Player();
        $ueli->hand = bySuitsAndValues([Suit::ROSE], values());
        $ueli->strategies = ['RoseIsMyFavoriteSuit'];

        $intelligence->registerPlayerIntelligence($ueli, 'Tests\\Strategy', 'Tests\\Ability');

        $expected = Card::from(Suit::ROSE, Card\Value::ACE);
        $this->assertEquals($expected, $intelligence->firstCard($ueli, $style));

        $trick = new Trick();
        $intelligence->seeTrick($ueli, $trick, $style);

        $this->assertArrayHasKey('hello', $ueli->brain);
        $this->assertEquals('world', $ueli->brain['hello']);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Could not figure out next card for player Ueli');
        $intelligence->card($ueli, $trick, $style);
    }
}