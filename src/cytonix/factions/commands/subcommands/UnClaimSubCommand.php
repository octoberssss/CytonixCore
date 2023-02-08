<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;

class UnClaimSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("unclaim", "Unclaim a chunk of land");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $faction = Manager::getSessionManager()->getSession($sender)->getFaction();
        if ($faction == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to do this.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be captain or higher to un-claim faction land.");
            return;
        }
        $x = $sender->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $z = $sender->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        if (is_null($claim = Manager::getClaimManager()->getClaim($sender->getWorld()->getDisplayName(), $x, $z))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no claim here.");
            return;
        }
        if ($claim !== $faction->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot unclaim other factions claims.");
            return;
        }
        Manager::getClaimManager()->deleteClaim($sender->getWorld()->getDisplayName(), $x, $z);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully unclaimed land.");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "\"" . $sender->getName() . "\" has unclaimed a chunk.");
    }

}