<?php namespace cytonix\enchants\types;

use pocketmine\entity\Entity;
use pocketmine\player\Player;

abstract class CytonixSwordEnchant extends CytonixEnchant {

    abstract function onHit(Player $player, Entity $hit, int $level) : void;

}