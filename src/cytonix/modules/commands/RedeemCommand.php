<?php namespace cytonix\modules\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class RedeemCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "redeem", "Redeem a code");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("code"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        $codes = [
            "code" => [
                VanillaItems::BOOK()->setCustomName("reward")
            ]
        ];
        $code = $args["code"];
        if (!isset($codes[$code])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This code doesn't exist.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->hasCodeClaimed($code)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You have already redeemed this code.");
            return;
        }
        $session->addCodeClaimed($code);
        foreach($codes[$code] as $item) {
            $sender->getInventory()->addItem($item);
        }
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "You have claimed the code \"" . $code . "\".");
    }

}