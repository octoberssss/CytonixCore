<?php namespace cytonix;

use cytonix\claims\ClaimManager;
use cytonix\crates\CratesManager;
use cytonix\enchants\EnchantManager;
use cytonix\factions\FactionManager;
use cytonix\kits\KitManager;
use cytonix\ranks\RankManager;
use cytonix\sessions\SessionManager;
use cytonix\text\FloatingTextManager;

class Manager {

    /** @var SessionManager */
    private static SessionManager $sessionManager;

    /** @var FactionManager */
    private static FactionManager $factionManager;

    /** @var ClaimManager */
    private static ClaimManager $claimManager;

    /** @var RankManager */
    private static RankManager $rankManager;

    /** @var KitManager */
    private static KitManager $kitManager;

    /** @var EnchantManager */
    private static EnchantManager $enchantManager;

    /** @var CratesManager */
    private static CratesManager $cratesManager;

    /** @var FloatingTextManager */
    private static FloatingTextManager $floatingTextManager;

    public static function init() : void {
        self::$floatingTextManager = new FloatingTextManager();
        self::$sessionManager = new SessionManager();
        self::$factionManager = new FactionManager();
        self::$claimManager = new ClaimManager();
        self::$rankManager = new RankManager();
        self::$kitManager = new KitManager();
        self::$enchantManager = new EnchantManager();
        self::$cratesManager = new CratesManager();
    }

    public static function getSessionManager() : SessionManager {
        return self::$sessionManager;
    }

    public static function getFactionManager() : FactionManager {
        return self::$factionManager;
    }

    public static function getClaimManager() : ClaimManager {
        return self::$claimManager;
    }

    public static function getRankManager() : RankManager {
        return self::$rankManager;
    }

    public static function getKitManager() : KitManager {
        return self::$kitManager;
    }

    public static function getEnchantManager() : EnchantManager {
        return self::$enchantManager;
    }

    public static function getCratesManager() : CratesManager {
        return self::$cratesManager;
    }

    public static function getFloatingTextManager() : FloatingTextManager {
        return self::$floatingTextManager;
    }

}