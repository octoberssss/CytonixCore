<?php namespace cytonix\kits;

use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use jojoe77777\FormAPI\CustomForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class Kit {

    /** @var string */
    private string $name;

    /** @var string */
    private string $description;

    /** @var array<Item> */
    private array $items;

    /** @var int */
    private int $coolDown;

    /** @var Item */
    private Item $icon;

    /** @var string */
    private string $permission;

    /** @var int */
    private int $iconPosition;

    public function __construct(
        string $name,
        string $description,
        array $items,
        int $coolDown,
        Item $icon,
        string $permission,
        int $iconPosition
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->items = $items;
        $this->coolDown = $coolDown;
        $this->icon = $icon;
        $this->permission = $permission;
        $this->iconPosition = $iconPosition;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function getItems() : array {
        return $this->items;
    }

    public function getCoolDown() : int {
        return $this->coolDown;
    }

    public function getIcon() : Item {
        return $this->icon;
    }

    public function getPermission() : string {
        return $this->permission;
    }

    public function equip(Player $player) : void {
        $dropped = false;
        foreach($this->items as $item) {
            if (!$player->getInventory()->canAddItem($item)) {
                $dropped = true;
                $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
                continue;
            }
            $player->getInventory()->addItem($item);
        }
        if (!$dropped) {
            return;
        }
        $player->sendMessage(FormatUtils::PREFIX_BAD . "You inventory didn't have enough space, and some items were dropped on the ground.");
    }

    public function getIconPosition() : int {
        return $this->iconPosition;
    }

    public function openIconEditForm(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->send($player, "Icon Editor Menu");
        $menu->getInventory()->setItem(13, $this->icon);
        for ($i = 0; $i <= 26; $i++) {
            if ($menu->getInventory()->getItem($i)->getId() == 0) {
                $menu->getInventory()->setItem($i, VanillaBlocks::INVISIBLE_BEDROCK()->asItem()->setCustomName("§l §e §d"));
            }
        }
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
            $this->items = $inventory->getContents();
            $this->icon = $inventory->getItem(13);
            $player->sendMessage(FormatUtils::PREFIX_GOOD . "Kit fully edited.");
            Manager::getKitManager()->save();
            Manager::getKitManager()->__construct();
        });
    }

    public function openItemsEditForm(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($player, "Kit Items");
        $menu->getInventory()->setContents($this->items);
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
            $this->items = $inventory->getContents();
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void {
                $this->openIconEditForm($player);
            }), 20);
            $player->sendMessage(FormatUtils::PREFIX_GOOD . "Opening icon edit form.");
        });
    }

    public function openEditForm(Player $player) : void {
        $form = new CustomForm(function(Player $player, array $data) {
            $name = $data[0];
            $description = $data[1];
            $permission = $data[2];
            $coolDown = $data[3];
            $iconPosition = $data[4];
            $this->name = $name;
            $this->description = $description;
            $this->permission = $permission;
            $this->coolDown = $coolDown;
            $this->iconPosition = $iconPosition;
            $this->openItemsEditForm($player);
            $player->sendMessage(FormatUtils::PREFIX_GOOD . "Opening item editor form.");
        });
        $form->setTitle("Kit Editor Form");
        $form->addInput("Kit Name", "Name", $this->name);
        $form->addInput("Kit Description", "A Cool Kit Description", $this->description);
        $form->addInput("Kit Permission", "cytonix.kits.guest", $this->permission);
        $form->addInput("Kit CoolDown", "120", $this->coolDown);
        $form->addInput("Icon Position", "0", $this->iconPosition);
        $player->sendForm($form);
    }

}