<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\factions\FactionManager;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class JoinSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("join", "Join a faction");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("faction"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game");
            return;
        }
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getFaction() !== "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You are already in a faction.");
            return;
        }
        $mgr = Manager::getFactionManager();
        $fac = $args["faction"];
        if (!$mgr->factionExists($name = $fac)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no faction with that name.");
            return;
        }
        $fac = $mgr->getFactionFromName($fac);
        if (!$fac->hasInvite($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You do not have an invite from this faction.");
            return;
        }
        if ($fac->getMemberCount() >= FactionManager::$maxMembers) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This faction is at its max members.");
            return;
        }
        $session->setFaction($name);
        $fac->acceptInvite($sender);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully joined \"" . $name . "\".");
        $fac->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "\"" . $sender->getName() . "\" has joined the faction.");
    }

}