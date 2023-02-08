<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RejectAllySubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("rejectally", "Reject a ally request");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("faction"));
    }

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
        if ($faction->getOwner() !== $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only the owner can reject ally requests.");
            return;
        }
        if (!$faction->hasAllyRequest($args["faction"])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no ally request from that faction.");
            return;
        }
        $faction->removeAllyRequest($args["faction"]);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Rejected ally request.");
    }

}