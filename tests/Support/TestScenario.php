<?php

namespace Tests\Support;

use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\CardSet\byShortcuts;
use function Jass\Game\playCard;
use function Jass\Player\byNames;
use function Jass\Player\nextPlayer;
use Jass\Strategy\Simple;
use function Jass\Strategy\card;
use Jass\Strategy;
use Jass\Style;
use Jass\Style\TopDown;
use Jass\Style\Trump;
use function Jass\Trick\addTurn;
use LogicException;

class TestScenario
{
    private Game $game;

    public function __construct()
    {
        $this->game = new Game();
        $this->game->players = byNames('Ueli, Fritz, Franz, Hans');
        $this->game->style = new TopDown();
        $this->game->currentPlayer = $this->game->players[0];

        foreach ($this->game->players as $player) {
            $player->hand = [];
            $player->strategies = [Simple::class];
        }
    }

    public static function new(): self
    {
        return new self();
    }

    public static function topDown(): self
    {
        return self::new()->style(new TopDown());
    }

    public static function trump(Suit|string $suit): self
    {
        if (is_string($suit)) {
            $suit = Suit::from(strtolower($suit));
        }

        return self::new()->style(new Trump($suit));
    }

    public function style(Style $style): self
    {
        $this->game->style = $style;

        return $this;
    }

    /**
     * @param string|Card[] $cards
     */
    public function hand(int|string $player, string|array $cards): self
    {
        $this->player($player)->hand = is_string($cards) ? byShortcuts($cards) : $cards;

        return $this;
    }

    public function strategies(int|string $player, string ...$strategies): self
    {
        $this->player($player)->strategies = $strategies;

        return $this;
    }

    public function currentPlayer(int|string $player): self
    {
        $this->game->currentPlayer = $this->player($player);

        return $this;
    }

    /**
     * @param array<int|string, string|Card> $turns keyed by player index/name
     */
    public function currentTrick(array $turns): self
    {
        $trick = new Trick();
        $lastPlayer = null;

        foreach ($turns as $player => $card) {
            $lastPlayer = $this->player($player);
            addTurn($trick, $lastPlayer, $this->card($card));
        }

        $this->game->currentTrick = $trick;
        if ($lastPlayer instanceof Player) {
            $this->game->currentPlayer = nextPlayer($lastPlayer, $this->game->players);
        }

        return $this;
    }

    public function play(string|Card $card): self
    {
        playCard($this->game, $card);

        return $this;
    }

    public function chosenCard(int|string|null $player = null): ?Card
    {
        if ($player !== null) {
            $this->currentPlayer($player);
        }

        return card($this->game)?->withoutHint();
    }

    public function chosenShortcut(int|string|null $player = null): ?string
    {
        return $this->chosenCard($player)?->toShortcut();
    }

    public function choose(string $strategyClass, int|string|null $player = null): ?Card
    {
        if ($player !== null) {
            $this->currentPlayer($player);
        }

        $strategy = new $strategyClass();
        if (!$strategy instanceof Strategy) {
            throw new LogicException($strategyClass . ' is not a strategy.');
        }

        return $strategy->chooseCard($this->game)?->withoutHint();
    }

    public function chooseShortcut(string $strategyClass, int|string|null $player = null): ?string
    {
        return $this->choose($strategyClass, $player)?->toShortcut();
    }

    public function isValid(string|Card $card, int|string|null $player = null): bool
    {
        $player = $player === null ? $this->game->currentPlayer : $this->player($player);
        $trick = $this->game->currentTrick ?? new Trick();

        return $this->game->style->isValidCard($trick, $player->hand, $this->card($card));
    }

    public function game(): Game
    {
        return $this->game;
    }

    private function player(int|string $player): Player
    {
        if (is_int($player) && isset($this->game->players[$player])) {
            return $this->game->players[$player];
        }

        foreach ($this->game->players as $candidate) {
            if ($candidate->name === $player) {
                return $candidate;
            }
        }

        throw new LogicException('Unknown player ' . $player);
    }

    private function card(string|Card $card): Card
    {
        return is_string($card) ? Card::shortcut($card) : $card;
    }
}
