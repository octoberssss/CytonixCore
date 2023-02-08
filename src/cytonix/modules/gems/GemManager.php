<?php namespace cytonix\modules\gems;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class GemManager {

    /**
     * @param string $name
     * @param array $lore
     * @param string $type
     * @return Item
     */
    public static function createGem(string $name, array $lore, string $type) : Item {
        $gem = VanillaItems::NETHER_STAR();
        $gem->setCustomName($name);
        $gem->setLore($lore);
        $gem->getNamedTag()->setString("gemType", $type);
        $gem->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1), 1));
        return $gem;
    }

    public static function getItems(string $type) : array {
        return [
            "betaGem" => [
                VanillaItems::BOOK()->setCustomName("cool gem thing"),
                VanillaItems::DIAMOND_HELMET()->setCustomName("cool gem thing (2)")
            ]
        ][$type];
    }
    
}