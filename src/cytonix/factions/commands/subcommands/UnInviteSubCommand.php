<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class UnInviteSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("uninvite", "Un-invite a player from your faction");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("name"));
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
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You don't have permission to un-invite a player.");
            return;
        }
        $name = $args["name"];
        if (!$faction->hasInvite($name)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "That player doesn't have a invite to the faction.");
            return;
        }
        $faction->unInvite($name);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully un-invited \"" . $name . "\" from your faction.");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "\"" . $sender->getName() . "\" has un-invited \"" . $name . "\" from the faction.");
    }

}