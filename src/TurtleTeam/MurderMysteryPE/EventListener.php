<?php

  /**
   * This class will listen for events and notify the main class.
   *
   * @link https://github.com/TurtleTeam/MurderMysteryPE.git
   */
  
  use pocketmine\event\Listener;
  
  class EventListener implements Listener{
    
    private $plugin;
    
    public function __construct(Main $main){
      $this->plugin = $main;
    }
    
    public function onDeath(PlayerDeathEvent $event){
      $name = $event->getPlayer()->getName();
      if($event->getPlayer()->getLastDamageCause() instanceof EntityDamageByEntityEvent){
        $this->plugin->handleDeath($name, $event->getPlayer()->getLastDamageCause()->getDamager());
      } else{
        $this->plugin->handleDeath($name);
      }
    }
  }
  