<?php
namespace Jass\Style;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Trick;

class Trump extends TopDown
{
    public string $name = "trump";

    public Suit $trumpSuit;

    public function __construct(Suit $trumpSuit)
    {
        $this->trumpSuit = $trumpSuit;
        $this->name .= ' ' . strtolower($trumpSuit->name);
    }

    public function __serialize() : array
    {
        return [
            'name' => $this->name,
            'trumpSuit' => $this->trumpSuit,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->name = $data['name'] ?? 'trump';
        $this->trumpSuit = $data['trumpSuit'] instanceof Suit ? $data['trumpSuit'] : Suit::fromLabel($data['trumpSuit']);
    }

    #[\Override]
    public function orderValue(Card $card, ?Suit $leadingSuit = null) : int
    {
        if ($card->suit === $this->trumpSuit) {
            $result = $card->value->trumpRank();
        } else {
            $result = $card->value->topDownRank();
        }

        // increase value if its the same suit like leading turn
        if ($leadingSuit === $card->suit) {
            $result += 100;
        }

        // increase value if its trump
        if ($this->trumpSuit === $card->suit) {
            $result += 200;
        }

        return $result;

    }

    #[\Override]
    public function points(Card $card) : int
    {
        return $this->trumpSuit === $card->suit
            ? $card->value->trumpPoints()
            : $card->value->topDownPoints();
    }

    #[\Override]
    public function isValidCard(Trick $trick, array $hand, Card $card) : bool
    {
        if (in_array($card, $hand) && $card->suit === $this->trumpSuit) {
            // Todo: do not allow under trumping
            return true; // trump cards are always valid
        }
        return parent::isValidCard($trick, $hand, $card);
    }

}
