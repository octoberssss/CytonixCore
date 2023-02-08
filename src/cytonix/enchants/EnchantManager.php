<?php namespace cytonix\enchants;

use cytonix\enchants\armor\SpeedEnchant;
use cytonix\enchants\commands\CECommand;
use cytonix\enchants\commands\EnchanterCommand;
use cytonix\enchants\commands\BookCommand;
use cytonix\enchants\pickaxe\RelicFinderEnchant;
use cytonix\enchants\sword\PoisonEnchant;
use cytonix\enchants\types\CytonixEnchant;
use cytonix\Main;
use cytonix\utils\FormatUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;

class EnchantManager {

    public const NAME_TO_ID = [
        "RelicFinder" => 60,
        "Poison" => 61,
        "Speed" => 62
    ];

    public const RARITY_TO_COLOR = [
        Rarity::COMMON => "§a",
        Rarity::UNCOMMON => "§2",
        Rarity::RARE => "§3",
        Rarity::MYTHIC => "§6"
    ];

    public function __construct() {
        $list = [
            -1 => new Enchantment("", Rarity::COMMON, 0x0, 0x0, 0),
            60 => new RelicFinderEnchant("RelicFinder", Rarity::RARE, ItemFlags::PICKAXE, 0x0, 5),
            61 => new PoisonEnchant("Poison", Rarity::COMMON, ItemFlags::SWORD, 0x0, 5),
            62 => new SpeedEnchant("Speed", Rarity::UNCOMMON, ItemFlags::ARMOR, 0x0, 2)
        ];
        foreach($list as $id => $enchant) {
            EnchantmentIdMap::getInstance()->register($id, $enchant);
        }
        $server = Server::getInstance();
        $server->getPluginManager()->registerEvents(new EnchantListener(), Main::getInstance());
        $server->getCommandMap()->registerAll("CytonixCore", [
            new CECommand(),
            new BookCommand(),
            new EnchanterCommand()
        ]);
    }

    public function reSlotItem(Item $item) : Item {
        if (is_null($item->getNamedTag()->getTag("slots"))) {
            $item->getNamedTag()->setInt("slots", 6);
        }
        return $item;
    }
    
    public function getSlotsFilled(Item $item) : int {
        $enchants = 0;
        foreach($item->getEnchantments() as $enchantment) {
            $enchantment = $enchantment->getType();
            if ($enchantment instanceof CytonixEnchant) {
                $enchants++;
            }
        }
        return $enchants;
    }

    public function loreItem(Item $item) : Item {
        if (!is_null($item->getNamedTag()->getTag("hideEnchantments"))) {
            return $item;
        }
        $item = $this->reSlotItem($item);
        $enchantLore = [];
        foreach($item->getEnchantments() as $enchantment) {
            $enchantment = $enchantment->getType();
            $enchantLore[" §r§7» §r" . self::RARITY_TO_COLOR[$enchantment->getRarity()] . $enchantment->getName() . " " . FormatUtils::intToRoman($item->getEnchantmentLevel($enchantment))] = $enchantment->getRarity();
        }
        asort($enchantLore);
        $lore = ["§r§3Enchantments:"];
        $lore = array_merge($lore, array_keys($enchantLore));
        $lore[] = "§r§3Info:";
        $lore[] = "§r§7 » §r§fSlots§7: §3" . $this->getSlotsFilled($item) . " §r§7/ §3" . $item->getNamedTag()->getInt("slots");
        $item->setLore($lore);
        return $item;
    }

    public function makeEnchantmentBook(CytonixEnchant $enchantment, int $level, int $success = 100) : Item {
        $book = VanillaItems::BOOK();
        $book->setCustomName("§r" . self::RARITY_TO_COLOR[$enchantment->getRarity()] . $enchantment->getName() . " §r§3Book");
        $book->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1), 1));
        $book->setLore([
            "§r§fEnchantment§7: §r" . self::RARITY_TO_COLOR[$enchantment->getRarity()] . $enchantment->getName(),
            "§r§fDescription§7: §3" . wordwrap($enchantment->getDescription(), 30),
            "§r §e",
            "§r§aSuccess Chance§7: §3" . $success . "%",
            "§r§cFail Chance§7: §3" . (100 - $success) . "%",
            "§r §d",
            "§r§fCan be applied to§7: §3" . $enchantment->canBeAppliedTo(),
            "§r§7Use /enchanter to apply to an item!"
        ]);
        $book->getNamedTag()->setInt("enchantId", EnchantmentIdMap::getInstance()->toId($enchantment));
        $book->getNamedTag()->setInt("enchantLevel", $level);
        $book->getNamedTag()->setInt("successChance", $success);
        return $book;
    }

    public function getCombineData(Item $book) : ?array {
        if (is_null($id = $book->getNamedTag()->getTag("enchantId"))) {
            return null;
        }
        $id = $id->getValue();
        $level = $book->getNamedTag()->getInt("enchantLevel");
        $chance = $book->getNamedTag()->getInt("successChance");
        return [$id, $level, $chance];
    }

    public function openEnchanterMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->send($player, "Enchanter");
        $inv = $menu->getInventory();
        $inv->setItem(2, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN())
            ->asItem()->setCustomName("§r§aYour Item")
            ->setLore(["§r§fSet your item in the slot below!"]));
        $inv->setItem(6, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::PURPLE())
            ->asItem()->setCustomName("§r§5Enchanting Book")
            ->setLore(["§r§fPut your enchanting book in the slot below!"]));
        $inv->setItem(22, VanillaItems::MAGMA_CREAM()->setCustomName("§r§eTap me to combine!"));
        $inv->setItem(4, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW())->asItem()->setCustomName("§r§7§oThe combined items will output under this item."));
        $blank = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName("§l §c §b");
        $setItems = function(Item $item) use ($inv) : void {
            for ($i = 0; $i <= 26; $i++) {
                if (in_array($i, [2, 11, 6, 15, 13, 22, 4])) {
                    continue;
                }
                $inv->setItem($i, $item);
            }
        };
        $setItems($blank);
        $menu->setListener(function(InvMenuTransaction $transaction) use ($inv, $setItems) : InvMenuTransactionResult {
            $slot = $transaction->getAction()->getSlot();
            if (in_array($slot, [11, 15, 13])) {
                return $transaction->continue();
            }
            if ($slot == 22) {
                $in = $inv->getItem(11);
                $book = $inv->getItem(15);
                if (!$in instanceof Durable || is_null($data = $this->getCombineData($book))) {
                    $setItems(VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::ORANGE())->asItem()->setCustomName("§r§cInvalid items."));
                    return $transaction->discard();
                }
                $in = $this->loreItem($in);
                if ($this->getSlotsFilled($in) >= $in->getNamedTag()->getInt("slots")) {
                    $setItems(VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::ORANGE())->asItem()->setCustomName("§r§cThis item's slots are full"));
                    return $transaction->discard();
                }
                /** @var CytonixEnchant $enchant */
                $enchant = EnchantmentIdMap::getInstance()->fromId($data[0]);
                if (!$enchant->isApplicableTo($in)) {
                    $setItems(VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::ORANGE())->asItem()->setCustomName("§r§cThat book is not applicable."));
                    return $transaction->discard();
                }
                $rand = mt_rand(0, 100);
                $inv->setItem(11, VanillaItems::AIR());
                $inv->setItem(15, VanillaItems::AIR());
                if ($rand < (100 - $data[2])) {
                    $setItems(VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("§r§cCombine failed."));
                    return $transaction->discard();
                }
                $in->addEnchantment(new EnchantmentInstance(
                    EnchantmentIdMap::getInstance()->fromId($data[0]),
                    $data[1]
                ));
                $in = $this->loreItem($in);
                $inv->setItem(13, $in);
                $setItems(VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN())->asItem()->setCustomName("§r§aCombine success!"));
                return $transaction->discard();
            }
            return $transaction->discard();
        });
    }

}