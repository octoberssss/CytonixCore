<?php namespace cytonix\crates\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AddCrateCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "addcrate", "Add a crate");
        $this->setPermission("cytonix.crates");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new RawStringArgument("description"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        $mgr = Manager::getCratesManager();
        if ($mgr->crateExists($args["name"])) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is already a crate with this name.");
            return;
        }
        $mgr->addCrate($sender, $sender->getPosition(), $args["name"], $args["description"]);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Crate added.");
    }

}