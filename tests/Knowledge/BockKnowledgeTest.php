<?php

namespace Tests\Knowledge;


use function Jass\CardSet\byShortcuts;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card;
use Jass\Knowledge\BockKnowledge;
use PHPUnit\Framework\TestCase;

class BockKnowledgeTest extends TestCase
{
    public function testBockCards()
    {
        $game = testGame([
            byShortcuts('sk,sq,o6'),
            byShortcuts('sa,ra,rk,rq,rj,r10,r9,r8,r7'),
            byShortcuts('o7'),
            byShortcuts('o8'),
        ]);

        $actual = BockKnowledge::analyze($game)->bockCards;
        $expected = [
            Suit::SHIELD => Card::shortcut('sa'),
            Suit::BELL => Card::shortcut('ba'),
            Suit::OAK => Card::shortcut('oa'),
            Suit::ROSE => Card::shortcut('ra'),
        ];
        $this->assertCount(4, $actual);
        $this->assertEquals($expected, $actual);


        playCard($game, 'o6');
        playCard($game, 'sa');
        playCard($game, 'o7');
        playCard($game, 'o8');

        $actual = BockKnowledge::analyze($game)->bockCards;
        $this->assertEquals(Card::shortcut('sk'), $actual[Suit::SHIELD]);
    }

    public function testSuitPotential()
    {
        $player1Cards = byShortcuts('sk,sq,o6,b6,b7,b8,b9,b10,bj');
        $player2Cards = byShortcuts('sa,ra,rk,rq,rj,r10,r9,r8,r7');

        $game = testGame([
            $player1Cards,
            $player2Cards,
            byShortcuts('o7'),
            byShortcuts('o8'),
        ]);

        $actual = BockKnowledge::analyze($game)->suitPotential;

        $expected = [
            Suit::SHIELD => 1,
            Suit::BELL => 3,
            Suit::OAK => 8,
        ];

        $this->assertEquals($expected, $actual);
    }
}