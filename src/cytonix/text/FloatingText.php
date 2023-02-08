<?php namespace cytonix\text;

use pocketmine\player\Player;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;

class FloatingText {

    /** @var callable */
    private $getText;

    /** @var FloatingTextParticle */
    private FloatingTextParticle $particle;

    /** @var Position */
    private Position $pos;

    public function __construct(callable $getText, Position $pos) {
        $this->getText = $getText;
        $this->pos = $pos;
        $this->particle = new FloatingTextParticle("", "");
    }

    public function getWorld() : string {
        return $this->pos->getWorld()->getDisplayName();
    }

    public function setTextFor(Player $player) : void {
        $this->particle->setText(($this->getText)($player));
        $this->pos->getWorld()->addParticle($this->pos->asVector3(), $this->particle, [$player]);
    }

    public function removeTextFor(Player $player) : void {
        $this->particle->setText("");
        $this->pos->getWorld()->addParticle($this->pos->asVector3(), $this->particle, [$player]);
    }

}