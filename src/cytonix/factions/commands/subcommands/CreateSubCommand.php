<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\utils\FormatUtils;
use cytonix\Manager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("create", "Create a faction");
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
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getFaction() !== "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You are already in a faction.");
            return;
        }
        $name = $args["name"];
        if (!ctype_alpha($name)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Faction names can only contain real characters.");
            return;
        }
        $len = strlen($name);
        if ($len < 3 || $len > 12) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Faction names must be more than two (2), and less than 13 (13) characters. Yours was " . $len . " characters long.");
            return;
        }
        $mgr = Manager::getFactionManager();
        if ($mgr->factionExists($name)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is already a faction with this name.");
            return;
        }
        $session->setFaction($name);
        $mgr->createFaction($sender, $name);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully created faction.");
    }

}