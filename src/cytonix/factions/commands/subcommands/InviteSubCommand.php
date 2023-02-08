<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class InviteSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("invite", "Invite a player to your faction");
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
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getFaction() == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to do this.");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args["player"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Could not find that player.");
            return;
        }
        if ($player->getName() == $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot invite yourself.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You do not have permission to invite players.");
            return;
        }
        $faction->invitePlayer($player);
        $player->sendMessage(FormatUtils::PREFIX_GOOD . "You have been invite to the faction \"" . $faction->getName() . "\".");
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully invited player \"" . $player->getName() . "\".");
        $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "\"" . $sender->getName() . "\" has invited \"" . $player->getName() . "\" to the faction.");
    }

}