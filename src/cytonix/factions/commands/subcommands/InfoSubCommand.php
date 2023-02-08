<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\factions\Faction;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("info", "Check the info of a faction");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("faction"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (is_null($faction = Manager::getFactionManager()->getFactionFromName($args["faction"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no faction with that name.");
            return;
        }
        $online = implode(", ", array_map(fn(Player $player) => $player->getName(), $faction->getOnlineMembers()));
        $captains = implode(", ", $faction->getCaptains());
        $members = implode(", " , $faction->getMembers());
        $allies = implode(", ", array_map(fn(Faction $fac) => $fac->getName(), $faction->getAllyObjects()));
        $message = [
            "§7»----------------------------------------«",
            "§r§3" . $faction->getName() . "'s Info",
            "§r§7 » §fOwner: §3" . $faction->getOwner(),
            "§r§7 » §fCaptains: §3" . ($captains == "" ? "None" : $captains),
            "§r§7 » §fMembers: §3" . ($members == "" ? "None" : $members),
            "§r§7 » §fOnline: §3" . ($online == "" ? "None" : $online),
            "§r§7 » §fPower: §3" . $faction->getPower(),
            "§r§7 » §fClaims: §3" . $faction->getClaimCount(),
            "§r§7 » §fAllies: §3" . ($allies == "" ? "None" : $allies),
            "§7»----------------------------------------«"
        ];
        $sender->sendMessage(join("\n", $message));
    }

}