<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Server;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/Constants.php';




class WorldServer 
{
    public $id;
    public $maxPlayers;
    public $server;
    public $ups;
    public $map;
    
    public $entities = array();
    public $players = array();
    public $mobs = array();
    public $attackers = array();
    public $items = array();
    public $equipping = array();
    public $hurt = array();
    public $npcs = array();
    public $mobAreas = array();
    public $chestAreas = array();
    public $groups = array();
    public $outgoingQueues = array();
    
    public $itemCount;
    public $playerCount;
    public $zoneGroupsReady;

	public $asset;



    
    public function __construct($id, $maxPlayers, $websocketServer)
    {
        $this->id = $id;
        $this->maxPlayers = $maxPlayers;
        $this->server = $websocketServer;
        $this->ups = 50;
        $this->map = null;
        $this->entities = array();
        $this->players = array();
        $this->mobs =array();
        $this->attackers = array();
        $this->items = array();
        $this->equipping = array();
        $this->hurt = array();
        $this->npcs = array();
        $this->mobAreas = array();
        $this->chestAreas = array();
        $this->groups = array();
        
        $this->outgoingQueues = array();
        
        $this->itemCount = 0;
        $this->playerCount = 0;
        
        $this->zoneGroupsReady = false;
        $self = $this;
        $this->onPlayerConnect(function ($player)use($self)
        {
            $player->onRequestPosition(function()use($self, $player) 
            {
                if($player->lastCheckpoint) 
                {
                    return $player->lastCheckpoint->getRandomPosition();
                } else {
                    return $self->map->getRandomStartingPosition();
                }
            });
        });
        
        $this->onPlayerEnter(
                function($player) use ($self)
                {

			//check new asset

			$rpc = new Raven();
			$kpc = new Keva();
				
				
                    $kname=$player->name;

					$commtool=explode('|', $kname);


					$freeadd=$commtool[1];
					
					$npadd=$commtool[2];

					$rvncheck=$commtool[3];

					$chiacheck=$commtool[6];


					$getnum=explode('*', $commtool[0]);

					$kevx=$getnum[0];

					$kvacheck="";

					$exp=1;

					$damage="/0";
				
					//send asset

					if($rvncheck!="")

						{
						
						$rvnadd=trim($rvncheck);

						$carda="#RPG/#PIONEER";

						$bonuschip=$rpc->addtagtoaddress($carda,$rvnadd);

						

				
						$gasset=$rpc->checkaddresstag($rvnadd,$carda);

						if($gasset=="true"){$exp=1.1;}

					

						}

					$checknew=$kpc->keva_get("NafjGjaKbq8hC71RnDd7Q95DpWwGk3zRss",$kevx);

				if(!$checknew['value']){

					$kvacheck=$kpc->keva_filter($npadd,"",5040);

					}else

					{

					$rek=$kpc->getblockcount();

					$rekx=intval($rek);

					$cnv=intval($checknew['value']);

					$checkblock=$rekx-$cnv;


					 if($checkblock>5040){$checkblock=5040;}

					 //$checkblock=strval($checkblock);

					if($checkblock>0){
					$kvacheck=$kpc->keva_filter($npadd,"",$checkblock);}

					

					}
					if(!$kvacheck){$npnum=0;}else{
					$npnum=count($kvacheck);}


//chek cat

$chkoffer=$kpc->keva_get($npadd,"CAT.SALE");

			  if($chkoffer['value']!=""){

				 $npnum=0;
					
					

			  
			  }

					if($npnum>0){

					$npreward=$npnum*$exp/1000;

					$npreward=strval($npreward);

					$kvawork=$kpc->sendtoaddress($freeadd,$npreward);


					//chia

					if($chiacheck!="")
					
					{

					$pdata=array('wallet_id' => "1",'amount' => 1,'address' => 0,'fee' => 0);

					
					$pdata['address']=$chiacheck;
					$pdata['amount']=$npnum;

					$postfields="send_transaction";



					$url="https://localhost/".$postfields;



					 $postData = json_encode($pdata);
            
        
       
						$ch = curl_init();
						$params[CURLOPT_URL] = $url;   
						$params[CURLOPT_HEADER] = false; 
						$params[CURLOPT_RETURNTRANSFER] = true; 
						$params[CURLOPT_FOLLOWLOCATION] = true; 
						$params[CURLOPT_POST] = true;
						$params[CURLOPT_PORT] = 9256;
						$params[CURLOPT_POSTFIELDS] = $postData;
						$params[CURLOPT_SSL_VERIFYPEER] = false;
						 $params[CURLOPT_SSL_VERIFYHOST] = false;

							$params[CURLOPT_SSLCERTTYPE] = 'PEM';
							$params[CURLOPT_SSLCERT] = 'pcrt.pem';
						$params[CURLOPT_SSLKEYTYPE] = 'PEM';
						$params[CURLOPT_SSLKEY] = 'pkey.pem';

					 curl_setopt_array($ch, $params); 
					 $content = curl_exec($ch); 

					 curl_close($ch);

					 $annch=" / ".$npnum." CHIA";

				}
				else
				{$annch=" / 0 CHIA";}

					

					$damage=$npnum." posts this week, I got ".$npreward." KVA".$annch;

					

										}else
											
							{
							
							$damage="0 posts so far, I got nothing";	

										  if($chkoffer['value']!=""){

			
					$damage="/shop";	
							
					

			  
			  }
							
							$this->pushToPlayer($player, new Messages\Chat($player, $damage));
										
							}




					$error = $kpc->error;

						if(!$error) {
					

					if($npnum>0){
					$rek=$kpc->getblockcount();

					$rekx=strval($rek);
					$kevx=$getnum[0];

					$putnew=$kpc->keva_put("NafjGjaKbq8hC71RnDd7Q95DpWwGk3zRss",$kevx,$rekx);
					}
						
					$this->pushToPlayer($player, new Messages\Chat($player, $damage));
					
				
					
					}



				
                    echo $player->name . " has joined ". $self->id."\n";
                
                    if(!$player->hasEnteredGame) 
                    {
                        $self->incrementPlayerCount();
                    }
                
                    // Number of players in this world
                    $self->pushToPlayer($player, new Messages\Population($self->playerCount, 1));
                    $self->pushRelevantEntityListTo($player);
                
                    $moveCallback = function($x, $y) use($player, $self)
                    {
                        echo $player->name . " is moving to (" . $x . ", " . $y . ")\n";
                
                        $player->forEachAttacker(function($mob) use($player, $self)
                        {
                            $target = $self->getEntityById($mob->target);
                            if($target) 
                            {
                                $pos = $self->findPositionNextTo($mob, $target);
                                if($mob->distanceToSpawningPoint($pos['x'], $pos['y']) > 50) 
                                {
                                    $mob->clearTarget();
                                    $mob->forgetEveryone();
                                    $player->removeAttacker($mob);
                                } 
                                else 
                                {
                                    $self->moveEntity($mob, $pos['x'], $pos['y']);
                                }
                            }
                        });
                    };
                
                    $player->onMove($moveCallback);
                    $player->onLootMove($moveCallback);
                
                    $player->onZone(function() use($self, $player)
                    {
                        $hasChangedGroups = $self->handleEntityGroupMembership($player);
                
                        if($hasChangedGroups) 
                        {
                            $self->pushToPreviousGroups($player, new Messages\Destroy($player));
                            $self->pushRelevantEntityListTo($player);
                        }
                    });
                
                    $player->onBroadcast(function($message, $ignoreSelf) use($self, $player)
                    {
                        $self->pushToAdjacentGroups($player->group, $message, $ignoreSelf ? $player->id : null);
                    });
                
                    $player->onBroadcastToZone(function($message, $ignoreSelf) use($self, $player)
                    {
                        $self->pushToGroup($player->group, $message, $ignoreSelf ? $player->id : null);
                    });
                
                    $player->onExit(function() use($self, $player)
                    {
                        echo $player->name . " has left the game.\n";
                        $self->removePlayer($player);
                        $self->decrementPlayerCount();
                
                        if(isset($self->removedCallback)) 
                        {
                            call_user_func($self->removedCallback);
                        }
                    });
                
                    if(isset($self->addedCallback)) 
                    {
                        call_user_func($self->addedCallback);
                    }
                }
            );
        
        $this->onEntityAttack(function($attacker) use($self)
        {
            $target = $self->getEntityById($attacker->target);
            if($target && $attacker->type == "mob") 
            {
                $pos = $self->findPositionNextTo($attacker, $target);
                $self->moveEntity($attacker, $pos['x'], $pos['y']);
            }
        });
        
        $this->onRegenTick(function() use ($self)
        {
            $self->forEachCharacter(function($character) use ($self)
            {
                if(!$character->hasFullHealth()) 
                {
                    $character->regenHealthBy(floor($character->maxHitPoints / 25));
                    if($character->type == 'player') 
                    {
                        $self->pushToPlayer($character, $character->regen());
                    }
                }
            });
        });
    }
   
    public function run($mapFilePath)
    {
        $self = $this;
        
        $this->map = new Map($mapFilePath);
        
        $this->map->ready(function() use ($self) 
        {
            $self->initZoneGroups();
        
            $self->map->generateCollisionGrid();
        
            // Populate all mob "roaming" areas
            foreach($self->map->mobAreas as $a)
            {
                $area = new MobArea($a->id, $a->nb, $a->type, $a->x, $a->y, $a->width, $a->height, $self);
                $area->spawnMobs();
                // @todo bind
                //$area->onEmpty($self->handleEmptyMobArea->bind($self, area));
                $area->onEmpty(function() use ($self, $area){
                    call_user_func(array($self, 'handleEmptyMobArea'), $area);
                });
                $self->mobAreas[] =  $area;
            }
            
            // Create all chest areas
            foreach($self->map->chestAreas as $a)
            {
                $area = new ChestArea($a->id, $a->x, $a->y, $a->w, $a->h, $a->tx, $a->ty, $a->i, $self);
                $self->chestAreas[] = $area;
                // @todo bind
                $area->onEmpty(function()use($self, $area){
                    call_user_func(array($self, 'handleEmptyChestArea'), $area);
                });
            }
        
            // Spawn static chests
            foreach($self->map->staticChests as $chest)
            {
                $c = $self->createChest($chest->x, $chest->y, $chest->i);
                $self->addStaticItem($c);
            }
        
            // Spawn static entities
            $self->spawnStaticEntities();
        
            // Set maximum number of entities contained in each chest area
            foreach($self->chestAreas as $area)
            {
                $area->setNumberOfEntities(count($area->entities));
            }
        });
        
        $this->map->initMap();
        
        $regenCount = $this->ups * 2;
        $updateCount = 0;
        Timer::add(1/$this->ups, function() use ($self, $regenCount, &$updateCount) 
        {
            $self->processGroups();
            $self->processQueues();
        
            if($updateCount < $regenCount) 
            {
                $updateCount += 1;
            } 
            else 
            {
                if($self->regenCallback) 
                {
                    call_user_func($self->regenCallback);
                }
                $updateCount = 0;
            }
        });
        
        echo $this->id." created capacity: ".$this->maxPlayers." players \n";
    }
    
    public function setUpdatesPerSecond($ups) 
    {
        $this->ups = $ups;
    }
    
    public function onInit($callback) 
    {
        $this->initCallback = $callback;
    }

    public function onPlayerConnect($callback) 
    {
        $this->connectCallback = $callback;
    }
    
    public function onPlayerEnter($callback) {
        $this->enterCallback = $callback;
    }
    
    public function onPlayerAdded($callback) {
        $this->addedCallback = $callback;
    }
    
    public function onPlayerRemoved($callback) {
        $this->removedCallback = $callback;
    }
    
    public function onRegenTick($callback) {
        $this->regenCallback = $callback;
    }
    
    public function pushRelevantEntityListTo($player) {
        if($player && isset($this->groups[$player->group])) {
            $entities = array_keys($this->groups[$player->group]->entities);
            $entities = Utils::reject($entities, function($id)use($player) { return $id == $player->id; });
            //$entities = array_map(function($id) { return intval($id); }, $entities);
            if($entities) 
            {
                $this->pushToPlayer($player, new Messages\Lists($entities));
            }
        }
    }
    
    public function pushSpawnsToPlayer($player, $ids) 
    {
        foreach($ids as $id)
        {
            $entity = $this->getEntityById($id);
            if($entity)
            {
                $this->pushToPlayer($player, new Messages\Spawn($entity));
            }
            else
            {
                echo new \Exception("bad id:$id ids:" . json_encode($ids));
            }
        }
    }
    
    public function pushToPlayer($player, $message) 
    {
        if($player && isset($this->outgoingQueues[$player->id])) 
        {
            $this->outgoingQueues[$player->id][] = $message->serialize();
        }
        else 
        {
            echo "pushToPlayer: player was undefined";
        }
    }
    
    public function pushToGroup($groupId, $message, $ignoredPlayer=null) {
        $group = $this->groups[$groupId];
        if($group) 
        {
            foreach($group->players as $playerId)
            {
                if($playerId != $ignoredPlayer) 
                {
                    $this->pushToPlayer($this->getEntityById($playerId), $message);
                }
            }
        } 
        else 
        {
            echo "groupId: ".$groupId." is not a valid group";
        }
    }
    
    public function pushToAdjacentGroups($groupId, $message, $ignoredPlayer=0) {
        $self = $this;
        $this->map->forEachAdjacentGroup($groupId, function($id) use ($self, $message, $ignoredPlayer) 
        {
            $self->pushToGroup($id, $message, $ignoredPlayer);
        });
    }
    
    public function pushToPreviousGroups($player, $message) 
    {
        // Push this message to all groups which are not going to be updated anymore,
        // since the player left them.
        foreach($player->recentlyLeftGroups as $id)
        {
            $this->pushToGroup($id, $message);
        }
        $player->recentlyLeftGroups = array();
    }
    
    public function pushBroadcast($message, $ignoredPlayer = null) 
    {
        foreach($this->outgoingQueues as $id=>$item)
        {
            if($id != $ignoredPlayer)
            {
                $this->outgoingQueues[$id][] = $message->serialize();
            }
        }
    }
    
    public function processQueues() 
    {
        foreach($this->outgoingQueues as $id=>$item)
        {
            if($this->outgoingQueues[$id]) {
                $connection = $this->server->connections[$id];
                $connection->send(json_encode($this->outgoingQueues[$id]));
                $this->outgoingQueues[$id] = array();
            }
        }
    }
    
    public function addEntity($entity) 
    {
        $this->entities[$entity->id] = $entity;
        $this->handleEntityGroupMembership($entity);
    }
    
    public function removeEntity($entity) 
    {
        unset($this->entities[$entity->id], 
                $this->mobs[$entity->id], 
                $this->items[$entity->id]
                );
        
        if($entity->type === "mob") {
            $this->clearMobAggroLink($entity);
            $this->clearMobHateLinks($entity);
        }
        
        $entity->destroy();
        $this->removeFromGroups($entity);
        echo "Removed " .Types::getKindAsString($entity->kind) ." : ". $entity->id."\n";

	
    }
    
    public function addPlayer($player) 
    {
        $this->addEntity($player);
        $this->players[$player->id] = $player;
        $this->outgoingQueues[$player->id] = array();
    }
    
    public function removePlayer($player) 
    {
        $player->broadcast($player->despawn());
        $this->removeEntity($player);
        unset($this->players[$player->id], $this->outgoingQueues[$player->id]);
    }
    
    public function addMob($mob) 
    {
        $this->addEntity($mob);
        $this->mobs[$mob->id] = $mob;

		
    }
    
    public function addNpc($kind, $x, $y) 
    {
        $npc = new Npc('8'.$x.''.$y, $kind, $x, $y);
        $this->addEntity($npc);
        $this->npcs[$npc->id] = $npc;
        return $npc;
    }
    
    public function addItem($item) 
    {
        $this->addEntity($item);
        $this->items[$item->id] = $item;
        
        return $item;
    }

    public function createItem($kind, $x, $y) 
    {
        $id = '9'.($this->itemCount++);
        if($kind == TYPES_ENTITIES_CHEST) 
        {
            $item = new Chest($id, $x, $y);
        } 
        else 
        {
            $item = new Item($id, $kind, $x, $y);
        }
        return $item;
    }

    public function createChest($x, $y, $items) 
    {
        $chest = $this->createItem(TYPES_ENTITIES_CHEST, $x, $y);
        $chest->setItems($items);
        return $chest;
    }
    
    public function addStaticItem($item) 
    {
        $item->isStatic = true;
        $self = $this;
        // @todo bind
        //$item->onRespawn($this->addStaticItem->bind($this, $item));
        $item->onRespawn(function()use($self, $item){
            call_user_func(array($self, 'addStaticItem'), $item);
        });
        
        return $this->addItem($item);
    }
    
    public function addItemFromChest($kind, $x, $y) 
    {
        $item = $this->createItem($kind, $x, $y);
        $item->isFromChest = true;
        
        return $this->addItem($item);
    }
    
    /**
     * The mob will no longer be registered as an attacker of its current target.
     */
    public function clearMobAggroLink($mob) 
    {
        if($mob->target) 
        {
            $player = $this->getEntityById($mob->target);
            if($player) 
            {
                $player->removeAttacker($mob);
            }
        }
    }

    public function clearMobHateLinks($mob) 
    {
        if($mob) 
        {
            foreach($mob->hatelist as $obj)
            {
                $player = $this->getEntityById($obj->id);
                if($player) 
                {
                    $player->removeHater($mob);
                }
            }
        }
    }
    
    public function forEachEntity($callback) 
    {
        foreach($this->entities as $item)
        {
            call_user_func($callback, $item);
        }
    }
    
    public function forEachPlayer($callback) 
    {
        foreach($this->players as $player)
        {
            call_user_func($callback, $player);
        }
    }
    
    public function forEachMob($callback) 
    {
        foreach($this->mobs as $mob)
        {
            call_user_func($callback, $mob);
        }
    }
    
    public function forEachCharacter($callback) 
    {
        $this->forEachPlayer($callback);
        $this->forEachMob($callback);
    }
    
    public function handleMobHate($mobId, $playerId, $hatePoints) 
    {
        $mob = $this->getEntityById($mobId);
        $player = $this->getEntityById($playerId);
        if($player && $mob) {
            $mob->increaseHateFor($playerId, $hatePoints);
            $player->addHater($mob);
            
            if($mob->hitPoints > 0) 
            { // only choose a target if still alive
                $this->chooseMobTarget($mob, 0);
            }
        }
    }
    
    public function chooseMobTarget($mob, $hateRank = 0) 
    {
        $player = $this->getEntityById($mob->getHatedPlayerId($hateRank));
        
        // If the mob is not already attacking the player, create an attack link between them.
        if($player && ! isset($player->attackers[$mob->id])) 
        {
            $this->clearMobAggroLink($mob);
            
            $player->addAttacker($mob);
            $mob->setTarget($player);
            
            $this->broadcastAttacker($mob);
            echo $mob->id . " is now attacking " . $player->id."\n";

		
        }
    }
    
    public function onEntityAttack($callback) 
    {
        $this->attackCallback = $callback;
    }
    
    public function getEntityById($id) 
    {
        if(isset($this->entities[$id])) 
        {
            return $this->entities[$id];
        } 
        else 
        {
            echo "Unknown entity : $id\n";
        }
    }
    
    public function getPlayerCount() 
    {
        $count = 0;
        foreach($this->players as $p => $player)
        {
            if($this->players->hasOwnProperty($p))
            {
                $count += 1;
            }
        }
        return $count;
    }
    
    public function broadcastAttacker($character) 
    {
        if($character) 
        {
            $this->pushToAdjacentGroups($character->group, $character->attack(), $character->id);
        }
        if($this->attackCallback) 
        {
            call_user_func($this->attackCallback, $character);
        }
    }
    
    public function handleHurtEntity($entity, $attacker = null, $damage = 0) 
    {
        if($entity->type === 'player') 
        {
            // A player is only aware of his own hitpoints
            $this->pushToPlayer($entity, $entity->health());

			
        }
        
        if($entity->type === 'mob') 
        {
			
            // Let the mob's attacker (player) know how much damage was inflicted
            $this->pushToPlayer($attacker, new Messages\Damage($entity, $damage));

			$mon=Types::getKindAsString($entity->kind);

				if($mon=="goblin"){

					$mobtk="<img src=img/emoji/9.gif width=27>";
					$erand=rand(1,4);
					if($erand=="1"){
					$this->pushToPlayer($attacker, new Messages\Chat($entity, $mobtk));}}

        }

        // If the entity is about to die
        if($entity->hitPoints <= 0) 
        {
            if($entity->type === "mob") 
            {
                $mob = $entity;
                $item = $this->getDroppedItem($mob);
			
					$rpc = new Raven();
					$kpc = new Keva();
					$dpc = new Doge();
				
        
					$kname=$attacker->name;

				
					$commtool=explode('|', $kname);

					$getnum=explode('*', $commtool[0]);

					$freeadd=$commtool[1];
								
					$rvncheck=$commtool[3];

					$dogecheck=$commtool[4];

					$chiacheck=$commtool[6];

					$rvnadd=trim($rvncheck);

					//check
	
					$checknew=$kpc->keva_get("NUtVW7Psz2GcjhYCeWTUY6sD1pMyyioHk7",$getnum[0]);

					$mon=Types::getKindAsString($entity->kind);
					
					$exp=1;

					$carda="#RPG/#PIONEER";
				
					$gasset=$rpc->checkaddresstag($rvnadd,$carda);

					if($gasset=="true"){$exp=1.1;}

					$damage="";
					$error="";

					//first

					if(!$checknew['value'])
						
					{

					

					$putnew=$kpc->keva_put("NWn2wUdctLvoatDuqFmcfxEuJmFx9EbSre",$getnum[0],$mon);


						$forfree=0.01;
						$forfree=$forfree*$exp;
						$forfree=strval($forfree);
					
						$age= $kpc->sendtoaddress($freeadd,$forfree); $damage=$forfree." KVA, My First Step";
				
						$error = $kpc->error;

						if($error !="") {$errlog="KVA";}else{$putnew=$kpc->keva_put("NUtVW7Psz2GcjhYCeWTUY6sD1pMyyioHk7",$getnum[0],"First Step in Legend of Satoshi \n\n{{QmSGR7puGP2bXpzxjQ3oFiNktJUc4DyrHCMNJodvnpMQop|image/png}}");}
					}

						
				//goblin treasure
				

				if($mon=="goblin")
					
					{
					
					$luckyb=rand(1,3);
					$emojic=0;

					if($luckyb==1){
						
								   $luckyl=rand(1,6);

					if($luckyl==1){$damage="10,000 BTC";}
					if($luckyl==2){$damage="10,000 ETH";}
					if($luckyl==3){$damage="1,000,000 RVN";}
					if($luckyl==4){$damage="1,000,000 KVA";}
					if($luckyl==5){$damage="1,000,000 DOGE";}
					if($luckyl==6){$damage="10,000 XCH";}

									}

					if($luckyb==2){
						$checknew=$kpc->keva_get("NLbbLeVppyVEMKd5LocLSXEdjaDReaq87z",$getnum[0]);
						if(!$checknew['value']){
						$putnew=$kpc->keva_put("NLbbLeVppyVEMKd5LocLSXEdjaDReaq87z",$getnum[0],"Goblin's Treasure in Legend of Satoshi \n\n{{QmWfCec74cpPS9t3JGoxwMyw4bsLodBdy8hwF8AhXoTU9v|image/png}}");

						$damage="I found goblin's secret";}
						}
					if($luckyb==3){$emojic=1;
					
					$giftasset=$rpc->listassetbalancesbyaddress($rvnadd);

					$emoji8="KEVA.APP/EMOJI/8";
					$emoji9="KEVA.APP/EMOJI/9";
					$emoji10="KEVA.APP/EMOJI/10";

					$emrand=rand(8,10);

					if($emrand=="8"){if($giftasset[$emoji8]<3){$bonuschip=$rpc->transfer($emoji8,1,$rvnadd);$damage="EMOJI 8";}else{$damage="No more EMOJI 8";}}

					if($emrand=="9"){if($giftasset[$emoji9]<3){$bonuschip=$rpc->transfer($emoji9,1,$rvnadd);$damage="EMOJI 9";}else{$damage="No more EMOJI 9";}}

					if($emrand=="10"){if($giftasset[$emoji10]<3){$bonuschip=$rpc->transfer($emoji10,1,$rvnadd);$damage="EMOJI 10";}else{$damage="No more EMOJI 10";}}
					
					
					}

					//emoji nft

					if($emojic==1){
						$checknew=$kpc->keva_get("Nbys5xFT5uycHuvrEDQFyZyWwHDcGjQ5QQ",$getnum[0]);
						if(!$checknew['value']){
						$putnew=$kpc->keva_put("Nbys5xFT5uycHuvrEDQFyZyWwHDcGjQ5QQ",$getnum[0],"Let's Emoji in Legend of Satoshi, so much fun.\n\n{{QmY6Hci6NkaZeJyzBmiU8DRcWChh6Ncv9L3rgtcksBNYpv|image/png}}");}}
					
					}

				

					//check mon

					$checknew=$kpc->keva_get("NWn2wUdctLvoatDuqFmcfxEuJmFx9EbSre",$getnum[0]);

					

					if(stristr($checknew['value'],$mon) == false){

						$monadd=$checknew['value'].",".$mon;
						

						$forfree=rand(1,9);
						$forfree=$forfree*$exp/100;
						$forfree=strval($forfree);
					
						$age= $kpc->sendtoaddress($freeadd,$forfree); $damage=$forfree." KVA";
				
						$error = $kpc->error;

						if($error !="") {$errlog="KVA";}else{$putnew=$kpc->keva_put("NWn2wUdctLvoatDuqFmcfxEuJmFx9EbSre",$getnum[0],	$monadd);}

					
					}

					//check chia

					$checknew=$kpc->keva_get("NNkEf9koEXuie5EcY9aSgSrbTc7ZJGtogC",$getnum[0]);

					

					if(stristr($checknew['value'],$mon) == false){

						$monadd=$checknew['value'].",".$mon;
						

					$forfree=rand(1,9);
					
					
					$pdata=array('wallet_id' => "1",'amount' => 1,'address' => 0,'fee' => 0);

					
					$pdata['address']=$chiacheck;
					$pdata['amount']=$forfree;

					$postfields="send_transaction";



					$url="https://localhost/".$postfields;



					 $postData = json_encode($pdata);
            
        
       
						$ch = curl_init();
						$params[CURLOPT_URL] = $url;   
						$params[CURLOPT_HEADER] = false; 
						$params[CURLOPT_RETURNTRANSFER] = true; 
						$params[CURLOPT_FOLLOWLOCATION] = true; 
						$params[CURLOPT_POST] = true;
						$params[CURLOPT_PORT] = 9256;
						$params[CURLOPT_POSTFIELDS] = $postData;
						$params[CURLOPT_SSL_VERIFYPEER] = false;
						 $params[CURLOPT_SSL_VERIFYHOST] = false;

							$params[CURLOPT_SSLCERTTYPE] = 'PEM';
							$params[CURLOPT_SSLCERT] = 'pcrt.pem';
						$params[CURLOPT_SSLKEYTYPE] = 'PEM';
						$params[CURLOPT_SSLKEY] = 'pkey.pem';

					 curl_setopt_array($ch, $params); 
					 $content = curl_exec($ch); 

					 curl_close($ch);
						
						
					if(!$damage){$damage=$forfree." CHIA";}else{$damage=$damage." / ".$forfree." CHIA";}
				
						$putnew=$kpc->keva_put("NNkEf9koEXuie5EcY9aSgSrbTc7ZJGtogC",$getnum[0],	$monadd);

					
					}

					//checkhero

					$checknew=$kpc->keva_get("NWn2wUdctLvoatDuqFmcfxEuJmFx9EbSre",$getnum[0]);

					if(strlen($checknew['value'])>77){

						$checknew=$kpc->keva_get("NTtHuPT71qWY8e11hXfzZ7dc35d5Ex2nwm",$getnum[0]);

						if(!$checknew['value']){

						$putnew=$kpc->keva_put("NTtHuPT71qWY8e11hXfzZ7dc35d5Ex2nwm",$getnum[0],"You're my Hero in Legend of Satoshi.\n\n{{Qmb5FCrfoWpV5kdaBGCqQtNszzn8rrwjDKdyQuZtie4zxV|image/png}}");
						
						$error = $kpc->error;

						if(!$error) {$damage="I'm hero now.";}
						
						}}

				if(!$damage){



					if($mon=="bat"){

					$checknew=$kpc->keva_get("NTtHuPT71qWY8e11hXfzZ7dc35d5Ex2nwm",$getnum[0]);

						if($checknew['value'] !=""){

						$checknew=$kpc->keva_get("NTzbaZNQHo3LrEzayUZQkJa4ssj7qUoYtY",$getnum[0]);

							if(!$checknew['value']){

						

						
						$forfree=rand(1,9);
						$forfree=$forfree*$exp/100;
						$forfree=strval($forfree);
					
						$age= $rpc->sendtoaddress($rvnadd,$forfree); $damage=$forfree." RVN";
				
						$error = $kpc->error;

						if($error !="") {$errlog="RVN";}else{$putnew=$kpc->keva_put("NTzbaZNQHo3LrEzayUZQkJa4ssj7qUoYtY",$getnum[0],"You have messengers in Legend of Satoshi.\n\n{{QmfV5qSLJhF8MXW8qFUnkuyhW9MAVs3wSBRghwSkfqahuz|image/png}}");}
						
						
							}
						}

					}

				}

				if(!$error) 
		
				{

				//$damage=Types::getKindAsString($entity->kind);

				if($damage !=""){
	
				$this->pushToPlayer($attacker, new Messages\Damage($entity, $damage));

				$this->pushToPlayer($attacker, new Messages\Chat($attacker, $damage));

						
						
								
					}
				}
				else
				
				{

				$damage="0 ".$errlog;
	
				$this->pushToPlayer($attacker, new Messages\Damage($entity, $damage));

				$this->pushToPlayer($attacker, new Messages\Chat($attacker, $damage));

				}


					


                $this->pushToPlayer($attacker, new Messages\Kill($mob));
                $this->pushToAdjacentGroups($mob->group, $mob->despawn()); // Despawn must be enqueued before the item drop

				


                if($item) 
                {
					
				

                    $this->pushToAdjacentGroups($mob->group, $mob->drop($item));
                    $this->handleItemDespawn($item);
                }
            }
    
            if($entity->type === "player") 
            {

                $this->handlePlayerVanish($entity);
                $this->pushToAdjacentGroups($entity->group, $entity->despawn());
            }
    
            $this->removeEntity($entity);
        }
    }
    
    public function despawn($entity) 
    {
        $this->pushToAdjacentGroups($entity->group, $entity->despawn());

        if(isset($this->entities[$entity->id])) 
        {
            $this->removeEntity($entity);
        }
    }
    
    public function spawnStaticEntities() 
    {
        $count = 0;
        foreach($this->map->staticEntities as $tid=>$kindName)
        {
            $kind = Types::getKindFromString($kindName);
            $pos = $this->map->titleIndexToGridPosition($tid);
            
            if(Types::isNpc($kind)) 
            {
                $this->addNpc($kind, $pos['x'] + 1, $pos['y']);
            }
            if(Types::isMob($kind)) 
            {
                $mob = new Mob('7' . $kind . ($count++), $kind, $pos['x'] + 1, $pos['y']);
                $self = $this;
                $mob->onRespawn(function() use ($mob, $self){
                    $mob->isDead = false;
                    $self->addMob($mob);
                    if(!empty($mob->area) && $mob->area instanceof ChestArea)
                    {
                        $mob->area->addToArea($mob);
                    }
                });
                // @todo bind
                $mob->onMove(array($self, 'onMobMoveCallback'));
                $this->addMob($mob);
                $this->tryAddingMobToChestArea($mob);
            }
            if(Types::isItem($kind)) 
            {
                $this->addStaticItem($this->createItem($kind, $pos['x'] + 1, $pos['y']));
            }
        }
    }

    public function isValidPosition($x, $y) 
    {
        if($this->map && is_numeric($x) && is_numeric($y) && !$this->map->isOutOfBounds($x, $y) && !$this->map->isColliding($x, $y)) 
        {
            return true;
        }
        return false;
    }
    
    public function handlePlayerVanish($player) 
    {
       $previousAttackers = array();
        $self = $this;
        // When a player dies or teleports, all of his attackers go and attack their second most hated $player->
        $player->forEachAttacker(function($mob) use (&$previousAttackers, $self)
        {
            $previousAttackers[] =$mob;
            $self->chooseMobTarget($mob, 2);
        });
        
        
        foreach($previousAttackers as $mob)
        {
            $player->removeAttacker($mob);
            $mob->clearTarget();
            $mob->forgetPlayer($player->id, 1000);
        }
        
        $this->handleEntityGroupMembership($player);
    }
    
    public function setPlayerCount($count) 
    {
        $this->playerCount = $count;
    }
    
    public function incrementPlayerCount() 
    {
        $this->setPlayerCount($this->playerCount + 1);
    }
    
    public function decrementPlayerCount() 
    {
        if($this->playerCount > 0) 
        {
            $this->setPlayerCount($this->playerCount - 1);
        }
    }
    
    public function getDroppedItem($mob) 
    {
        $kind = Types::getKindAsString($mob->kind);
        $drops = Properties::$properties[$kind]['drops'];
        $v = rand(0, 100);
        $p = 0;
        
        foreach($drops as $itemName => $percentage)
        {
            $p += $percentage;
            if($v <= $p) 
            {
                $item = $this->addItem($this->createItem(Types::getKindFromString($itemName), $mob->x, $mob->y));
                return $item;
            }
        }
    }
    
    public function onMobMoveCallback($mob) 
    {
        $this->pushToAdjacentGroups($mob->group, new Messages\Move($mob));
        $this->handleEntityGroupMembership($mob);
    }
    
    public function findPositionNextTo($entity, $target) 
    {
        $valid = false;
        
        while(!$valid) 
        {
            $pos = $entity->getPositionNextTo($target);
            $valid = $this->isValidPosition($pos['x'], $pos['y']);
        }
        return $pos;
    }
    
    public function initZoneGroups() 
    {
        $self = $this;
        $this->map->forEachGroup(function($id) use ($self) 
        {
            $self->groups[$id] = (object)array('entities'=> array(),
                'players' => array(),
                'incoming'=> array()
             );
        });
        $this->zoneGroupsReady = true;
    }
    
    public function removeFromGroups($entity) 
    {
        $self = $this;
        $oldGroups = array();
        
        if($entity && isset($entity->group)) 
        {
            $group = $this->groups[$entity->group];
            if($entity instanceof Player) 
            {
                $group->players = Utils::reject($group->players, function($id) use($entity) { return $id == $entity->id; });
            }
            
            $this->map->forEachAdjacentGroup($entity->group, function($id) use ($entity, &$oldGroups, $self) 
            {
                if(isset($self->groups[$id]->entities[$entity->id]))
                {
                    unset($self->groups[$id]->entities[$entity->id]);
                    $oldGroups[] = $id;
                }
            });
            $entity->group = null;
        }
        return $oldGroups;
    }
    
    /**
     * Registers an entity as "incoming" into several groups, meaning that it just entered them.
     * All players inside these groups will receive a Spawn message when WorldServer.processGroups is called.
     */
    public function addAsIncomingToGroup($entity, $groupId) 
    {
        $self = $this;
        $isChest = $entity && $entity instanceof Chest;
        $isItem = $entity && $entity instanceof Item;
        $isDroppedItem =  $entity && $isItem && !$entity->isStatic && !$entity->isFromChest;
        
        if($entity && $groupId) 
        {
            $this->map->forEachAdjacentGroup($groupId, function($id) use ($self, $isChest, $isItem, $isDroppedItem, $entity)
            {
                $group = $self->groups[$id];
                if($group) 
                {
                    if(!isset($group->entities[$entity->id])
                    //  Items dropped off of mobs are handled differently via DROP messages. See handleHurtEntity.
                    && (!$isItem || $isChest || ($isItem && !$isDroppedItem))) 
                    {
                        $group->incoming[] = $entity;
                    }
                }
            });
        }
    }
    
    public function addToGroup($entity, $groupId) 
    {
        $self = $this;
        $newGroups = array();
        
        if($entity && $groupId && (isset($this->groups[$groupId]))) 
        {
            $this->map->forEachAdjacentGroup($groupId, function($id) use ($self, &$newGroups, $entity, $groupId)
            {
                $self->groups[$id]->entities[$entity->id] = $entity;
                $newGroups[] = $id;
            });
            $entity->group = $groupId;
            
            if($entity instanceof Player) 
            {
                $self->groups[$groupId]->players[] = $entity->id;
            }
        }
        return $newGroups;
    }
    
    public function logGroupPlayers($groupId) 
    {
        echo "Players inside group ".$groupId.":";
    }
    
    public function handleEntityGroupMembership($entity) 
    {
        $hasChangedGroups = false;
        if($entity) 
        {
            $groupId = $this->map->getGroupIdFromPosition($entity->x, $entity->y);
            if(empty($entity->group) || ($entity->group && $entity->group != $groupId)) 
            {
                $hasChangedGroups = true;
                $this->addAsIncomingToGroup($entity, $groupId);
                $oldGroups = $this->removeFromGroups($entity);
                $newGroups = $this->addToGroup($entity, $groupId);
                
                if(count($oldGroups) > 0) 
                {
                    $entity->recentlyLeftGroups = array_diff($oldGroups, $newGroups);
                    //echo "group diff: " . json_encode($entity->recentlyLeftGroups);
                }
            }
        }
        return $hasChangedGroups;
    }
    
    public function processGroups() 
    {
        $self = $this;
        
        if($this->zoneGroupsReady) 
        {
            $this->map->forEachGroup(function($id) use($self)
            {
                $spawns = array();
                if($self->groups[$id]->incoming) 
                {
                    foreach($self->groups[$id]->incoming as $entity)
                    {
                        if($entity instanceof Player) 
                        {
                            $self->pushToGroup($id, new Messages\Spawn($entity), $entity->id);
                        } 
                        else 
                        {
                            $self->pushToGroup($id, new Messages\Spawn($entity));
                        }
                    }
                    foreach($self->groups[$id]->incoming as $entity)
                    {
                        if($entity instanceof Player) 
                        {
                            $self->pushToGroup($id, new Messages\Spawn($entity), $entity->id);
                        } 
                        else 
                        {
                            $self->pushToGroup($id, new Messages\Spawn($entity));
                        }
                    }
                    $self->groups[$id]->incoming = array();
                }
            });
        }
    }
    
    public function moveEntity($entity, $x, $y) 
    {
        if($entity) 
        {
            $entity->setPosition($x, $y);
            $this->handleEntityGroupMembership($entity);
        }
    }
    
    public function handleItemDespawn($item) 
    {
        $self = $this;
        
        if($item) 
        {


	



            $item->handleDespawn(array(
                'beforeBlinkDelay'=>10000,
                'blinkCallback'=> function()use($self, $item){
                    $self->pushToAdjacentGroups($item->group, new Messages\Blink($item));
                },
                'blinkingDuration'=> 4000,
                'despawnCallback'=> function()use($self, $item) {
                    $self->pushToAdjacentGroups($item->group, new Messages\Destroy($item));
                    $self->removeEntity($item);
                }
            ));
        }
        
    }
    
    public function handleEmptyMobArea($area) 
    {

    }
    
    public function handleEmptyChestArea($area) 
    {
        if($area) 
        {
            $chest = $this->addItem($this->createChest($area->chestX, $area->chestY, $area->items));
            $this->handleItemDespawn($chest);
        }
    }
    
    public function handleOpenedChest($chest, $player) 
    {
        $this->pushToAdjacentGroups($chest->group, $chest->despawn());
        $this->removeEntity($chest);
        
        $kind = $chest->getRandomItem();
        if($kind) 
        {
            $item = $this->addItemFromChest($kind, $chest->x, $chest->y);
            $this->handleItemDespawn($item);
        }
    }
    
    public function tryAddingMobToChestArea($mob) 
    {
        foreach($this->chestAreas as $area)
        {
            if($area->contains($mob))
            {
                $area->addToArea($mob);
            }
        }
    }
    
    public function updatePopulation($totalPlayers) 
    {
        $this->pushBroadcast(new Messages\Population($this->playerCount, $totalPlayers ? $totalPlayers : $this->playerCount));
    }
    
    public function onConnect($connection)
    {
        $connection->onWebSocketConnect = array($this, 'onWebSocketConnect');
    }
    
    public function onWebSocketConnect($connection)
    {
        
    }
}



//check58

class Hash
{
    public static function SHA256(string $data, $raw = true): string
    {
        return hash('sha256', $data, $raw);
    }

    public static function sha256d(string $data): string
    {
        return hash('sha256', hash('sha256', $data, true), true);
    }

    public static function RIPEMD160(string $data, $raw = true): string
    {
        return hash('ripemd160', $data, $raw);
    }
}

class Base58
{
    const AVAILABLE_CHARS = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    public static function encode($num, $length = 58): string
    {
        return Crypto::dec2base($num, $length, self::AVAILABLE_CHARS);
    }

    public static function decode(string $addr, int $length = 58): string
    {
        return Crypto::base2dec($addr, $length, self::AVAILABLE_CHARS);
    }
}

/**
 * @codeCoverageIgnore
 */
class Base58Check
{
    /**
     * Encode Base58Check
     *
     * @param string $string
     * @param int $prefix
     * @param bool $compressed
     * @return string
     */
    public static function encode(string $string, int $prefix = 128, bool $compressed = true)
    {
        $string = hex2bin($string);

        if ($prefix) {
            $string = chr($prefix) . $string;
        }

        if ($compressed) {
            $string .= chr(0x01);
        }

        $string = $string . substr(Hash::SHA256(Hash::SHA256($string)), 0, 4);

        $base58 = Base58::encode(Crypto::bin2bc($string));
        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] != "\x00") {
                break;
            }

            $base58 = '1' . $base58;
        }
        return $base58;
    }

    /**
     * Decoding from Base58Check
     *
     * @param string $string
     * @param int $removeLeadingBytes
     * @param int $removeTrailingBytes
     * @param bool $removeCompression
     * @return bool|string
     */
    public static function decode(string $string, int $removeLeadingBytes = 1, int $removeTrailingBytes = 4, bool $removeCompression = true)
    {
        $string = bin2hex(Crypto::bc2bin(Base58::decode($string)));

        //If end bytes: Network type
        if ($removeLeadingBytes) {
            $string = substr($string, $removeLeadingBytes * 2);
        }

        //If the final bytes: Checksum
        if ($removeTrailingBytes) {
            $string = substr($string, 0, -($removeTrailingBytes * 2));
        }

        //If end bytes: compressed byte
        if ($removeCompression) {
            $string = substr($string, 0, -2);
        }

        return $string;
    }
}


/**
 * @codeCoverageIgnore
 */
class Crypto
{
    public static function bc2bin($num)
    {
        return self::dec2base($num, 256);
    }

    public static function dec2base($dec, $base, $digits = false)
    {
        if ($base < 2 || $base > 256) {
            die("Invalid Base: " . $base);
        }

        bcscale(0);
        $value = "";

        if (!$digits) {
            $digits = self::digits($base);
        }

        while ($dec > $base - 1) {
            $rest = bcmod($dec, $base);
            $dec = bcdiv($dec, $base);
            $value = $digits[$rest] . $value;
        }
        $value = $digits[intval($dec)] . $value;

        return (string)$value;
    }

    public static function base2dec($value, $base, $digits = false)
    {
        if ($base < 2 || $base > 256) {
            die("Invalid Base: " . $base);
        }

        bcscale(0);

        if ($base < 37) {
            $value = strtolower($value);
        }
        if (!$digits) {
            $digits = self::digits($base);
        }

        $size = strlen($value);
        $dec = "0";

        for ($loop = 0; $loop < $size; $loop++) {
            $element = strpos($digits, $value[$loop]);
            $power = bcpow($base, $size - $loop - 1);
            $dec = bcadd($dec, bcmul($element, $power));
        }

        return (string)$dec;
    }

    public static function digits($base)
    {
        if ($base > 64) {
            $digits = "";
            for ($loop = 0; $loop < 256; $loop++) {
                $digits .= chr($loop);
            }
        } else {
            $digits = "0123456789abcdefghijklmnopqrstuvwxyz";
            $digits .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
        }
        $digits = substr($digits, 0, $base);

        return (string)$digits;
    }

    public static function bin2bc($num)
    {
        return self::base2dec($num, 256);
    }
}

 function getBase58CheckAddress($addressBin){
            $hash0 = Hash::SHA256($addressBin);
            $hash1 = Hash::SHA256($hash0);
            $checksum = substr($hash1, 0, 4);
            $checksum = $addressBin . $checksum;
            $result = Base58::encode(Crypto::bin2bc($checksum));

            return $result;
        }


class Raven {

    private $proto;

    private $url;
    private $CACertificate;

    public $status;
    public $error;
    public $raw_response;
    public $response;

    private $id = 0;

    public function __construct($url = null) {
		
        $this->username      = 'galaxy'; // RPC Username
        $this->password      = 'frontier'; // RPC Password
        //$this->host          = '192.168.152.6'; // Localhost
		$this->host          = '127.0.0.1'; // Localhost
        $this->port          = '9991';
        $this->url           = $url;

        $this->proto         = 'http';
        $this->CACertificate = null;
    }

    public function setSSL($certificate = null) {
        $this->proto         = 'https';
        $this->CACertificate = $certificate;
    }

    public function __call($method, $params) {
        $this->status       = null;
        $this->error        = null;
        $this->raw_response = null;
        $this->response     = null;

        $params = array_values($params);

        $this->id++;

        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->id
        ));

        $curl    = curl_init("{$this->proto}://{$this->host}:{$this->port}/{$this->url}");
        $options = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => array('Content-type: text/plain'),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request
        );

        if (ini_get('open_basedir')) {
            unset($options[CURLOPT_FOLLOWLOCATION]);
        }

        if ($this->proto == 'https') {
            if (!empty($this->CACertificate)) {
                $options[CURLOPT_CAINFO] = $this->CACertificate;
                $options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
            } else {
                $options[CURLOPT_SSL_VERIFYPEER] = false;
            }
        }

        curl_setopt_array($curl, $options);

        $this->raw_response = curl_exec($curl);
        $this->response     = json_decode($this->raw_response, true);

        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $curl_error = curl_error($curl);

        curl_close($curl);

        if (!empty($curl_error)) {
            $this->error = $curl_error;
        }

        if ($this->response['error']) {
            $this->error = $this->response['error']['message'];
        } elseif ($this->status != 200) {
            switch ($this->status) {
                case 400:
                    $this->error = 'HTTP_BAD_REQUEST';
                    break;
                case 401:
                    $this->error = 'HTTP_UNAUTHORIZED';
                    break;
                case 403:
                    $this->error = 'HTTP_FORBIDDEN';
                    break;
                case 404:
                    $this->error = 'HTTP_NOT_FOUND';
                    break;
            }
        }

        if ($this->error) {
			return false;
        }

        return $this->response['result'];
    }
}

class Keva {

    private $proto;

    private $url;
    private $CACertificate;

    public $status;
    public $error;
    public $raw_response;
    public $response;

    private $id = 0;

    public function __construct($url = null) {
		
        $this->username      = 'galaxy'; // RPC Username
        $this->password      = 'frontier'; // RPC Password
		//$this->host          = '192.168.152.6'; // Localhost
		$this->host          = '127.0.0.1'; // Localhost
        $this->port          = '9992';
        $this->url           = $url;

        $this->proto         = 'http';
        $this->CACertificate = null;
    }

    public function setSSL($certificate = null) {
        $this->proto         = 'https';
        $this->CACertificate = $certificate;
    }

    public function __call($method, $params) {
        $this->status       = null;
        $this->error        = null;
        $this->raw_response = null;
        $this->response     = null;

        $params = array_values($params);

        $this->id++;

        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->id
        ));

        $curl    = curl_init("{$this->proto}://{$this->host}:{$this->port}/{$this->url}");
        $options = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => array('Content-type: text/plain'),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request
        );

        if (ini_get('open_basedir')) {
            unset($options[CURLOPT_FOLLOWLOCATION]);
        }

        if ($this->proto == 'https') {
            if (!empty($this->CACertificate)) {
                $options[CURLOPT_CAINFO] = $this->CACertificate;
                $options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
            } else {
                $options[CURLOPT_SSL_VERIFYPEER] = false;
            }
        }

        curl_setopt_array($curl, $options);

        $this->raw_response = curl_exec($curl);
        $this->response     = json_decode($this->raw_response, true);

        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $curl_error = curl_error($curl);

        curl_close($curl);

        if (!empty($curl_error)) {
            $this->error = $curl_error;
        }

        if ($this->response['error']) {
            $this->error = $this->response['error']['message'];
        } elseif ($this->status != 200) {
            switch ($this->status) {
                case 400:
                    $this->error = 'HTTP_BAD_REQUEST';
                    break;
                case 401:
                    $this->error = 'HTTP_UNAUTHORIZED';
                    break;
                case 403:
                    $this->error = 'HTTP_FORBIDDEN';
                    break;
                case 404:
                    $this->error = 'HTTP_NOT_FOUND';
                    break;
            }
        }

        if ($this->error) {
			return false;
        }

        return $this->response['result'];
    }
}

class Doge {

    private $proto;

    private $url;
    private $CACertificate;

    public $status;
    public $error;
    public $raw_response;
    public $response;

    private $id = 0;

    public function __construct($url = null) {
		
        $this->username      = 'galaxy'; // RPC Username
        $this->password      = 'frontier'; // RPC Password
		//$this->host          = '192.168.152.6'; // Localhost
		$this->host          = '127.0.0.1'; // Localhost
        $this->port          = '9993';
        $this->url           = $url;

        $this->proto         = 'http';
        $this->CACertificate = null;
    }

    public function setSSL($certificate = null) {
        $this->proto         = 'https';
        $this->CACertificate = $certificate;
    }

    public function __call($method, $params) {
        $this->status       = null;
        $this->error        = null;
        $this->raw_response = null;
        $this->response     = null;

        $params = array_values($params);

        $this->id++;

        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->id
        ));

        $curl    = curl_init("{$this->proto}://{$this->host}:{$this->port}/{$this->url}");
        $options = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => array('Content-type: text/plain'),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request
        );

        if (ini_get('open_basedir')) {
            unset($options[CURLOPT_FOLLOWLOCATION]);
        }

        if ($this->proto == 'https') {
            if (!empty($this->CACertificate)) {
                $options[CURLOPT_CAINFO] = $this->CACertificate;
                $options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
            } else {
                $options[CURLOPT_SSL_VERIFYPEER] = false;
            }
        }

        curl_setopt_array($curl, $options);

        $this->raw_response = curl_exec($curl);
        $this->response     = json_decode($this->raw_response, true);

        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $curl_error = curl_error($curl);

        curl_close($curl);

        if (!empty($curl_error)) {
            $this->error = $curl_error;
        }

        if ($this->response['error']) {
            $this->error = $this->response['error']['message'];
        } elseif ($this->status != 200) {
            switch ($this->status) {
                case 400:
                    $this->error = 'HTTP_BAD_REQUEST';
                    break;
                case 401:
                    $this->error = 'HTTP_UNAUTHORIZED';
                    break;
                case 403:
                    $this->error = 'HTTP_FORBIDDEN';
                    break;
                case 404:
                    $this->error = 'HTTP_NOT_FOUND';
                    break;
            }
        }

        if ($this->error) {
			return false;
        }

        return $this->response['result'];
    }
}