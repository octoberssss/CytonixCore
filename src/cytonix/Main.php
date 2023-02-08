<?php namespace cytonix;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use cytonix\modules\ModuleRegistry;
use JsonException;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    /** @var array */
    private array $config;

    /*** @throws HookAlreadyRegistered */
    public function onEnable() : void {
        self::setInstance($this);

        $this->saveResource("config.yml");
        $this->config = $this->getConfig()->getAll();

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        Manager::init();
        ModuleRegistry::init();

        $this->getServer()->getNetwork()->setName("§r§3Cytonix§r§7");
        $this->getLogger()->notice("Welcome to Cytonix.");
    }

    /*** @throws JsonException */
    public function onDisable() : void {
        Manager::getFactionManager()->saveAllFactions();
        Manager::getClaimManager()->saveAllClaims();
        Manager::getKitManager()->save();
        Manager::getCratesManager()->save();
    }

    public function getCachedConfig() : array {
        return $this->config;
    }

}