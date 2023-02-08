<?php namespace cytonix\crates;

use cytonix\utils\FormatUtils;
use cytonix\utils\PositionUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\world\Position;

class Crate {

    /** @var string */
    private string $name;

    /** @var string */
    private string $description;

    /** @var array */
    private array $rewards;

    /** @var Position  */
    private Position $position;

    public function __construct(string $name, string $description, array $rewards, Position $position) {
        $this->name = $name;
        $this->description = $description;
        $this->rewards = $rewards;
        $this->position = $position;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function getRandomRewards() : Item {
        return $this->rewards[array_rand($this->rewards)];
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function setDescription(string $description) : void {
        $this->description = $description;
    }

    public function setRewards(array $new) : void {
        $this->rewards = $new;
    }

    public function getRewards() : array {
        return $this->rewards;
    }

    public function getPosition() : Position {
        return $this->position;
    }

    public function setPosition(Position $position) : void {
        $this->position = $position;
    }

    public function getSaveArray() : array {
        return [
            "description" => $this->getDescription(),
            "rewards" => array_map(fn(Item $item) => $item->jsonSerialize(), $this->getRewards()),
            "position" => PositionUtils::positionToString($this->getPosition())
        ];
    }

    public function openRewardsEditorMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->send($player, "Crate Reward Editor");
        $menu->getInventory()->setContents($this->getRewards());
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
            $this->setRewards($inventory->getContents());
            $player->sendMessage(FormatUtils::PREFIX_GOOD . "Crate rewards set!");
        });
    }

}