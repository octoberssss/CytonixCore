<?php namespace cytonix\sessions;

use cytonix\factions\Faction;
use cytonix\Main;
use cytonix\Manager;
use JsonException;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class PlayerSession {

    public const CHAT_MODE_NORMAL = 0;

    public const CHAT_MODE_FACTION = 1;

    public const CHAT_MODE_ALLIES = 2;

    /** @var Player */
    private Player $player;

    /** @var Config */
    private Config $db;

    /** @var string */
    private string $faction;

    /** @var int */
    private int $kills;

    /** @var int */
    private int $deaths;

    /** @var int */
    private int $killStreak;

    /** @var int */
    private int $bestKillStreak;

    /** @var string */
    private string $rank;

    /** @var array<string> */
    private array $permissions;

    /** @var int */
    private int $chatMode = 0;

    /** @var array<string, int> */
    private array $kitCoolDowns;

    /** @var array<PermissionAttachment> */
    private array $attachments = [];

    /** @var int */
    private int $combatTag = 0;

    /** @var array<string> */
    private array $codesClaimed;

    /** @var array */
    private array $crateKeys;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->faction = "";
        $this->db = new Config(Main::getInstance()->getDataFolder() . "players.yml", Config::YAML);
        if ($this->db->exists($player->getName())) {
            foreach($this->db->get($player->getName()) as $key => $value) {
                $this->$key = $value;
            }
        } else {
            $this->faction = "";
            $this->kills = 0;
            $this->deaths = 0;
            $this->killStreak = 0;
            $this->bestKillStreak = 0;
            $this->rank = "Guest";
            $this->permissions = [];
            $this->kitCoolDowns = [];
            $this->crateKeys = [];
            $this->codesClaimed = [];
        }
        if (
            !Manager::getFactionManager()->factionExists($this->faction) ||
            !Manager::getFactionManager()->getFactionFromName($this->faction)->isInFaction($this->player->getName())
        ) {
            $this->faction = "";
        }
        $this->setPermissions();
    }

    /*** @throws JsonException */
    public function close() : void {
        if ($this->combatTag > 0) {
            $this->player->kill();
        }
        $this->db->set($this->player->getName(), [
            "faction" => $this->faction,
            "kills" => $this->kills,
            "deaths" => $this->deaths,
            "killStreak" => $this->killStreak,
            "bestKillStreak" => $this->bestKillStreak,
            "rank" => $this->rank,
            "permissions" => $this->permissions,
            "kitCoolDowns" => $this->kitCoolDowns,
            "codesClaimed" => $this->codesClaimed,
            "crateKeys" => $this->crateKeys
        ]);
        $this->db->save();
    }

    public function getFaction() : string {
        return $this->faction;
    }

    public function setRank(string $group) : void {
        $this->rank = $group;
        $this->setPermissions();
    }

    public function delPermission(string $permission) : void {
        $this->permissions = array_diff($this->permissions, [$permission]);
        $this->setPermissions();
    }

    public function hasPermission(string $permission) : bool {
        return in_array($permission, $this->permissions);
    }

    public function addPermission(string $permission) : void {
        $this->permissions[] = $permission;
        $this->setPermissions();
    }

    public function setPermissions() : void {
        foreach($this->attachments as $attachment) {
            $this->player->removeAttachment($attachment);
        }
        foreach(array_merge($this->permissions, Manager::getRankManager()->getRankFromName($this->rank)->getPermissions()) as $permission) {
            $this->attachments[] = $this->player->addAttachment(Main::getInstance(), $permission, true);

        }
        $this->player->recalculatePermissions();
    }

    public function getFactionObject() : ?Faction {
        $mgr = Manager::getFactionManager();
        if (!$mgr->factionExists($this->faction)) {
            return null;
        }
        return $mgr->getFactionFromName($this->faction);
    }

    public function getKills() : int {
        return $this->kills;
    }

    public function getDeaths() : int {
        return $this->deaths;
    }

    public function getKillStreak() : int {
        return $this->killStreak;
    }

    public function setFaction(string $name) : void {
        $this->faction = $name;
    }

    public function setChatMode(int $mode) : void {
        $this->chatMode = $mode;
    }

    public function getChatMode() : int {
        return $this->chatMode;
    }

    public function getRankFormat() : string {
        return Manager::getRankManager()->getRankFromName($this->rank)->getFancyName();
    }

    public function setKitCoolDown(string $name) : void {
        $this->kitCoolDowns[$name] = time();
    }

    public function isOnKitCoolDown(string $name, int $coolDownLength) : bool {
        if (!isset($this->kitCoolDowns[$name])) {
            return false;
        }
        return (time() - $this->kitCoolDowns[$name]) < $coolDownLength;
    }

    public function getKitTimeLeft(string $kit, int $coolDownLength) : int {
        if (!isset($this->kitCoolDowns[$kit])) {
            return 0;
        }
        $length = $coolDownLength - (time() - $this->kitCoolDowns[$kit]);
        return $length < 1 ? 0 : $length;
    }

    public function getCombatTag() : int {
        return $this->combatTag;
    }

    public function setCombatTag(int $new) : void {
        $this->combatTag = $new;
    }

    public function hasCodeClaimed(string $code) : bool {
        return in_array($code, $this->codesClaimed);
    }

    public function addCodeClaimed(string $code) : void {
        $this->codesClaimed[] = $code;
    }

    public function getKeys(string $crate) : int {
        if (!isset($this->crateKeys[$crate])) {
            return 0;
        }
        return $this->crateKeys[$crate];
    }

    public function addKeys(string $crate, int $keys = 1) : void {
        $this->crateKeys[$crate] = isset($this->crateKeys[$crate]) ? $this->crateKeys[$crate] + $keys : $keys;
    }

}