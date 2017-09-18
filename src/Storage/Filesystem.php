<?php

namespace Jass\Storage;

use Jass\Message\Message;

class Filesystem implements Storage
{

    protected $basisPath;

    /**
     * Filesystem constructor.
     */
    public function __construct()
    {
        $this->basisPath = realpath(__DIR__ . '/../..');
        if (!is_dir($this->basisPath . '/data')) {
            throw new \LogicException('No directory called data in ' . $this->basisPath);
        }
        $this->basisPath .= '/data';
    }


    public function messagesOfGame(string $gameName)
    {
        if (!is_dir($this->basisPath . '/' . $gameName)) {
            throw new \InvalidArgumentException('Unknown game ' . $gameName);
        }
        $files = glob($this->basisPath . '/' . $gameName . '/*');
        $messages = [];
        foreach ($files as $fileName) {
            $content = file_get_contents($fileName);
            $messages[] = unserialize($content);
        }

        return $messages;
    }

    public function recordMessage(string $gameName, Message $message)
    {
        if (!is_dir($this->basisPath . '/' . $gameName)) {
            mkdir($this->basisPath . '/' . $gameName);
        }
        $files = glob($this->basisPath . '/' . $gameName . '/*');
        $fileName = $this->basisPath . '/' . $gameName . '/' . count($files);
        file_put_contents($fileName, serialize($message));
    }
}