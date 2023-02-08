<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\factions\FactionManager;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetHomeSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("sethome", "Set a faction home");
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
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to do this.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You do not have permission to set a faction home.");
            return;
        }
        if ($faction->getHomeCount() >= FactionManager::$maxFactionHomes) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Your faction is at its max homes.");
            return;
        }
        $name = $args["name"];
        if (!is_null($faction->getHome($name))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is already a home with this name.");
            return;
        }
        $faction->setHome($name, $sender->getPosition());
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Set a faction home.");
    }

}