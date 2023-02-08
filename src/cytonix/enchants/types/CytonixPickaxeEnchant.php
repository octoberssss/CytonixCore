<?php namespace cytonix\enchants\types;

use pocketmine\block\Block;
use pocketmine\player\Player;

abstract class CytonixPickaxeEnchant extends CytonixEnchant {

    abstract function onBreak(Player $player, Block $block, int $level) : void;

}