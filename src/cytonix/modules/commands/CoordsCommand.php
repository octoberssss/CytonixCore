<?php namespace cytonix\modules\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;

class CoordsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "coords", "Turn coordinates on or off");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("on/off"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        $pk = new GameRulesChangedPacket();
        if ($args["on/off"] == "off") {
            $pk->gameRules = ["showcoordinates" => new BoolGameRule(false, false)];
            $sender->getNetworkSession()->sendDataPacket($pk);
            $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Disabled coordinates.");
            return;
        }
        $pk->gameRules = ["showcoordinates" => new BoolGameRule(true, false)];
        $sender->getNetworkSession()->sendDataPacket($pk);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Enabled coordinates.");
    }

}