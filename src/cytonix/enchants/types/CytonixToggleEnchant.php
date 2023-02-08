<?php namespace cytonix\enchants\types;

use pocketmine\player\Player;

abstract class CytonixToggleEnchant extends CytonixEnchant {

    abstract function onPutOn(Player $player, int $level, bool $message = true) : void;

    abstract function onTakeOff(Player $player) : void;

}