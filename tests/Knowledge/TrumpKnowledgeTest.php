<?php

namespace Tests\Knowledge;


use function Jass\CardSet\byShortcuts;
use function Jass\CardSet\bySuit;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Knowledge\TrumpKnowledge;
use Jass\Strategy\Simple;
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

    public function testKnowledgeAfterPlayingTrump()
    {
        $game = testGame(
            [
                byShortcuts('rj,r9,ra,ba,bk,o6,o7,o8,s6'),
                byShortcuts('rq,r10,oa,ok,o9,s7,s8,s9,s10'),
                byShortcuts('r6,r7,r8,oq,oj,sa,sj,b6,b7'),
                byShortcuts('rk') // all the other cards
            ],
            new Trump(Suit::ROSE)
        );

        $simple = new Simple();

        $trump = TrumpKnowledge::analyze($game);
        $this->assertEquals(true, $trump->isTrumpGame);
        $this->assertEquals(Suit::ROSE, $trump->suit);
        $this->assertEquals($game->currentPlayer->team, $trump->choosingTeam);
        $this->assertEquals(byShortcuts('rj,r9,ra'), $trump->hand);
        $this->assertEquals(true, $trump->shouldLeadWithTrump);
        $this->assertEquals(true, $trump->possibleMatch);
        $this->assertEquals([], $trump->played);

        playCard($game, 'rj');
        playCard($game, 'r10');
        playCard($game, 'r6');
        playCard($game, 'rk');

        $trump = TrumpKnowledge::analyze($game);
        $this->assertEquals(byShortcuts('r9,ra'), $trump->hand);
        $this->assertEquals(true, $trump->shouldLeadWithTrump);
        $this->assertEquals(true, $trump->possibleMatch);
        $this->assertEquals(byShortcuts('rj,r10,r6,rk'), $trump->played);

        playCard($game, 'r9');
        playCard($game, 'rq');
        playCard($game, 'r7');
        playCard($game, $simple->chooseCard($game));

        $trump = TrumpKnowledge::analyze($game);
        $this->assertEquals(byShortcuts('ra'), $trump->hand);
        $this->assertEquals(Suit::ROSE, $trump->suit);
        $this->assertEquals(false, $trump->shouldLeadWithTrump);
        $this->assertEquals(true, $trump->possibleMatch);
        $this->assertEquals(byShortcuts('rj,r10,r6,rk,r9,rq,r7'), $trump->played);
    }
}