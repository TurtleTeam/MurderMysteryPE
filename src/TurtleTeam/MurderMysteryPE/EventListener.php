<?php

namespace TurtleTeam\MurderMysteryPE;


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
   * @param MurderMystery $main
   */
  public function __construct(MurderMystery $main){
    $this->plugin = $main;
  }

  /**
   * @param EntityDeathEvent $event
   */
  public function onDeath(EntityDeathEvent $event){
    $target = $event->getEntity();
  }
}
  