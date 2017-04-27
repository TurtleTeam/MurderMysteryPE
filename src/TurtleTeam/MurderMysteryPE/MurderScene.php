<?php
namespace TurtleTeam;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
final class MurderScene{
        /**
         * @var Player $traitor
         * @var Player $detective
         */
        public $traitor, $detective;

        /** @var Player[] */
        public $joinQueue = [], $innocents = [];

        /** @var Position[] */
        protected $goldIngotPositions = [], $innocentsSpawn = [];

        /** @var Position */
        public $traitorSpawn = NULL, $detectiveSpawn = NULL;

        /**
         * MurderScene constructor.
         *
         * @TODO parse murder scene config
         *
         * @param Config $config
         */
        public function __construct(Config $config){
        }

        /**
         * @return int|string
         */
        public function getRandomKeyFromQueue(){
                $rand = array_rand($this->joinQueue);
                return $rand;
        }

        /**
         * @return Player
         */
        public function pullFromQueue(){
                $key = $this->getRandomKeyFromQueue();
                $val = $this->joinQueue[$key];
                unset($this->joinQueue[$key]);
                return $val;
        }

        /**
         * @param Player $player
         */
        public function pushToQueue(Player $player){
                $this->joinQueue[ ] = $player;
        }

        /**
         * @param Player $traitor
         */
        public function setTraitor(Player $traitor){
                $this->traitor = $traitor;
        }

        /**
         * @return Player
         */
        public function getTraitor(){
                return $this->traitor;
        }

        /**
         * @param Player $detective
         */
        public function setDetective(Player $detective){
                $this->detective = $detective;
        }

        /**
         * @return Player
         */
        public function getDetective(){
                return $this->detective;
        }

        /**
         * @return Player[]
         */
        public function getInnocents(){
                return $this->innocents;
        }

        public function autoSetPlayers(){
                $this->traitor = $this->pullFromQueue();
                $this->detective = $this->pullFromQueue();
                $this->innocents = $this->joinQueue;
                $this->joinQueue = [ ];
        }

        /**
         * @TODO make player a spectator
         *
         * @param Player $target
         */
        public function handleDeath(Player $target){

        }

        /**
         * @TODO implement method
         * @TODO spawn golden ingots every X seconds
         */
        public function tickScene(){

        }
}