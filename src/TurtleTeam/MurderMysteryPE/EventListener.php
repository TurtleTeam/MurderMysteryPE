<?php

  /**
   * This class will listen for events and notify the main class.
   *
   * @link https://github.com/TurtleTeam/MurderMysteryPE.git
   */


use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDeathEvent;

class EventListener implements Listener{

        private $plugin;

        /**
         * EventListener constructor.
         *
         * @param Main $main
         */
        public function __construct(Main $main){
                $this->plugin = $main;
        }

        /**
         * @param EntityDeathEvent $event
         */
        public function onDeath(EntityDeathEvent $event){
                $target = $event->getEntity();
        }
}
  