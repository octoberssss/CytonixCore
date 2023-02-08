<?php namespace cytonix\crates;

use cytonix\crates\commands\AddCrateCommand;
use cytonix\crates\commands\EditCrateRewardsCommand;
use cytonix\crates\commands\GiveKeyCommand;
use cytonix\crates\commands\RemoveCrateCommand;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\PositionUtils;
use JsonException;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class CratesManager {

    /** @var Config */
    private Config $db;

    /** @var array<string, Crate> */
    private array $crates = [];

    public function __construct() {
        $this->db = new Config(Main::getInstance()->getDataFolder() . "crates.yml", Config::YAML);
        foreach($this->db->getAll() as $name => $data) {
            $this->crates[$name] = new Crate(
                $name,
                $data["description"],
                array_map(fn($item) => Item::jsonDeserialize($item), $data["rewards"]),
                $pos = PositionUtils::stringToPosition($data["position"])
            );
            Manager::getFloatingTextManager()->addText(
                fn(Player $player) => "§r§l§e" . $name . " §r§fCrate\n§r§7Right click to open crate!\n§r§fYour keys: §3" . Manager::getSessionManager()->getSession($player)->getKeys($name),
                new Position($pos->getFloorX() + 0.5, $pos->getFloorY() + 1.5, $pos->getFloorZ() + 0.5, $pos->getWorld())
            );
        }
        Server::getInstance()->getCommandMap()->registerAll("CytonixCore", [
            new AddCrateCommand(),
            new RemoveCrateCommand(),
            new EditCrateRewardsCommand(),
            new GiveKeyCommand()
        ]);
        Server::getInstance()->getPluginManager()->registerEvents(new CrateListener(), Main::getInstance());
    }

    /*** @throws JsonException */
    public function save() : void {
        foreach(array_keys($this->db->getAll()) as $key) {
            $this->db->remove($key);
        }
        foreach($this->crates as $name => $crate) {
            $this->db->set($name, $crate->getSaveArray());
        }
        $this->db->save();
    }

    public function addCrate(Player $player, Position $pos, string $name, string $description) : void {
        $crate = new Crate($name, $description, [], $pos);
        $this->crates[$name] = $crate;
        $crate->openRewardsEditorMenu($player);
    }

    public function getCrate(string $crate) : Crate {
        return $this->crates[$crate];
    }

    public function crateExists(string $name) : bool {
        return isset($this->crates[$name]);
    }

    public function removeCrate(string $name) : void {
        unset($this->crates[$name]);
    }

    public function getCrateAt(Position $position) : ?Crate {
        foreach($this->crates as $crate) {
            $pos = $crate->getPosition();
            if ($pos->getWorld()->getDisplayName() !== $position->getWorld()->getDisplayName()) {
                continue;
            }
            if ($pos->getFloorX() == $position->getFloorX() && $pos->getFloorZ() == $position->getFloorZ() && $pos->getFloorY() == $position->getFloorY()) {
                return $crate;
            }
        }
        return null;
    }

}