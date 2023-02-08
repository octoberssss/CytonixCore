<?php namespace cytonix\factions;

use cytonix\factions\commands\FactionCommand;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use JsonException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class FactionManager {

    /** @var Config */
    private Config $db;

    /** @var array<string, Faction> */
    private array $factions = [];

    /** @var int|mixed */
    public static int $maxMembers;

    /** @var int|mixed */
    public static int $maxClaims;

    /** @var int|mixed */
    public static int $maxFactionHomes;

    /** @var int|mixed */
    public static int $maxAllies;

    /** @var int|mixed */
    public static int $maxHomes;

    public function __construct() {
        $this->db = new Config(Main::getInstance()->getDataFolder() . "factions.yml", Config::YAML);
        foreach($this->db->getAll() as $name => $data) {
            $this->factions[$name] = new Faction(
                $name,
                $data["owner"],
                $data["captains"],
                $data["members"],
                $data["power"],
                $data["homes"],
                $data["invites"],
                $data["allies"],
                $data["allyRequests"]
            );
        }
        $server = Server::getInstance();
        $server->getPluginManager()->registerEvents(new FactionListener(), Main::getInstance());
        $server->getCommandMap()->register("CytonixCore", new FactionCommand());
        $main = Main::getInstance();
        $all = $main->getCachedConfig();
        self::$maxAllies = $all["max-allies"];
        self::$maxClaims = $all["max-claims"];
        self::$maxFactionHomes = $all["max-faction-homes"];
        //self::$maxHomes = $all["max-homes"];
        self::$maxMembers = $all["max-members"];
    }

    public function createFaction(Player $owner, string $name) : void {
        $this->factions[$name] = new Faction(
            $name,
            $owner->getName(),
            [],
            [],
            50,
            [],
            [],
            [],
            []
        );
    }

    /*** @throws JsonException */
    public function saveAllFactions() : void {
        foreach($this->factions as $name => $faction) {
            $arr = $faction->returnStorableArray();
            $this->db->set($name, $arr);
        }
        $this->db->save();
    }

    public function factionExists(string $name) : bool {
        return isset($this->factions[$name]);
    }

    public function getFactionFromName(string $name) : ?Faction {
        if (!isset($this->factions[$name])) {
            return null;
        }
        return $this->factions[$name];
    }

    public function delFaction(string $name) : void {
        $faction = $this->factions[$name];
        foreach($faction->getOnlineMembers() as $member) {
            $member->sendMessage(FormatUtils::PREFIX_BAD . "Your faction was disbanded.");
            Manager::getSessionManager()->getSession($member)->setFaction("");
        }
        Manager::getClaimManager()->removeAllClaimsOwnedBy($name);
        unset($this->factions[$name]);
    }

}