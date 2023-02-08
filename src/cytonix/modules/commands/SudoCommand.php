<?php namespace cytonix\modules\commands;

use cytonix\utils\FormatUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SudoCommand extends Command {

    public function __construct() {
        parent::__construct("sudo", "Base sudo command.");
        $this->setPermission("cytonix.hidden");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof Player && !Server::getInstance()->isOp($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Missing permissions.");
            return;
        }
        if (count($args) < 2) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Usage: /sudo (player) (message)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix(array_shift($args)))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Player not found.");
            return;
        }
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Command executed.");
        $player->chat(join(" ", $args));
    }

}