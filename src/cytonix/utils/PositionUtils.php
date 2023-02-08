<?php namespace cytonix\utils;

use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class PositionUtils {

    public static function returnMinVector(Vector3 $vec1, Vector3 $vec2) : Vector3 {
        return new Vector3(
            min($vec1->getFloorX(), $vec2->getFloorX()),
            min($vec1->getFloorY(), $vec2->getFloorY()),
            min($vec1->getFloorZ(), $vec2->getFloorZ())
        );
    }

    public static function returnMaxVector(Vector3 $vec1, Vector3 $vec2) : Vector3 {
        return new Vector3(
            max($vec1->getFloorX(), $vec2->getFloorX()),
            max($vec1->getFloorY(), $vec2->getFloorY()),
            max($vec1->getFloorZ(), $vec2->getFloorZ())
        );
    }

    public static function vectorToString(Vector3 $vector3) : string {
        return $vector3->getFloorX() . ":" . $vector3->getFloorY() . ":" . $vector3->getFloorZ();
    }

    public static function stringToVector(string $vector3) : Vector3 {
        $split = explode(":", $vector3);
        return new Vector3((int)$split[0], (int)$split[1], (int)$split[2]);
    }

    public static function positionToString(Position $position) : string {
        return self::vectorToString($position->asVector3()) . ":" . $position->getWorld()->getDisplayName();
    }

    public static function stringToPosition(string $position) : Position {
        $split = explode(":", $position);
        return new Position(
            (int)$split[0],
            (int)$split[1],
            (int)$split[2],
            Server::getInstance()->getWorldManager()->getWorldByName($split[3])
        );
    }

}