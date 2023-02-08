<?php namespace cytonix\modules\gems\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\modules\gems\GemManager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class GemGiveCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "givegem", "Give a gem");
        $this->setPermission("cytonix.gems");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("type"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args["player"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no player with that name.");
            return;
        }
        switch($args["type"]) {
            case "betaGem"://»§
                $gem = GemManager::createGem("§r§l§3BETA §r§fGem", [
                    "§r§3From The BETA Gem, You Might Get: ",
                    " §r§7» §fNothing good, because its BETA"
                ], "betaGem");
                $player->getInventory()->addItem($gem);
                break;
            default:
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no gem with that name.");
        }
    }

}
