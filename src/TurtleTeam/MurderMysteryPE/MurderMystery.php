<?php

namespace TurtleTeam\MurderMysteryPE;
  
/**
 * This is the main class of the plugin. It will load everything.
 *
 * @link https://github.com/TurtleTeam/MurderMysteryPE.git
 */
  
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use TurtleTeam\MurderMysteryPE\Utils\GenUtils;

define("START_TIME", microtime(true));
// If the plugin is running from the source
define("DEV_MODE", strpos(__FILE__, "phar://") === false);
define("VERSION_HISTORY", ['0.0.1', '0.1.0']);

/**
 * Returns a string
 *
 * @param string $key
 * @param string[] $params
 *
 * @return string
 */
function lang(string $key, $params = []): string {
    return MurderMystery::getInstance()->getMessage($key, (array) $params);
}

/**
 * Returns a setting value from a config file
 *
 * @param string $key
 * @param null $default
 * @param bool $nested
 *
 * @return mixed
 */
function _var(string $key, $default = null, $nested = false){
    if (!$nested) return MurderMystery::getInstance()->getConfig()->get($key, $default);
    return MurderMystery::getInstance()->getConfig()->getNested($key, $default);
}

class MurderMystery extends PluginBase{

    /** @var MurderMystery */
    private static $instance;

    /** @var Config $lang */
    private $lang;

    /** @var MurderScene[] */
    private $murderScenes = [];

    public function onLoad(){
        self::$instance = $this;

        define("AUTHORS", $this->getDescription()->getAuthors());
        define("VERSION", $this->getDescription()->getVersion());

        $df = $this->getDataFolder();
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . 'murderScenes/');
        $this->saveDefaultConfig();

        if (!file_exists($df . "messages.yml")) {
            $this->saveResource("messages.yml");
        }

        // Load messages
        $this->lang = new Config($df . "messages.yml");
    }

    public function onEnable(){

        $this->getLogger()->info(lang("plugin.enabling"));

        $this->getLogger()->info(GenUtils::formatter("Developed and maintained by %1", AUTHORS)); # This does't seem to be working corectly

        // Load Games
        foreach (new \FilesystemIterator($this->getDataFolder() . "murderScenes") as $path => $file) {
            if($file->isDot() or $file->isDir()) continue;

            try {
              // Load Game
              // MurderScene::__construct will throw an error if the data is invalid or something else fails
              $game = new MurderScene($file->getBasename(".yml"), new Config($path, Config::YAML, []));
              $this->addMurderScene($game);

            } catch (\InvalidArgumentException $e) {
              $this->getLogger()->error("Can not load game from '$file': ".$e->getMessage()); // or attach failed ?
            }
        }

        $this->getLogger()->info(lang("plugin.games-loaded", [
          "games" => implode(", ", array_keys($this->murderScenes)), 
          "count" => count($this->murderScenes)
          ]));

        // Load Signs

        // Set command executors

        // Schedule SceneTicker

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getLogger()->info(lang("plugin.enabled", ["time" => round(microtime(true) - START_TIME, 4)]));
    }

    public function onDisable(){
        $this->getLogger()->info(lang("plugin.disabling"));

        // Stop all MurderScenes
        foreach ($this->murderScenes as $scene) {
            $scene->stop();
        }

        // Save all MurderScenes settings into array then into file
        // TODO

        // Save all signs into array then into file
        // TODO

        $this->getLogger()->info(lang("plugin.disabled"));
    }

    private function getLang(): Config{
        return $this->lang;
    }


//      .d8b.  d8888b. d888888b 
//     d8' `8b 88  `8D   `88'   
//     88ooo88 88oodD'    88    
//     88~~~88 88~~~      88    
//     88   88 88        .88.   
//     YP   YP 88      Y888888b

    public static function getInstance(): MurderMystery{
            return self::$instance;
    }

    /**
     * @param string $key
     * @param array $params
     *
     * @return mixed
     */
    public function getMessage(string $key, array $params = []){
        $msg = $this->getLang()->getNested($key, $key);
        if ($msg === $key) {
            $this->getLogger()->debug("Undefined key '$key' " . (!empty($params) ? "(params=" . implode(", ", array_map(function ($key, $el) {
                        return $key . ": '" . $el . "'";
                    }, array_keys($params), array_values($params))) . ")" : ""));
        }

        $i = 0;
        GenUtils::loop($params, function($key, $value) use (&$msg, &$i){
            $msg = str_replace([":$i", "{:$key}", ":$key"], $value, $msg);
        }, $i);

        return $msg;
    }

    /**
     * @param int $id
     *
     * @return MurderScene|null
     */
    public function getMurderScene($id){
        if(isset($this->murderScenes[$id])){
            return $this->murderScenes[$id];
        }
        return null;
    }

    /**
     * @param Player $player
     *
     * @return MurderScene|null
     */
    public function getMurderSceneByPlayer(Player $player){
        $returnVal = NULL;
        GenUtils::loop($this->murderScenes, function($key, $val) use (&$returnVal, &$player){
            if($val instanceof MurderScene and $val->isParticipator($player)){
                $returnVal = $val;
            }
        });
        return $returnVal;
    }

    /**
     * Returns true if $player is currently playing in one of the scenes
     *
     * @param Player $player
     *
     * @return bool
     */
    public function isParticipator(Player $player): bool{
        return $this->getMurderSceneByPlayer($player) !== null;
    }

    /**
     * if participator returns int
     *
     * 0x00 = not participating
     * 0x01 = traitor
     * 0x02 = detective
     * 0x02 = innocent
     * 0x03 = unknown
     *
     * @param Player $player
     *
     * @return int
     */
    public function getRole(Player $player){
        if($this->isParticipator($player)){
            $scene = $this->getMurderSceneByPlayer($player);
            return $scene->getRole($player);
        }
        return 0x00;
    }

    /**
     * @return MurderScene[]
     */
    public function getAllMurderScenes(): array{
        return $this->murderScenes;
    }

    /**
     * Adds new MurderScene to available games list. Throws an error if scene already is added
     *
     * @throws \InvalidArgumentException
     * @param MurderScene $scene
     */
    public function addMurderScene(MurderScene $scene) {
        if($this->murderSceneExists($scene)) {
            throw new \InvalidArgumentException("$scene already attached");
        } else {
            $this->murderScenes[$scene->getId()] = $scene;
        }
    }

    private function murderSceneExists(MurderScene $scene): bool {
        return in_array($scene, $this->murderScenes, true) || isset($this->murderScenes[$scene->getId()]);
    }

    public function removeMurderScene(MurderScene $scene): bool {
        if(!$this->murderSceneExists($scene)) return false;
        unset($this->murderScenes[$scene->getId()]);
        return true;
    }

}