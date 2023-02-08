<?php namespace cytonix\claims;

use cytonix\Main;
use JsonException;
use pocketmine\Server;
use pocketmine\utils\Config;

class ClaimManager {

    /** @var Config */
    private Config $db;

    /** @var array<string, array<string>> */
    private array $claims = [];

    public const CLAIMABLE_WORLDS = ["world"];

    public function __construct() {
        $this->db = new Config(Main::getInstance()->getDataFolder() . "claims.yml", Config::YAML);
        foreach($this->db->getAll() as $world => $claims) {
            $this->claims[$world] = [];
            foreach($claims as $pos => $owner) {
                $this->claims[$world][$pos] = $owner;
            }
        }
        Server::getInstance()->getPluginManager()->registerEvents(new ClaimListener(), Main::getInstance());
    }

    public function deleteClaim(string $world, int $chunkX, int $chunkZ) : void {
        if (!isset($this->claims[$world][$chunkX . ":" . $chunkZ])) {
            return;
        }
        unset($this->claims[$world][$chunkX . ":" . $chunkZ]);
    }

    public function createClaim(string $world, int $chunkX, int $chunkZ, string $owner) : void {
        $this->claims[$world][$chunkX . ":" . $chunkZ] = $owner;
    }

    public function getClaim(string $world, int $chunkX, int $chunkZ) : ?string {
        if (!isset($this->claims[$world]) || !isset($this->claims[$world][$chunkX . ":" . $chunkZ])) {
            return null;
        }
        return $this->claims[$world][$chunkX . ":" . $chunkZ];
    }

    /*** @throws JsonException */
    public function saveAllClaims() : void {
        foreach($this->claims as $world => $claims) {
            $list = [];
            foreach($claims as $pos => $owner) {
                $list[$pos] = $owner;
            }
            $this->db->set($world, $list);
        }
        $this->db->save();
    }

    public function removeAllClaimsOwnedBy(string $name) : void {
        foreach($this->claims as $world => $claims) {
            foreach($claims as $claim => $owner) {
                if ($owner !== $name) {
                    continue;
                }
                unset($this->claims[$world][$claim]);
            }
        }
    }

    public function getClaimCount(string $faction) : int {
        $count = 0;
        foreach($this->claims as $claims) {
            foreach($claims as $owner) {
                if ($owner !== $faction) {
                    continue;
                }
                $count++;
            }
        }
        return $count;
    }

}