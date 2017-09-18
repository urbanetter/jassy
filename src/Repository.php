<?php

namespace Jass;


use Jass\Entity\Game;
use Jass\Message\Message;
use Jass\Storage\Storage;

class Repository
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var MessageHandler
     */
    private $messageHandler;

    /**
     * @param Storage $storage
     * @param MessageHandler $messageHandler
     */
    public function __construct(Storage $storage, MessageHandler $messageHandler)
    {
        $this->storage = $storage;
        $this->messageHandler = $messageHandler;
    }

    public function loadGame(string $name) : Game
    {
        $game = new Game();

        $messages = $this->storage->messagesOfGame($name);

        foreach ($messages as $message) {
            $game = $this->messageHandler->handle($game, $message);
        }

        return $game;
    }

    public function recordMessage(Game $game, Message $message) : Game
    {
        $this->storage->recordMessage($game->name, $message);
        return $this->messageHandler->handle($game, $message);
    }

}