<?php

namespace Tests;


use Jass\Entity\Game;
use Jass\Message\PlayerSetup;
use Jass\MessageHandler;
use function Jass\Player\byNames;
use PHPUnit\Framework\TestCase;

class MessageHandlerTest extends TestCase
{
    public function testPlayerSetupMessage()
    {
        $game = new Game();
        $sut = new MessageHandler();

        $message = new PlayerSetup();
        $message->players = byNames('1, 2, 3, 4');
        $message->starter = $message->players[0];

        $game = $sut->handle($game, $message);

        $this->assertEquals('1', $game->players[0]->name);
    }
}