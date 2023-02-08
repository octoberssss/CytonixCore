<?php namespace cytonix\modules\gems;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class GemListener implements Listener {

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if (!is_null($type = $item->getNamedTag()->getTag("gemType"))) {
            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
            $items = GemManager::getItems($type->getValue());
            foreach($items as $item) {
                if (!$player->getInventory()->canAddItem($item)) {
                    $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
                    continue;
                }
                $player->getInventory()->addItem($item);
            }
        }
    }

}