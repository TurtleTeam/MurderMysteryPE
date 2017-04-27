<?php
  
  /**
   * This is the main class of the plugin. It will load everything.
   *
   * @link https://github.com/TurtleTeam/MurderMysteryPE.git
   */
  
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
class Main extends PluginBase{

        /** @var Config $config */
        private $config;

        /** @var Config $lang */
        private $lang;

        /** @var Config $lang */
        private $data;

        /** @var \TurtleTeam\MurderScene[] */
        public $murderScenes = [];

        public function onEnable(){
                $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
                $this->getLogger()->notice("Enabling...");
        }
}