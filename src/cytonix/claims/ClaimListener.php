<?php namespace cytonix\claims;

use cytonix\Manager;
use cytonix\utils\FormatUtils;
use cytonix\utils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\format\Chunk;

class ClaimListener implements Listener {

    /** @var array<string, string> */
    private array $lastClaim = [];

    public function onMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        $chunkX = $to->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $chunkZ = $to->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        if (
            $chunkX == ($from->getFloorX() >> Chunk::COORD_BIT_SIZE) &&
            $chunkZ == ($from->getFloorZ() >> Chunk::COORD_BIT_SIZE)
        ) {
            return;
        }
        if (!isset($this->lastClaim[$player->getName()])) {
            $this->lastClaim[$player->getName()] = "";
        }
        if (is_null($claim = Manager::getClaimManager()->getClaim($to->getWorld()->getDisplayName(), $chunkX, $chunkZ))) {
            if ($this->lastClaim[$player->getName()] !== "") {
                ServerUtils::jukeboxPlayer($player, FormatUtils::TIP_PREFIX_GOOD . "Entering wilderness.");
            }
            $this->lastClaim[$player->getName()] = "";
            return;
        }
        if ($this->lastClaim[$player->getName()] == $claim) {
            return;
        }
        $this->lastClaim[$player->getName()] = $claim;
        ServerUtils::jukeboxPlayer($player, FormatUtils::TIP_PREFIX_GOOD . "Entering " . $claim . "'s territory.");
    }

}