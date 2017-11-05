<?php

namespace Tests\Knowledge;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Knowledge\TeamMateKnowledge;
use PHPUnit\Framework\TestCase;

class TeamMateKnowledgeTest extends TestCase
{
    public function testGoodSuits()
    {
        $game = testGame([
            byShortcuts('sk,sq,sj,s10,s9,b6,b7,b8,b9'),
            byShortcuts('s6'),
            byShortcuts('ra,rk,rq,rj,o6,o7,o8,o9,sa'),
            byShortcuts('s7')
        ]);

        $actual = TeamMateKnowledge::analyze($game)->goodSuits;
        $this->assertCount(0, $actual);

        $game = playCard($game, 's9');
        $game = playCard($game, 's6');
        $game = playCard($game, 'sa');
        $game = playCard($game, 's7');

        $actual = TeamMateKnowledge::analyze($game)->goodSuits;
        $this->assertContains(Suit::SHIELD, $actual);

    }

    public function testBadSuits()
    {
        $game = testGame([
            byShortcuts('sa,sk,sq,sj,s10,s9,b6,b7,b8'),
            byShortcuts('s6'),
            byShortcuts('ra,rk,rq,rj,o6,o7,o8,o9,o10'),
            byShortcuts('s7')
        ]);

        $actual = TeamMateKnowledge::analyze($game)->badSuits;
        $this->assertCount(0, $actual);

        $game = playCard($game, 'sa');
        $game = playCard($game, 's6');
        $game = playCard($game, 'o6');
        $game = playCard($game, 's7');

        $actual = TeamMateKnowledge::analyze($game)->badSuits;
        $this->assertContains(Suit::OAK, $actual);

    }
}