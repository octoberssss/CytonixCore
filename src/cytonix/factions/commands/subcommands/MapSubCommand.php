<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;

class MapSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("map", "Show the claims surrounding you");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $baseX = $sender->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $baseZ = $sender->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        $map = [];
        $mgr = Manager::getClaimManager();
        $faction = Manager::getSessionManager()->getSession($sender)->getFaction();
        $world = $sender->getWorld()->getDisplayName();
        for ($x2 = -5; $x2 <= 5; $x2++) {
            for ($z2 = -5; $z2 <= 5; $z2++) {
                $x = $baseX + $x2;
                $z = $baseZ + $z2;
                if ($x == $baseX && $z == $baseZ) {
                    $map[] = "§e[+]";
                    continue;
                }
                if (is_null($name = $mgr->getClaim($world, $x, $z))) {
                    $map[] = "§7[-]";
                    continue;
                }
                if ($name == $faction) {
                    $map[] = "§a[+]";
                    continue;
                }
                $map[] = "§c[-]";
            }
        }
        $sender->sendMessage("§e[+]§7: §fYou §7[-]: §fWilderness §a[+]§7: §fYour faction §c[-]§7: §fEnemies");
        $map = array_chunk($map, 11);
        foreach($map as $subMap) {
            $sender->sendMessage(join("", $subMap));
        }
    }

}