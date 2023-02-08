<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\Manager;
use cytonix\sessions\PlayerSession;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ChatSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("chat", "Change your chat mode");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($sender);
        $session->setChatMode($session->getChatMode() + 1);
        if ($session->getChatMode() > 2) {
            $session->setChatMode(0);
        }
        $sender->sendMessage(match($session->getChatMode()) {
            PlayerSession::CHAT_MODE_NORMAL => FormatUtils::PREFIX_GOOD . "Chat mode set to: public.",
            PlayerSession::CHAT_MODE_FACTION => FormatUtils::PREFIX_GOOD . "Chat mode set to: faction.",
            PlayerSession::CHAT_MODE_ALLIES => FormatUtils::PREFIX_GOOD . "Chat mode set to: allies."
        });
    }

}