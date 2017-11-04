<?php

namespace Tests\Knowledge;


use function Jass\CardSet\byShortcuts;
use function Jass\CardSet\bySuit;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Knowledge\SuitsKnowledge;
use PHPUnit\Framework\TestCase;

class SuitsKnowledgeTest extends TestCase
{
    public function testOrderedSuits()
    {
        $game = testGame([
            byShortcuts('sa,sk,sq,oj,o10,o9,r6,r7,r8')
        ]);

        $actual = SuitsKnowledge::analyze($game)->orderedSuits;
        $expected = [Suit::SHIELD, Suit::OAK, Suit::ROSE];
        $this->assertEquals($expected, $actual);
    }

    public function testSuitsOnlyInMyTeam()
    {
        $game = testGame([
            byShortcuts('sa,sk,sq'),
            bySuit(Suit::OAK),
            byShortcuts('s6,s7,s8'),
            bySuit(Suit::BELL),
        ]);

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyInMyTeam;

        $this->assertCount(0, $actual);

        $game = playCard($game, 'sa');
        $game = playCard($game, 'oq');
        $game = playCard($game, 's6');
        $game = playCard($game, 'bq');

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyInMyTeam;
        $this->assertContains(Suit::SHIELD, $actual);

    }

    public function testSuitsOnlyInMyTeamNotWorkingWhenTeammateHasNoSuit()
    {

        $game = testGame([
            bySuit(Suit::SHIELD),
            bySuit(Suit::OAK),
            bySuit(Suit::ROSE),
            bySuit(Suit::BELL),
        ]);

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyInMyTeam;

        $this->assertCount(0, $actual);

        $game = playCard($game, 'sa');
        $game = playCard($game, 'oq');
        $game = playCard($game, 'r6');
        $game = playCard($game, 'bq');

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyInMyTeam;
        $this->assertCount(0, $actual);

    }

    public function testSuitsOnlyIHave()
    {
        $game = testGame([
            byShortcuts('sa,r6'),
            bySuit(Suit::BELL),
            byShortcuts('sk,sq,sj,s10,s9,s8,s7,s6'),
            bySuit(Suit::OAK)
        ]);

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyIHave;
        $this->assertCount(0, $actual);

        playCard($game, 'sa');
        playCard($game, 'b6');
        playCard($game, 's6');
        playCard($game, 'o6');

        playCard($game, 'r6');
        playCard($game, 'b7');

        $actual = SuitsKnowledge::analyze($game)->suitsOnlyIHave;
        $this->assertContains(Suit::SHIELD, $actual);



    }

}