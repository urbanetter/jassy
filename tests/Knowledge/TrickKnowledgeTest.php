<?php
namespace Tests\Knowledge;

use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Knowledge\TrickKnowledge;
use PHPUnit\Framework\TestCase;

class TrickKnowledgeTest extends TestCase
{
    public function testBasic()
    {
        $game = testGame([
            byShortcuts('sa'),
            byShortcuts('sk'),
            byShortcuts('sq'),
            byShortcuts('sj')
        ]);

        $actual = TrickKnowledge::analyze($game);

        $this->assertTrue($actual->canLead);
        $this->assertTrue($actual->leadingTurnInMyTeam);
        $this->assertNull($actual->leadingSuit);
        $this->assertNull($actual->bestCard);
        $this->assertCount(0, $actual->playedCards);
        $this->assertEquals(1, $actual->playerOfTurn);
        $this->assertTrue($actual->isFirst);

        playCard($game, Card::shortcut('sa'));
        $actual = TrickKnowledge::analyze($game);

        $this->assertFalse($actual->canLead);
        $this->assertFalse($actual->leadingTurnInMyTeam);
        $this->assertEquals(Card\Suit::SHIELD, $actual->leadingSuit);
        $this->assertEquals(Card::shortcut('sa'), $actual->bestCard);
        $this->assertCount(1, $actual->playedCards);
        $this->assertTrue(in_array(Card::shortcut('sa'), $actual->playedCards));
        $this->assertEquals(2, $actual->playerOfTurn);
        $this->assertTrue($actual->isFirst);


        playCard($game, Card::shortcut('sk'));
        $actual = TrickKnowledge::analyze($game);

        $this->assertFalse($actual->canLead);
        $this->assertTrue($actual->leadingTurnInMyTeam);
        $this->assertEquals(Card\Suit::SHIELD, $actual->leadingSuit);
        $this->assertEquals(Card::shortcut('sa'), $actual->bestCard);
        $this->assertCount(2, $actual->playedCards);
        $this->assertTrue(in_array(Card::shortcut('sk'), $actual->playedCards));
        $this->assertEquals(3, $actual->playerOfTurn);
        $this->assertTrue($actual->isFirst);

        playCard($game, Card::shortcut('sq'));
        $actual = TrickKnowledge::analyze($game);

        $this->assertFalse($actual->canLead);
        $this->assertFalse($actual->leadingTurnInMyTeam);
        $this->assertEquals(Card\Suit::SHIELD, $actual->leadingSuit);
        $this->assertEquals(Card::shortcut('sa'), $actual->bestCard);
        $this->assertCount(3, $actual->playedCards);
        $this->assertTrue(in_array(Card::shortcut('sq'), $actual->playedCards));
        $this->assertEquals(4, $actual->playerOfTurn);
        $this->assertTrue($actual->isFirst);

        playCard($game, 'sj');
        $actual = TrickKnowledge::analyze($game);

        // new trick
        $this->assertCount(1, $game->playedTricks);
        $this->assertTrue($actual->canLead);
        $this->assertTrue($actual->leadingTurnInMyTeam);
        $this->assertNull($actual->leadingSuit);
        $this->assertNull($actual->bestCard);
        $this->assertCount(0, $actual->playedCards);
        $this->assertEquals(1, $actual->playerOfTurn);
        $this->assertFalse($actual->isFirst);


    }
}