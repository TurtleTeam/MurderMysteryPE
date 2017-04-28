<?php

namespace TurtleTeam\MurderMysteryPE;
  
/**
 * This is the main class of the plugin. It will load everything.
 *
 * @link https://github.com/TurtleTeam/MurderMysteryPE.git
 */
  
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

define("START_TIME", microtime(true));
// If the plugin is running from the source
define("DEV_MODE", strpos(__FILE__, "phar://") === false);

/**
 * Returns a string
 *
 * @param string $key
 * @param string $params = []
 */
function lang(string $key, $params = []): string {
  return MurderMystery::getInstance()->getMessage($key, (array) $params);
}

class MurderMystery extends PluginBase{

  /** @var MurderMystery */
  private static $instance;

  public static function getInstance(): MurderMystery {
    return self::$instance;
  }

  /** @var Config $config */
  private $config;

  /** @var Config $lang */
  private $lang;

  /** @var \TurtleTeam\MurderScene[] */
  public $murderScenes = [];

  public function onLoad() {
    self::$instance = $this;

    $df = $this->getDataFolder();
    @mkdir($this->getDataFolder());

    if(!file_exists($df."messages.yml")) {
      $this->saveResource("messages.yml");
    }

    // Load messages
    $this->lang = new Config($df."messages.yml");
    
    var_dump($this->getLang()->getAll());
  }

  public function onEnable() {
    $this->getLogger()->info(lang("plugin.enabling"));

    // Load Games

    // Load Signs

    // Set command executors

    // Schedule SceneTicker

    $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

    $this->getLogger()->info(lang("plugin.enabled", ["time" => round(microtime(true) - START_TIME, 4)]));
  }

  public function onDisable() {

  }

  private function getLang(): Config {
    return $this->lang;
  }

  public function getMessage(string $key, array $params = []) {
    $msg = $this->getLang()->getNested($key, $key);
    if($msg === $key) {
      $this->getLogger()->debug("Undefined key '$key' ".(!empty($params) ? "(params=".implode(", ", array_map(function($key, $el) {
        return $key.": '".$el."'";
      }, $params)).")" : ""));
    }

    $i = 0;
    foreach ($params as $key => $value) {
      $msg = str_replace([":$i", "{:$key}", ":$key"], $value, $msg); 
    }

    return $msg;
  }

}