<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DisbandSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("disband", "Disband your faction");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $faction = Manager::getSessionManager()->getSession($sender)->getFaction();
        if ($faction == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to use this command.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if ($faction->getOwner() !== $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only the owner can disband the faction.");
            return;
        }
        Manager::getFactionManager()->delFaction($faction->getName());
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Faction disbanded.");
    }

}