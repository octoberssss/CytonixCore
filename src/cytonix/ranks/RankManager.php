<?php namespace cytonix\ranks;

use cytonix\Main;
use cytonix\ranks\commands\SetRankCommand;
use pocketmine\Server;

class RankManager {

    /** @var array<string, Rank> */
    private array $ranks;

    public function __construct() {
        foreach(Main::getInstance()->getCachedConfig()["ranks"] as $name => $data) {
            $this->ranks[$name] = new Rank(
                $name,
                $data["fancyName"],
                $data["permissions"]
            );
        }
        Server::getInstance()->getCommandMap()->registerAll("CytonixCore", [
            new SetRankCommand()
        ]);
    }

    public function getRankFromName(string $name) : ?Rank {
        if (!isset($this->ranks[$name])) {
            return null;
        }
        return $this->ranks[$name];
    }

}