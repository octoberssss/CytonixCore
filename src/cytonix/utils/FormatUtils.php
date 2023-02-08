<?php namespace cytonix\utils;

use UnexpectedValueException;

class FormatUtils {

    public const PREFIX_BAD = "§r§3Cytonix §r§7> §c";

    public const PREFIX_GOOD = "§r§3Cytonix §r§7> §a";

    public const TIP_PREFIX_BAD = "§r§7[§c!§7] §c";

    public const TIP_PREFIX_GOOD = "§r§7[§a!§7] §a";

    public const FAC_PREFIX_GOOD = "§r§aFaction §r§7> §a";

    public const FAC_PREFIX_BAD = "§r§aFaction §r§7> §c";

    public const BLANK = "§r §e §l";

    public static function intToTimeString(int $seconds) : string {
        if($seconds < 0) throw new UnexpectedValueException("time can't be a negative value");
        if($seconds === 0) {
            return "0 seconds";
        }
        $timeString = "";
        $timeArray = [];
        if($seconds >= 86400) {
            $unit = floor($seconds / 86400);
            $seconds -= $unit * 86400;
            $timeArray[] = $unit . " days";
        }
        if($seconds >= 3600) {
            $unit = floor($seconds / 3600);
            $seconds -= $unit * 3600;
            $timeArray[] = $unit . " hours";
        }
        if($seconds >= 60) {
            $unit = floor($seconds / 60);
            $seconds -= $unit * 60;
            $timeArray[] = $unit . " minutes";
        }
        if($seconds >= 1) {
            $timeArray[] = $seconds . " seconds";
        }
        foreach($timeArray as $key => $value) {
            if($key === 0) {
                $timeString .= $value;
            } elseif($key === count($timeArray) - 1) {
                $timeString .= " and " . $value;
            } else {
                $timeString .= ", " . $value;
            }
        }
        return $timeString;
    }

    // https://stackoverflow.com/questions/14994941/numbers-to-roman-numbers-with-php
    public static function intToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

}