<?php namespace cytonix\sessions\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class RemovePermissionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "removepermission", "Remove a permission from a player.");
        $this->setPermission("cytonix.permissions");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(0, new RawStringArgument("permission"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args["player"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Could not find player with that name.");
            return;
        }
        $permission = $args["permission"];
        $session = Manager::getSessionManager()->getSession($player);
        if (!$session->hasPermission($permission)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This player doesn't have that permission.");
            return;
        }
        $session->delPermission($permission);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Removed permission.");
    }

}