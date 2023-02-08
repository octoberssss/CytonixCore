<?php namespace cytonix\crates;

use cytonix\Manager;
use cytonix\utils\FormatUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class CrateListener implements Listener {

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (!is_null($crate = Manager::getCratesManager()->getCrateAt($block->getPosition()))) {
            $event->cancel();
            if ($event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
                $menu->send($player, $crate->getName() . " Rewards");
                $menu->getInventory()->setContents($crate->getRewards());
                return;
            }
            $session = Manager::getSessionManager()->getSession($player);
            if ($session->getKeys($crate->getName()) < 1) {
                $player->sendMessage(FormatUtils::PREFIX_BAD . "You do not have any keys for this crate.");
                return;
            }
            if ($player->isSneaking()) {
                $amount = 10;
                if ($session->getKeys($crate->getName()) < 10) {
                    $amount = $session->getKeys($crate->getName());
                }
                $player->sendMessage(FormatUtils::PREFIX_GOOD . "You have opened the crate " . $amount . " times!");
                $session->addKeys($crate->getName(), -$amount);
                for ($i = 1; $i <= $amount; $i++) {
                    $player->getInventory()->addItem($crate->getRandomRewards());
                }
                Manager::getFloatingTextManager()->loadTexts($player);
                return;
            }
            $session->addKeys($crate->getName(), -1);
            $player->sendMessage(FormatUtils::PREFIX_GOOD . "You have opened the crate!");
            Manager::getFloatingTextManager()->loadTexts($player);
        }
    }

}