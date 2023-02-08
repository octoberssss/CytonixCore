<?php namespace cytonix\crates\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class GiveKeyCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "givekey", "Give keys to a player.");
        $this->setPermission("cytonix.crates");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("crate"));
        $this->registerArgument(2, new RawStringArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args["player"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no player with that name.");
            return;
        }
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully added keys.");
        Manager::getSessionManager()->getSession($player)->addKeys($args["crate"], $args["amount"]);
        Manager::getFloatingTextManager()->loadTexts($player);
    }

}