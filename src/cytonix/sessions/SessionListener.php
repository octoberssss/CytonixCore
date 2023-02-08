<?php namespace cytonix\sessions;

use cytonix\Main;
use cytonix\Manager;
use JsonException;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SessionListener implements Listener {

    public function onLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Manager::getSessionManager()->createSession($player);
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $event->setJoinMessage(" §7[§2+§7] §a" . $player->getName());
        $event->getPlayer()->sendMessage(join("\n", [
            "§7»----------------------------------------«",
            "§r§3Cytonix Network",
            "§r§7 » §fWelcome, §3" . $player->getName(),
            "§r§7 » §fDiscord: §3cytonix.net/discord",
            "§r§7 » §fStore: §3cytonix.net/store",
            "§r§7 » §fTwitter: §3@CytonixPE",
            "§r§7 » §fCore Version: §3v" . Main::getInstance()->getDescription()->getVersion(),
            "§7»----------------------------------------«"
        ]));
        Manager::getFloatingTextManager()->loadTexts($player);
        $player->setHealth(20);
        $player->getHungerManager()->setFood(20);
        $player->setImmobile(false);
    }

    /*** @throws JsonException */
    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        $event->setQuitMessage(" §7[§4-§7] §c" . $player->getName());
        Manager::getSessionManager()->closeSession($player);
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        if (($session = Manager::getSessionManager()->getSession($player))->getChatMode() !== 0) {
            return;
        }
        $factionString = "";
        $faction = $session->getFaction();
        if ($faction !== "") {
            $faction = Manager::getFactionManager()->getFactionFromName($faction);
            if ($faction->getOwner() == $player->getName()) {
                $factionString = "§e**" . $faction->getName() . " ";
            } else if ($faction->canInvitePlayer($player->getName())) {
                $factionString = "§f*" . $faction->getName() . " ";
            } else {
                $factionString = "§7" . $faction->getName() . " ";
            }
        }
        $event->setFormat($factionString . $session->getRankFormat() . " §e" . $player->getName() . "§7 » §f" . $event->getMessage());
    }

    public function onTeleport(EntityTeleportEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            Manager::getFloatingTextManager()->loadTexts($entity);
        }
    }

    public function onExhause(PlayerExhaustEvent $event) : void {
        $event->cancel();
    }

    public function onEntityDamage(EntityDamageEvent $event) : void {
        if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
        }
    }

}