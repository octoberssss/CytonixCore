<?php namespace cytonix\modules\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class TestCommand extends Command {

    public function __construct() {
        parent::__construct("test", "not for use in production.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Server::getInstance()->isOp($sender->getName())) {
            return;
        }

    }

}