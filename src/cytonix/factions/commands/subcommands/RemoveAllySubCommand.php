<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RemoveAllySubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("removeally", "Remove an ally from your faction");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("ally"));
    }

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
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only the faction owner can remove an ally.");
            return;
        }
        if (!$faction->isAlliedTo($rem = $args["ally"])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You are not allied to that faction.");
            return;
        }
        if (!is_null($fac = Manager::getFactionManager()->getFactionFromName($rem))) {
            if ($fac->isAlliedTo($faction->getName())) {
                $fac->removeAlly($faction->getName());
            }
        }
        $faction->removeAlly($rem);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Removed ally.");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "Removed ally \"" . $args["ally"] . "\".");
    }

}