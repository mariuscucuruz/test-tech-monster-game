<?php
class Game {
	/**
	* Array to hold the file path
	* private
	*/
	private $mapFile = array();

	/**
	* An array that holds all the errors for return to the user if required
	*@access public
	*$var array
	*/
	public $errors = array();

	/**
	* Array to hold the monsters and their data
	* protected
			monster array template:
			moster1 array('name'=>'NAME', 'currLoc' => 'CITY', 'path' => array('0' => 'xxx', ... , 'n' => 'xxx') )
	*/
	protected $monsters = array();

	/**
	* Array to hold cities as they're being destroied
	* protected
	*/
	protected $destroiedCities = array();

	/**
	* Instantiates the Game class and parse the file
	* @return array with all the monsters
	*/
	function __construct($mapFile) 
	{
			$mapData = file_get_contents($mapFile); //read the file
			$mostersData = explode("\n", $mapData); //each line is a monster and their paths/travel options

			for ($i = 0; $i < count($mostersData); $i++)  
			{
						$monstersLine[$i] = explode(" ", $mostersData[$i]); //element 0 is the name, all the others are reachable destinations/paths

						if (count($monstersLine[$i]) > 1)
						{ //avoid empty elements
								//begin adding name to monster array
								$monsters[$i] = array(//add monster to array
																	'name' 	  => $monstersLine[$i][0],
																	'currLoc' => '' //will hold location after each battle
																	);

								for ($p = 1; $p < count($monstersLine[$i]); $p++) 
								{ //get the paths
									$monsterPaths[$i] = explode("=", $monstersLine[$i][$p]); //assign each array to a 'path' element of the associated monster
									$monsters[$i]['path'][$monsterPaths[$i][0]] = $monsterPaths[$i][1];
								}
						}
			}
			return $this->monsters = $monsters;
	}


	/**
	* Returns an array with all the monsters 
	* @return array with all the monster details
	*/
	function getMonsters() {
			return $this->monsters;
	}


	/**
	* Start the fight: move each monster one step and detirmine fighting points
	* @access public 
	* @param none
	* @return $fights array with cityName => monsterNames
	*/
	public function fight() 
	{
			foreach ($this->monsters as $monsterID => $monsterDetails)
			{ 	//move each monster
					$moves[$monsterID] = $this->moveMonster($monsterID);//
			}

			$battleGrounds = $this->determineFights($moves);
			//foreach ($battleGrounds as $cityName) {echo "fighting in $cityName<br />";}

			$monstersFighting = array_intersect($moves, $battleGrounds);

			$fights = array(); //array to hold fights details
			foreach ($monstersFighting as $monsterID => $cityName)
			{
					$fights[$cityName][] = $this->monsters[$monsterID]['name'];

					$this->destroyCity($cityName);
					$this->killMonster($monsterID);
			}

			/*echo "What's happening:<pre>monsterz in cities:". print_r($monstersFighting, true) ."</pre>".
							"<pre>fights:". print_r($this->determineFights($moves), true) ."</pre>".
							"<pre>destroied cities:". print_r($this->destroiedCities, true) ."</pre>";
			*/
			return $fights;
	}


	/**
	* moves a monster to on random path
	* @access public (or private?)
	* @param monsterID is the array key 
	* @return string destination city
	*/
	public function moveMonster($monsterID) 
	{
			$availPaths = $this->monsters[$monsterID]['path'];
			if (!in_array($availPaths[$movingTo], $this->destroiedCities))
			{
				$movingTo = array_rand($availPaths); //pick one of the available options
				$this->monsters[$monsterID]['currLoc'] = $availPaths[$movingTo];
				//echo "Monster ". $this->monsters[$monsterID]['name'] ." is moving $movingTo to ". $availPaths[$movingTo] .".<br />";
			}
			return $availPaths[$movingTo];
	}


	/**
	* finds collision points
	* @access public (or private?) 
	* @param $cities array with city names
	* @return array with cities where fights are happening
	*/
	public function determineFights($moves) 
	{
			$fights = array();

			foreach (array_count_values($moves) as $cityName => $monsterCount)
			{ 	//more than 1 monster found: FIGHT!
					if ($monsterCount > 1)
					{
							//echo "we've got a fight in <strong>$cityName</strong> ($monsterCount monsters).<br />";
							$fights[] = $cityName;
							//$fights[]['city'] = $cityName;
							//$fights[]['mosters'] = array();
					}
			}
			return $fights;
	}


	/**
	* removes cities from paths
	* @access public (or private?) 
	* @param $cities array with city names
	* @return array with cities where fights are happening
	*/
	public function destroyCity($cityName) 
	{
			if (!in_array($cityName, $this->destroiedCities))
			{
					$this->destroiedCities[] = $cityName; //expected behaviour
					return true;
			} else {
					//this shouldn't happen ever! (means a fight happened in an already destroed city)
					return false;
			}

			return false;//uncaught error?
	}

	/**
	* removes cities from paths
	* @access public (or private?) 
	* @param $cities array with city names
	* @return array with cities where fights are happening
	*/
	public function killMonster($monsterID) 
	{
			//echo "Killed ". $this->monsters[$monsterID]['name'] ."!<br />";
			unset($this->monsters[$monsterID]);
			return true;
	}


	/**
	* Continues the fight until no more collisions?
	* @to-do think this through: maybe a map should be drawn before all fighting begins so we can know what paths a monster can take on next...
	* @access public 
	* @param none
	* @return $fights array with cityName => monsterNames
	*/
	public function annihilation() 
	{ //assume monsters return to original position and start again

			$allPaths = array();
			$allPathsString = '';
			$collisionPoints = 0;

			foreach ($this->monsters as $monsterID => $monsterDetails)
			{
					$allPathsString .= implode(",", $monsterDetails['path']) .",";
			}
			$allPaths = explode(",", $allPathsString);
			//echo "<pre>paths:". print_r($allPaths, true) ."</pre>"; 

			foreach (array_count_values($allPaths) as $city => $collisions)
			{
				if ($collisions > 1)
				{
					$collisionPoints++;
					//echo "potential fighting grounds: <strong>".$city ."</strong><br />";
				}
			}
			if ($collisionPoints > 0)
			{
					echo "<h4>Fighting can go on <u>in $collisionPoints potential battlefronts</u> between the remaining ". count($this->monsters)." survivers!</h4>";
					//$this->fight();

					foreach ($this->fight() as $cityName => $monsterNames)
					{
							echo "<p>Fighting in <strong>$cityName</strong> between ". count($monsterNames) ." monsters.</p>";
					}


					$this->annihilation();
			}
			else {
				//return $this->monsters;
			}


	}

}