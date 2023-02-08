<?php namespace cytonix\text;

use pocketmine\player\Player;
use pocketmine\world\Position;

class FloatingTextManager {

    /** @var array<FloatingText> */
    private array $texts = [];

    public function loadTexts(Player $player) : void {
        $world = $player->getWorld()->getDisplayName();
        foreach($this->texts as $text) {
            if ($text->getWorld() !== $world) {
                $text->removeTextFor($player);
                continue;
            }
            $text->setTextFor($player);
        }
    }

    public function addText(callable $setText, Position $position) : void {
        $this->texts[] = new FloatingText($setText, $position);
    }

}