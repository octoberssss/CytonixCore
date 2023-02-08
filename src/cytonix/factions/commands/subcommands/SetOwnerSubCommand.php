<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetOwnerSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("owner", "Set the owner of your faction");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
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
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only the faction owner can set the owner of the faction.");
            return;
        }
        if (!$faction->isInFaction($new = $args["player"])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "\"" . $new . "\" is not in your faction.");
            return;
        }
        if ($new == $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot owner yourself.");
            return;
        }
        $faction->setOwner($new);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Faction owner changed successfully.");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "Your faction owner is now \"" . $new . "\".");
    }

}