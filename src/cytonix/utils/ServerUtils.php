<?php namespace cytonix\utils;

use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class ServerUtils {

    public const INT32_MAX = 2147483647;

    public static function jukeboxPlayer(Player $player, string $message) : void {
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_JUKEBOX_POPUP;
        $pk->message = $message;
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public static function jukeboxTipAll(string $message) : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            self::jukeboxPlayer($player, $message);
        }
    }

}