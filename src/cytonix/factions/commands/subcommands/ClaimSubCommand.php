<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\claims\ClaimManager;
use cytonix\factions\FactionManager;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;

class ClaimSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("claim", "Claim a chunk");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $faction = Manager::getSessionManager()->getSession($sender)->getFaction();
        if ($faction == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to claim land.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only captains, and owners can claim land.");
            return;
        }
        if ($faction->getClaimCount() >= FactionManager::$maxClaims) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Your faction is at its max claims.");
            return;
        }
        $x = $sender->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $y = $sender->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        $world = $sender->getWorld();
        if (!in_array($world->getDisplayName(), ClaimManager::CLAIMABLE_WORLDS)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot claim in this world.");
            return;
        }
        if (!is_null(Manager::getClaimManager()->getClaim($world->getDisplayName(), $x, $y))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Another faction has already claimed this land.");
            return;
        }
        Manager::getClaimManager()->createClaim($world->getDisplayName(), $x, $y, $faction->getName());
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully claimed land.");
        $faction->messageFaction(FormatUtils::PREFIX_GOOD . "\"" . $sender->getName() . "\" has claimed a chunk.");
    }

}