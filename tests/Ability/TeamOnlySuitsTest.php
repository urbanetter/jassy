<?php

namespace Tests\Ability;


use Jass\Ability\TeamOnlySuits;
use Jass\Entity\Card\Suit;
use function Jass\Player\byNames;
use Jass\Style\TopDown;
use function Jass\Trick\byShortcuts;
use PHPUnit\Framework\TestCase;

class TeamOnlySuitsTest extends TestCase
{
    public function testRecognizesTeamOnlySuits()
    {
        $players = byNames('ueli,hans,fridolin,paul');
        $trick = byShortcuts($players, 'sa, o6, s6, b6');
        $style = new TopDown();

        $ueli = $players[0];

        TeamOnlySuits::seeTrick($ueli, $trick, $style);

        $this->assertContains(Suit::SHIELD, $ueli->brain[TeamOnlySuits::TEAM_ONLY_SUITS]);
    }
}