<?php namespace cytonix\enchants;

use cytonix\enchants\types\CytonixPickaxeEnchant;
use cytonix\enchants\types\CytonixSwordEnchant;
use cytonix\enchants\types\CytonixToggleEnchant;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnchantListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        foreach($player->getArmorInventory()->getContents() as $item) {
            foreach($item->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof CytonixToggleEnchant) {
                    $enchantment->onPutOn($player, $item->getEnchantmentLevel($enchantment), false);
                }
            }
        }
        $player->getArmorInventory()->getListeners()->add(new CallbackInventoryListener(
            $run = function(Inventory $inventory, int $slot, Item $old) use($player) : void {
                $newItem = $inventory->getItem($slot);
                if (!$newItem->equals($old, false)) {
                    foreach($newItem->getEnchantments() as $enchantment) {
                        $enchantment = $enchantment->getType();
                        if ($enchantment instanceof CytonixToggleEnchant) {
                            $enchantment->onPutOn($player, $newItem->getEnchantmentLevel($enchantment));
                        }
                    }
                    foreach($old->getEnchantments() as $enchantment) {
                        $enchantment = $enchantment->getType();
                        if ($enchantment instanceof CytonixToggleEnchant) {
                            $enchantment->onTakeOff($player);
                        }
                    }
                }
            },
            function(Inventory $inventory, array $oldContents) use ($player, $run) : void {
                foreach($oldContents as $slot => $item) {
                    if (!$item->equals($inventory->getItem($slot), false)) {
                        $run($inventory, $slot, $item);
                    }
                }
            }
        ));
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if (!$item instanceof Durable) {
            return;
        }
        foreach($item->getEnchantments() as $enchantment) {
            $enchantment = $enchantment->getType();
            if ($enchantment instanceof CytonixPickaxeEnchant) {
                $enchantment->onBreak($player, $event->getBlock(), $item->getEnchantmentLevel($enchantment));
            }
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     * @priority HIGHEST
     */
    public function onHit(EntityDamageByEntityEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            if(!$item instanceof Durable) {
                return;
            }
            foreach($item->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof CytonixSwordEnchant) {
                    $enchantment->onHit($damager, $player, $item->getEnchantmentLevel($enchantment));
                }
            }
        }
    }


}