<?php namespace cytonix\ranks\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SetRankCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "setrank", "Set a players rank");
        $this->setPermission("cytonix.ranks");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("rank"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args["player"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no player with that name.");
            return;
        }
        if (is_null(Manager::getRankManager()->getRankFromName($rank = $args["rank"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no rank with that name.");
            return;
        }
        Manager::getSessionManager()->getSession($player)->setRank($rank);
        $player->sendMessage(FormatUtils::PREFIX_GOOD . "Your rank has been changed to: " . $rank . ".");
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully set rank.");
    }

}