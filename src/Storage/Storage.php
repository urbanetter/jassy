<?php

namespace Jass\Storage;


use Jass\Message\Message;

interface Storage
{
    /**
     * @param string $gameName
     * @return Message[]
     */
    public function messagesOfGame(string $gameName);

    public function recordMessage(string $gameName, Message $message);
}