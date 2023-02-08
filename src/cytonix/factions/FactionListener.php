<?php namespace cytonix\factions;

use cytonix\claims\ClaimManager;
use cytonix\Manager;
use cytonix\sessions\PlayerSession;
use cytonix\utils\FormatUtils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\format\Chunk;

class FactionListener implements Listener {

    private function processEvent(Event $event, Player $player) : void {
        if (!$event instanceof Cancellable || !in_array($player->getWorld()->getDisplayName(), ClaimManager::CLAIMABLE_WORLDS)) return;
        $pos = null;
        if (
            $event instanceof BlockBreakEvent ||
            $event instanceof BlockPlaceEvent ||
            $event instanceof PlayerInteractEvent
        ) {
            $pos = $event->getBlock()->getPosition();
        } else if ($event instanceof PlayerBucketFillEvent) {
            $pos = $event->getBlockClicked()->getPosition();
        }
        if ($pos == null) {
            return;
        }
        $claim = Manager::getClaimManager()->getClaim(
            $pos->getWorld()->getDisplayName(),
            $pos->asVector3()->getFloorX() >> Chunk::COORD_BIT_SIZE,
            $pos->asVector3()->getFloorZ() >> Chunk::COORD_BIT_SIZE
        );
        if (
            is_null($claim) ||
            $claim == Manager::getFactionManager()->getFactionFromName($claim)->getName()
        ) return;
        $event->cancel();
        $player->sendMessage(FormatUtils::PREFIX_BAD . "You cannot do that here.");
    }

    public function onBreak(BlockBreakEvent $event) : void {
        $this->processEvent($event, $event->getPlayer());
    }

    public function onPlace(BlockPlaceEvent $event) : void {
        $this->processEvent($event, $event->getPlayer());
    }

    public function onInteract(PlayerInteractEvent $event) : void {
        $this->processEvent($event, $event->getPlayer());
    }

    public function onFill(PlayerBucketFillEvent $event) : void {
        $this->processEvent($event, $event->getPlayer());
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if ($player instanceof Player && $damager instanceof Player) {
            $sessionPlayer = Manager::getSessionManager()->getSession($player);
            $sessionDamager = Manager::getSessionManager()->getSession($damager);
            $factionPlayer = $sessionPlayer->getFaction();
            $factionDamager = $sessionDamager->getFaction();
            $factionPObject = Manager::getFactionManager()->getFactionFromName($factionPlayer);
            if (!is_null($factionPObject) && $factionPObject->isAlliedTo($factionDamager)) {
                $damager->sendMessage(FormatUtils::PREFIX_BAD . "You cannot hit faction allies.");
                $event->cancel();
                return;
            }
            if ($factionDamager !== "" && $factionPlayer !== "" && $factionDamager == $factionPlayer) {
                $damager->sendMessage(FormatUtils::PREFIX_BAD . "You cannot hit faction members.");
                $event->cancel();
                return;
            }
            if ($sessionPlayer->getCombatTag() < 1) $player->sendMessage(FormatUtils::PREFIX_BAD . "You have been tagged by " . $damager->getName() . ".");
            if ($sessionDamager->getCombatTag() < 1)$damager->sendMessage(FormatUtils::PREFIX_BAD . "You are now combat tagged.");
            $sessionPlayer->setCombatTag(12);
            $sessionDamager->setCombatTag(12);
            if ($player->isFlying()) $player->setFlying(false);
            if ($damager->isFlying()) $damager->setFlying(false);
        }
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        $session = Manager::getSessionManager()->getSession($player);
        if ($session->getChatMode() < 1) {
            return;
        }
        $faction = $session->getFaction();
        if ($faction == "") {
            $session->setChatMode(PlayerSession::CHAT_MODE_NORMAL);
            return;
        }
        $event->cancel();
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if ($session->getChatMode() == 2) {
            $message = "§7[§eAllies§7] §a" . $player->getName() . "§7: §e" . $event->getMessage();
            foreach($faction->getAllyObjects() as $subFac) {
                $subFac->messageFaction($message);
            }
            $faction->messageFaction($message);
            return;
        }
        $faction->messageFaction("§7[§aFaction§7] §a" . $player->getName() . "§7: §e" . $event->getMessage());
    }

    public function onCommand(CommandEvent $event) : void {
        $player = $event->getSender();
        if(!$player instanceof Player || Manager::getSessionManager()->getSession($player)->getCombatTag() < 1) {
            return;
        }
        $commandLine = $event->getCommand();
        $args = [];
        preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
        foreach($matches[0] as $k => $_) {
            for($i = 1; $i <= 2; ++$i) {
                if($matches[$i][$k] !== '') {
                    $args[$k] = $i === 1 ? stripslashes($matches[$i][$k]) : $matches[$i][$k];
                    break;
                }
            }
        }
        $command = Server::getInstance()->getCommandMap()->getCommand($args[0]);
        if($command !== null) {
            $args[0] = $command->getName();
        }
        $input = strtolower(trim(implode(" ", $args)));
        foreach(["spawn", "fly", "f", "home"] as $command) {
            if(str_starts_with($input, $command)) {
                $event->cancel();
                $player->sendMessage(FormatUtils::PREFIX_BAD . "You cannot run this command whilst combat tagged.");
                return;
            }
        }
    }

}