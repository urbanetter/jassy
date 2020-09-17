<?php


namespace Tests;


use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\MessageHandler;
use PHPUnit\Framework\TestCase;
use function Jass\Strategy\card;

class FunctionalTest extends TestCase
{
    private function loadGame(string $name): Game
    {
        $game = new Game();

        $messageHandler = new MessageHandler();

        $pattern = __DIR__ . '/../games/' . $name . '/*';
        $messages = array_map(function ($file) {
            return unserialize(file_get_contents($file));
        }, glob($pattern));

        foreach ($messages as $message) {
            $game = $messageHandler->handle($game, $message);
        }

        return $game;
    }

    public function testAllCardsOfSuitPlayed()
    {
        $game = $this->loadGame('allCardsOfSuit');

        $card = card($game)->withoutHint();
        $this->assertEquals(Card::shortcut('s8'), $card);
    }
}