<?php namespace cytonix\enchants\sword;

use cytonix\enchants\types\CytonixSwordEnchant;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\player\Player;

class PoisonEnchant extends CytonixSwordEnchant {

    public function getDescription() : string {
        return "When fighting your enemies, there is a small chance to poison them for a short amount of time.";
    }

    public function canBeAppliedTo() : string {
        return "Weapons";
    }

    public function isApplicableTo(Item $item) : bool {
        return $this->isSword($item);
    }

    public function onHit(Player $player, Entity $hit, int $level) : void {
        if (!$hit instanceof Player) {
            return;
        }
        $rand = mt_rand(0, 10);
        if ($rand < 1) {
            $player->sendMessage("§r§c* §ePoisoned your enemy...");
            $hit->sendMessage("§r§c* You have been poisoned...");
            $hit->getEffects()->add(new EffectInstance(VanillaEffects::POISON(), 20 * 5, 1));
        }
    }

}