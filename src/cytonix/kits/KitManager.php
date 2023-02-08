<?php namespace cytonix\kits;

use cytonix\kits\commands\CreateKitCommand;
use cytonix\kits\commands\EditKitCommand;
use cytonix\kits\commands\KitsCommand;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use cytonix\utils\InvMenuUtils;
use JsonException;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class KitManager {

    /** @var Config */
    private Config $db;

    /** @var array<string, Kit> */
    private array $kits;

    public function __construct() {
        $this->kits = [];
        $this->db = new Config(Main::getInstance()->getDataFolder() . "kits.yml", Config::YAML);
        foreach($this->db->getAll() as $name => $data) {
            $this->kits[$name] = new Kit(
                $name,
                $data["description"],
                array_map(fn($item) => Item::jsonDeserialize($item), $data["items"]),
                $data["coolDown"],
                Item::jsonDeserialize($data["icon"]),
                $data["permission"],
                $data["iconPosition"]
            );
        }
        Server::getInstance()->getCommandMap()->registerAll("CytonixCore", [
            new KitsCommand(),
            new CreateKitCommand(),
            new EditKitCommand()
        ]);
    }

    /*** @throws JsonException */
    public function save() : void {
        foreach(array_keys($this->db->getAll()) as $key) {
            $this->db->remove($key);
        }
        foreach($this->kits as $kit) {
            $this->db->set($kit->getName(), [
                "description" => $kit->getDescription(),
                "items" => array_map(fn(Item $item) => $item->jsonSerialize(), $kit->getItems()),
                "coolDown" => $kit->getCoolDown(),
                "icon" => $kit->getIcon()->jsonSerialize(),
                "permission" => $kit->getPermission(),
                "iconPosition" => $kit->getIconPosition()
            ]);
        }
        $this->db->save();
    }

    public function addKit(Player $player) : void {
        if (isset($this->kits["pre"])) {
            unset($this->kits["pre"]);
        }
        $this->kits["pre"] = new Kit(
            "pre",
            "pre",
            [],
            0,
            VanillaItems::RED_DYE(),
            "",
            0
        );
        $this->kits["pre"]->openEditForm($player);
    }

    public function getKitFromName(string $name) : ?Kit {
        if (!isset($this->kits[$name])) {
            return null;
        }
        return $this->kits[$name];
    }

    public function openKitMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($player, "Kits");

        $inventory = $menu->getInventory();
        foreach($this->kits as $kit) {
            $item = $kit->getIcon();
            $item->getNamedTag()->setString("kit", $kit->getName());
            $inventory->setItem($kit->getIconPosition(), $kit->getIcon());
        }
        $blank = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName("§l §c §b");
        for ($i = 0; $i <= 53; $i++) {
            if ($inventory->getItem($i)->getId() == 0) {
                $inventory->setItem($i, $blank);
            }
        }

        $menu->setListener(function(InvMenuTransaction $transaction) use($inventory) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            if (is_null($item->getNamedTag()->getTag("kit"))) {
                return $transaction->discard();
            }
            $kit = $this->kits[$item->getNamedTag()->getString("kit")];

            if (!$player->hasPermission($kit->getPermission())) {
                InvMenuUtils::errorAt($inventory, $transaction->getAction()->getSlot(), "§r§7You do not have permission to use this kit.");
                return $transaction->discard();
            }
            $session = Manager::getSessionManager()->getSession($player);
            if ($session->isOnKitCoolDown($kit->getName(), $kit->getCoolDown())) {
                InvMenuUtils::errorAt($inventory, $transaction->getAction()->getSlot(), "§r§7Still on cool-down (" . FormatUtils::intToTimeString($session->getKitTimeLeft($kit->getName(), $kit->getCoolDown())) . ")");
                return $transaction->discard();
            }
            $session->setKitCoolDown($kit->getName());
            $kit->equip($player);
            return $transaction->discard();
        });
    }

}