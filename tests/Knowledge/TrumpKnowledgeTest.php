<?php

namespace Tests\Knowledge;


use function Jass\CardSet\bySuit;
use Jass\Entity\Card\Suit;
use function Jass\Game\testGame;
use Jass\Knowledge\TrumpKnowledge;
use Jass\Style\Trump;
use PHPUnit\Framework\TestCase;

class TrumpKnowledgeTest extends TestCase
{
    public function testKnowledge()
    {
        $game = testGame([bySuit(Suit::OAK)], new Trump(Suit::OAK));

        $actual = TrumpKnowledge::analyze($game);

        $this->assertTrue($actual->isTrumpGame);
        $this->assertEquals($game->currentPlayer->team, $actual->choosingTeam);
        $this->assertEquals(Suit::OAK, $actual->suit);
        $this->assertEquals([], $actual->played);
        $this->assertEquals($game->currentPlayer->hand, $actual->hand);
        $this->assertTrue($actual->possibleMatch);
        $this->assertFalse($actual->shouldLeadWithTrump); // because all trumps are at player ones disposal
    }
}