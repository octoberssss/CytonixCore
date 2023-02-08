<?php namespace cytonix\crates\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EditCrateRewardsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "editcraterewards", "Edit a crates rewards");
        $this->setPermission("cytonix.crates");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("crate"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        if (!Manager::getCratesManager()->crateExists($type = $args["crate"])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no crate with that name.");
            return;
        }
        Manager::getCratesManager()->getCrate($type)->openRewardsEditorMenu($sender);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Opening menu.");
    }

}