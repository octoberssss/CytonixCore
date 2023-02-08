<?php namespace cytonix\modules\teleportation;

use cytonix\utils\FormatUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FlyCommand extends Command {

    public function __construct() {
        parent::__construct("fly", "Use this command to fly!");
        $this->setPermission("cytonix.fly");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        if ($sender->isFlying()) {
            $sender->setFlying(false);
            $sender->sendMessage(FormatUtils::PREFIX_GOOD . "You have disabled fly.");
            return;
        }
        $sender->setFlying(true);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "You are now flying.");
    }

}