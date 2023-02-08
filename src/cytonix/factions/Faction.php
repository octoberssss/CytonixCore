<?php namespace cytonix\factions;

use cytonix\Manager;
use cytonix\utils\PositionUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class Faction {

    /** @var string */
    private string $name;

    /** @var string */
    private string $owner;

    /** @var array<string> */
    private array $captains;

    /** @var array<string> */
    private array $members;

    /** @var int */
    private int $power;

    /** @var array<string> */
    private array $homes;

    /** @var array<string> */
    private array $invites;

    /** @var array<string> */
    private array $allies;

    /** @var array<string> */
    private array $allyRequests;

    public function __construct(
        string $name,
        string $owner,
        array $captains,
        array $members,
        int $power,
        array $homes,
        array $invites,
        array $allies,
        array $allyRequests,
    ) {
        $this->name = $name;
        $this->owner = $owner;
        $this->captains = $captains;
        $this->members = $members;
        $this->power = $power;
        $this->homes = $homes;
        $this->invites = $invites;
        $this->allies = $allies;
        $this->allyRequests = $allyRequests;
    }

    public function returnStorableArray() : array {
        return [
            "name" => $this->name,
            "owner" => $this->owner,
            "captains" => $this->captains,
            "members" => $this->members,
            "power" => $this->power,
            "homes" => $this->homes,
            "invites" => $this->invites,
            "allies" => $this->allies,
            "allyRequests" => $this->allyRequests
        ];
    }

    public function getName() : string {
        return $this->name;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getCaptains() : array {
        return $this->captains;
    }

    public function getMembers() : array {
        return $this->members;
    }

    public function getPower() : int {
        return $this->power;
    }

    public function getHome(string $name) : ?Position {
        if (!isset($this->homes[$name])) {
            return null;
        }
        return PositionUtils::stringToPosition($this->homes[$name]);
    }

    public function setHome(string $name, Position $where) : void {
        $this->homes[$name] = PositionUtils::positionToString($where);
    }

    public function delHome(string $name) : void {
        unset($this->homes[$name]);
    }

    public function canInvitePlayer(string|Player $member) : bool {
        if ($member instanceof Player) $member = $member->getName();
        return $this->owner == $member || in_array($member, $this->captains);
    }

    /*** @return array<Player> */
    public function getOnlineMembers() : array {
        $online = [];
        foreach(array_merge([$this->owner], $this->captains, $this->members) as $member) {
            if (is_null($player = Server::getInstance()->getPlayerExact($member))) {
                continue;
            }
            $online[] = $player;
        }
        return $online;
    }

    public function messageFaction(string $message) : void {
        foreach($this->getOnlineMembers() as $member) {
            $member->sendMessage($message);
        }
    }

    public function promoteMember(string $member) : void {
        $this->members = array_diff($this->members, [$member]);
        $this->captains[] = $member;
    }

    public function demoteMember(string $member) : void {
        $this->captains = array_diff($this->captains, [$member]);
        $this->members[] = $member;
    }

    public function setOwner(string|Player $new) : void {
        if ($new instanceof Player) $new = $new->getName();
        if ($this->canInvitePlayer($new)) {
            $this->demoteMember($new);
        }
        $this->members = array_diff($this->members, [$new]);
        $this->members[] = $this->owner;
        $this->owner = $new;
    }

    public function invitePlayer(Player $player) : void {
        $this->invites[] = $player->getName();
    }

    public function hasInvite(string $player) : bool {
        return in_array($player, $this->invites);
    }

    public function unInvite(string $name) : void {
        $this->invites = array_diff($this->invites, [$name]);
    }

    public function acceptInvite(Player $player) : void {
        $this->members[] = $player->getName();
        $this->invites = array_diff($this->invites, [$player->getName()]);
    }

    public function isInFaction(string $name) : bool {
        return in_array($name, $this->members) || in_array($name, $this->captains) || $this->owner == $name;
    }

    public function kick(string $name) : void {
        if (in_array($name, $this->members)) {
            $this->members = array_diff($this->members, [$name]);
            return;
        }
        $this->captains = array_diff($this->captains, [$name]);
    }

    public function hasAllyRequest(string $ally) : bool {
        return in_array($ally, $this->allyRequests);
    }

    public function addAllyRequest(string $request) : void {
        $this->allyRequests[] = $request;
    }

    public function removeAllyRequest(string $name) : void {
        $this->allyRequests = array_diff($this->allyRequests, [$name]);
    }

    public function isAlliedTo(string $faction) : bool {
        return in_array($faction, $this->allies);
    }

    public function addAlly(string $ally) : void {
        if (in_array($ally, $this->allyRequests)) {
            $this->allyRequests = array_diff($this->allyRequests, [$ally]);
        }
        $this->allies[] = $ally;
    }

    public function removeAlly(string $ally) : void {
        $this->allies = array_diff($this->allies, [$ally]);
    }

    /**
     * @return array<Faction>
     */
    public function getAllyObjects() : array {
        $mgr = Manager::getFactionManager();
        $list = [];
        foreach($this->allies as $ally) {
            if (is_null($faction = $mgr->getFactionFromName($ally))) {
                continue;
            }
            $list[] = $faction;
        }
        return $list;
    }

    public function getMemberCount() : int {
        return count($this->captains) + count($this->members) + 1;
    }

    public function getClaimCount() : int {
        return Manager::getClaimManager()->getClaimCount($this->getName());
    }

    public function getHomeCount() : int {
        return count($this->homes);
    }

}