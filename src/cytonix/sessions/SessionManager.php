<?php namespace cytonix\sessions;

use cytonix\Main;
use cytonix\sessions\commands\AddPermissionCommand;
use cytonix\sessions\commands\RemovePermissionCommand;
use JsonException;
use pocketmine\player\Player;
use pocketmine\Server;

class SessionManager {

    /** @var array<string, PlayerSession> */
    private array $sessions = [];

    public function __construct() {
        $server = Server::getInstance();
        $server->getPluginManager()->registerEvents(new SessionListener(), Main::getInstance());
        $server->getCommandMap()->registerAll("CytonixCore", [
            new AddPermissionCommand(),
            new RemovePermissionCommand()
        ]);
    }

    public function createSession(Player $player) : void {
        $this->sessions[$player->getName()] = new PlayerSession($player);
    }

    /*** @throws JsonException */
    public function closeSession(Player $player) : void {
        $this->sessions[$player->getName()]->close();
        unset($this->sessions[$player->getName()]);
    }

    public function getSession(Player $player) : PlayerSession {
        return $this->sessions[$player->getName()];
    }

}