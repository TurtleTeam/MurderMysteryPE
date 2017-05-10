<?php
namespace TurtleTeam\MurderMysteryPE;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use TurtleTeam\MurderMysteryPE\Utils\GenUtils;

class MurderScene{

    /**
     * After how many ticks before last ingot spawn should new ingots spawn
     * If < 0, it's disabled.
     *
     * @var int
     */
    protected $goldenIngotCounter = -1;

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
     * This is unique string identifier for scene. Used to refer it
     * It's used for example in commands where you have to type scene's id to join
     * 
     * @var string 
     */
    protected $id;

    /**
     * MurderScene constructor.
     *
     * @throws InvalidArgumentException
     * @param Config $config
     */
    public function __construct(string $id, Config $data){
        $this->id = $id;
        $this->goldenIngotCounter = $data->getNested("goldenIngot.spawnDelay", null) ?? intval(_var('goldenIngot.spawnDelay'));
        
        // Validate data,       
        // Parse Positions
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
        $this->joinQueue[] = $player;
    }

    /**
     * @return array|Player[]
     */
    public function getParticipators(){
        $players = $this->innocents;
        $players[] = $this->traitor;
        $players[] = $this->detective;

        return $players;
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function getRole(Player $player){
        if($player === $this->traitor)  return 0x01;
        if($player === $this->detective) return 0x02;
        if(isset($this->innocents[spl_object_hash($player)])) return 0x03;
        return 0x04;
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
     * @param Player $p
     */
    public function removeInnocent(Player $p){
        if(isset($this->innocents[spl_object_hash($p)])){
            unset($this->innocents[spl_object_hash($p)]);
        }
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
        $this->joinQueue = [];
    }

    /**
     * @TODO make player a spectator
     *
     * @param Player $target
     */
    public function handleDeath(Player $target){

    }

    /**
     * @TODO reset game scene
     */
    public function reset(){

    }

    /**
     * @TODO stop ticking scene
     * @TODO if running teleport all players to spawn
     *
     */
    public function stop(){

    }

    /**
     * @TODO implement method
     * @TODO send HUD tips to players every X second
     */
    public function tickScene(){
        --$this->goldenIngotCounter;
        if($this->goldenIngotCounter == 0) {
            GenUtils::loop($this->goldIngotPositions, function($key, $val){
               if($val instanceof Position){
                   $val->level->dropItem($val, Item::get(Item::GOLD_INGOT, 0, 1));
               }
            });
            $this->goldenIngotCounter = intval(_var('goldenIngot.spawnDelay'));
        }
    }
}