<?php namespace cytonix\enchants\armor;

use cytonix\enchants\types\CytonixToggleEnchant;
use cytonix\utils\ServerUtils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;

class SpeedEnchant extends CytonixToggleEnchant {

    public function getDescription() : string {
        return "When worn, this enchant will grant you speed depending on the level of the enchant.";
    }

    public function canBeAppliedTo() : string {
        return "Boots";
    }

    public function isApplicableTo(Item $item) : bool {
        return $this->isBoots($item);
    }

    public function onPutOn(Player $player, int $level, bool $message = true) : void {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), ServerUtils::INT32_MAX, $level - 1));
        if ($message) {
            $player->sendMessage(" §r§c* §eSpeed activated!");
        }
    }

    public function onTakeOff(Player $player) : void {
        $player->getEffects()->remove(VanillaEffects::SPEED());
    }

}