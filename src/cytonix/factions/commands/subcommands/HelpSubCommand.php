<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;

class HelpSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("help", "Show commands and what they do.");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $sender->sendMessage("§7»----------------------------------------«");
        $sender->sendMessage("§r§3Faction Help");
        $help = array_map(
            fn(BaseSubCommand $command) => "§r§7 » §f" . $command->getName() . ": §3" . $command->getDescription(),
            $this->parent->getSubCommands()
        );
        $sender->sendMessage(join("\n", $help));
        $sender->sendMessage("§7»----------------------------------------«");
    }

}