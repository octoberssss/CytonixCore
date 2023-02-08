<?php namespace cytonix\enchants\types;

use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Sword;

abstract class CytonixEnchant extends Enchantment {

    abstract function getDescription() : string;

    abstract function canBeAppliedTo() : string;

    abstract function isApplicableTo(Item $item) : bool;

    public function isSword(Item $item) : bool {
        return $item instanceof Sword;
    }

    public function isPickaxe(Item $item) : bool {
        return $item instanceof Pickaxe;
    }

    public function isArmor(Item $item) : bool {
        return $item instanceof Armor;
    }

    public function isBoots(Item $item) : bool {
        return in_array($item->getId(), [
            ItemIds::LEATHER_BOOTS,
            ItemIds::GOLDEN_BOOTS,
            ItemIds::CHAIN_BOOTS,
            ItemIds::DIAMOND_BOOTS,
            ItemIds::IRON_BOOTS
        ]);
    }

    public function isLeggings(Item $item) : bool {
        return in_array($item->getId(), [
            ItemIds::LEATHER_LEGGINGS,
            ItemIds::GOLDEN_LEGGINGS,
            ItemIds::CHAIN_BOOTS,
            ItemIds::DIAMOND_BOOTS,
            ItemIds::IRON_BOOTS
        ]);
    }

    public function isChestPlate(Item $item) : bool {
        return in_array($item->getId(), [
            ItemIds::LEATHER_CHESTPLATE,
            ItemIds::GOLDEN_CHESTPLATE,
            ItemIds::CHAIN_CHESTPLATE,
            ItemIds::DIAMOND_CHESTPLATE,
            ItemIds::IRON_CHESTPLATE
        ]);
    }

    public function isHelmet(Item $item) : bool {
        return in_array($item->getId(), [
            ItemIds::LEATHER_HELMET,
            ItemIds::GOLDEN_HELMET,
            ItemIds::CHAIN_HELMET,
            ItemIds::DIAMOND_HELMET,
            ItemIds::IRON_HELMET
        ]);
    }

}