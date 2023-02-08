<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class KickSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("kick", "Kick a player from your faction");
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
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to use this command.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if ($faction->getOwner() !== $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only the owner can kick members.");
            return;
        }
        $name = $args["player"];
        if ($name == $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot kick yourself.");
            return;
        }
        if (!$faction->isInFaction($name)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This member is not in your faction.");
            return;
        }
        $faction->kick($name);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully kicked member.");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "\"" . $name . "\" has been kicked from the faction.");
        if (!is_null($player = Server::getInstance()->getPlayerExact($name))) {
            Manager::getSessionManager()->getSession($player)->setFaction("");
            $player->sendMessage(FormatUtils::PREFIX_BAD . "You have been kicked from \"" . $faction->getName() . "\".");
        }
    }

}