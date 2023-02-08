<?php namespace cytonix\enchants\pickaxe;

use cytonix\enchants\types\CytonixPickaxeEnchant;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class RelicFinderEnchant extends CytonixPickaxeEnchant {

    public function getDescription() : string {
        return "Whilst mining, your chance of finding a relic is greatly boosted!";
    }

    public function isApplicableTo(Item $item) : bool {
        return $this->isPickaxe($item);
    }

    public function canBeAppliedTo() : string {
        return "Pickaxes";
    }

    public function onBreak(Player $player, Block $block, int $level) : void {
        $rand = mt_rand(0, 25);
        if ($rand < 1) {
            $player->sendMessage(" §r§c* §eRelic Finder! §7(+1 Relic)");
            $player->getInventory()->addItem(VanillaItems::BONE()->setCustomName("totally a relic"));
        }
    }

}