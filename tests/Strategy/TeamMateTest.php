<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use function Jass\CardSet\bySuit;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Strategy\TeamMate;
use PHPUnit\Framework\TestCase;

class TeamMateTest extends TestCase
{
    public function testTeamOnly()
    {
        $game = testGame([
            byShortcuts('sa,s6,b6,b7,b8,b9,b10,bj,bq'),
            bySuit(Suit::OAK),
            byShortcuts('ba,bk,s7,s8,s9,s10,sj,sq,sk'),
            bySuit(Suit::ROSE)
        ]);

        $game = playCard($game, 'sa');
        $game = playCard($game, 'o6');
        $game = playCard($game, 's7');
        $game = playCard($game, 'r6');

        $sut = new TeamMate();
        $actual = $sut->chooseCard($game);
        $this->assertEquals(Card::shortcut('s6'), $actual);

    }

    public function testGoodSuit()
    {
        $sut = new TeamMate();

        $game = testGame([
            byShortcuts('sk,sq,sj,s10,s9,b6,b7,b8,b9'),
            byShortcuts('s6'),
            byShortcuts('ra,rk,rq,rj,o6,o7,o8,s7,sa'),
            byShortcuts('s8')
        ]);

        $firstCard = $sut->chooseCard($game);
        $this->assertEquals(Card::shortcut('s9'), $firstCard);

        $game = playCard($game, 's10');
        $game = playCard($game, 's6');
        $game = playCard($game, 'sa');
        $game = playCard($game, 's8');

        $givingBackCard = $sut->chooseCard($game);
        $this->assertEquals(Card::shortcut('s7'), $givingBackCard);
    }

    public function testBadSuits()
    {
        $game = testGame([
            byShortcuts('sa,ba,sq,sj,s10,oj,b6,b7,r6'),
            byShortcuts('s6,bk'),
            byShortcuts('ra,rk,rq,rj,o6,o7,o8,o9,o10'),
            byShortcuts('s7,bq')
        ]);
        $sut = new TeamMate();


        $game = playCard($game, 'sa');
        $game = playCard($game, 's6');

        $actual = $sut->chooseCard($game);
        $this->assertEquals(Suit::OAK, $actual->suit); // toss away oak

        $game = playCard($game, 'o6');
        $game = playCard($game, 's7');

        $game = playCard($game, 'ba');
        $game = playCard($game, 'bk');

        $actual = $sut->chooseCard($game);
        $this->assertEquals(Suit::OAK, $actual->suit); // toss away oak

        $game = playCard($game, 'o7');
        $game = playCard($game, 'bq');

        $sut = new TeamMate();
        $actual = $sut->chooseCard($game);

        // player 1 now knows team mate does not have shield and bell
        // since oak is tossed away player 1 should play suit rose
        $this->assertEquals(Suit::ROSE, $actual->suit);

    }
}