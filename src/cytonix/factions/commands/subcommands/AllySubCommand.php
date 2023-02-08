<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\factions\FactionManager;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AllySubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("ally", "Request a faction to ally");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("faction"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $sFaction = Manager::getSessionManager()->getSession($sender)->getFaction();
        if ($sFaction == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to make a faction ally.");
            return;
        }
        if (is_null($faction = Manager::getFactionManager()->getFactionFromName($args["faction"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no faction with that name.");
            return;
        }
        $sFaction = Manager::getFactionManager()->getFactionFromName($sFaction);
        if ($sFaction->getOwner() !== $sender->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Only faction owners can send ally requests.w");
            return;
        }
        if ($sFaction->getName() == $faction->getName()) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot ally yourself.");
            return;
        }
        if ($sFaction->hasAllyRequest($faction->getName())) {
            if (count($sFaction->getAllyObjects()) >= FactionManager::$maxAllies) {
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "Your faction is at its max allies.");
                return;
            }
            if (count($faction->getAllyObjects()) >= FactionManager::$maxAllies) {
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "That faction is at its max allies.");
                return;
            }
            $sFaction->addAlly($faction->getName());
            $faction->addAlly($sFaction->getName());
            $sFaction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "Allied to \"" . $faction->getName() . "\".");
            $faction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "Allied to \"" . $sFaction->getName() . "\".");
            $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Successfully allied to " . $sFaction->getName() . ".");
            return;
        }
        if ($faction->hasAllyRequest($sFaction->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This faction already has an ally request from you.");
            return;
        }
        $faction->addAllyRequest($sFaction->getName());
        $sender->sendMessage(FormatUtils::PREFIX_BAD . "Ally request sent.");
        $sFaction->messageFaction(FormatUtils::FAC_PREFIX_GOOD . "You have received a ally request from \"" . $faction->getName() . "\".");
    }

}