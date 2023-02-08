<?php namespace cytonix\factions\commands;

use CortexPE\Commando\BaseCommand;
use cytonix\factions\commands\subcommands\AllySubCommand;
use cytonix\factions\commands\subcommands\ChatSubCommand;
use cytonix\factions\commands\subcommands\ClaimSubCommand;
use cytonix\factions\commands\subcommands\CreateSubCommand;
use cytonix\factions\commands\subcommands\DelHomeSubCommand;
use cytonix\factions\commands\subcommands\DemoteSubCommand;
use cytonix\factions\commands\subcommands\DisbandSubCommand;
use cytonix\factions\commands\subcommands\HelpSubCommand;
use cytonix\factions\commands\subcommands\HomeSubCommand;
use cytonix\factions\commands\subcommands\InfoSubCommand;
use cytonix\factions\commands\subcommands\InviteSubCommand;
use cytonix\factions\commands\subcommands\JoinSubCommand;
use cytonix\factions\commands\subcommands\KickSubCommand;
use cytonix\factions\commands\subcommands\LeaveSubCommand;
use cytonix\factions\commands\subcommands\MapSubCommand;
use cytonix\factions\commands\subcommands\PromoteSubCommand;
use cytonix\factions\commands\subcommands\RejectAllySubCommand;
use cytonix\factions\commands\subcommands\RemoveAllySubCommand;
use cytonix\factions\commands\subcommands\SetHomeSubCommand;
use cytonix\factions\commands\subcommands\SetOwnerSubCommand;
use cytonix\factions\commands\subcommands\UnClaimSubCommand;
use cytonix\factions\commands\subcommands\UnInviteSubCommand;
use cytonix\Main;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;

class FactionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "f", "Factions command", ["factions"]);
    }

    public function prepare() : void {
        $this->registerSubCommand(new CreateSubCommand());
        $this->registerSubCommand(new InviteSubCommand());
        $this->registerSubCommand(new JoinSubCommand());
        $this->registerSubCommand(new UnInviteSubCommand());
        $this->registerSubCommand(new ClaimSubCommand());
        $this->registerSubCommand(new UnClaimSubCommand());
        $this->registerSubCommand(new PromoteSubCommand());
        $this->registerSubCommand(new DemoteSubCommand());
        $this->registerSubCommand(new KickSubCommand());
        $this->registerSubCommand(new DisbandSubCommand());
        $this->registerSubCommand(new SetOwnerSubCommand());
        $this->registerSubCommand(new SetHomeSubCommand());
        $this->registerSubCommand(new DelHomeSubCommand());
        $this->registerSubCommand(new HomeSubCommand());
        $this->registerSubCommand(new HelpSubCommand());
        $this->registerSubCommand(new MapSubCommand());
        $this->registerSubCommand(new ChatSubCommand());
        $this->registerSubCommand(new AllySubCommand());
        $this->registerSubCommand(new RejectAllySubCommand());
        $this->registerSubCommand(new RemoveAllySubCommand());
        $this->registerSubCommand(new InfoSubCommand());
        $this->registerSubCommand(new LeaveSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please run \"/f help\" for a list of commands.");
    }

}