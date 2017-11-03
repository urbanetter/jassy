<?php

namespace Tests\Strategy;


use Jass\Ability\TeamOnlySuits;
use Jass\Ability\TrickCounter;
use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Player;
use Jass\Strategy\Ustrumpfe;
use Jass\Style\Trump;
use PHPUnit\Framework\TestCase;

class UstrumpfeTest extends TestCase
{
    public function testBasic()
    {
        $player = new Player();
        $player->hand = byShortcuts('sj, s9, sa, oa, rk');

        $style = new Trump(Suit::SHIELD);

        $player->brain[TrickCounter::CHOOSING_TEAM] = true;
        $player->brain[TrickCounter::TRICK_COUNTER] = 1;
        $player->brain[TeamOnlySuits::TEAM_ONLY_SUITS] = [];

        $card = Ustrumpfe::firstCardOfTrick($player, $style);
        $this->assertEquals(Card::shortcut('sj'), $card);

        $player->hand = byShortcuts('s9, sa, oa, rk');

        $card = Ustrumpfe::firstCardOfTrick($player, $style);
        $this->assertEquals(Card::shortcut('s9'), $card);

        $player->brain[TeamOnlySuits::TEAM_ONLY_SUITS] = [Suit::SHIELD];
        $card = Ustrumpfe::firstCardOfTrick($player, $style);
        $this->assertNull($card);

    }
}