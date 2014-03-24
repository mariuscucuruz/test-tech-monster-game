<?php
/**
 * @author Marius Cucuruz
 * @package tech test for EuropeanTravelVentures.com
 * @date 20-03-2014
 * 
 */

error_reporting(E_ALL ^ E_NOTICE);
require_once('class.game.php');

$monsters = new Game('world_map_medium.txt');
$allMonsterz = $monsters->getMonsters();

?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="Marius Cucuruz" />
	<title>ech test for EuropeanTravelVentures.com</title>
</head>

<body>
<p>Startig off with <?=count($allMonsterz) ?> monsters.<br /></p>

<?php
//kick off!

		//show fights : city & monsters fighting
		foreach ($monsters->fight() as $cityName => $monsterNames)
		{
				echo "<p>Fighting in <strong>$cityName</strong>: ";//$allMonsterz[$monsterID]['name']
				for ($countMons = 0; $countMons < count($monsterNames); $countMons++) 
				{
					echo "<strong>". $monsterNames[$countMons] ."</strong>";
					echo ($countMons < (count($monsterNames)-1)) ? ", " : ".";
				}
				echo "</p>";
		}

		$survivers = $monsters->getMonsters();
		echo "<p>Who's survived so far (". count($survivers) ."):</p>
					<ul>";
					/* foreach($survivers as $monsterID => $monsterName) {
						echo "<li><strong>". $monsterName['name'] ."</strong>, curently in <em>". $survivers[$monsterID]['currLoc'] ."</em>.</li>";
					}*/ 
		echo "</ul>";
?>
<h3>Let's finish this in a blood bath!</h3>
<?php $monsters->annihilation(); ?>

<?php //show outcome
		$winners = $monsters->getMonsters();
		echo "<p>Who's survived in the end (". count($winners) ."):</p>
					<ul>";
					foreach($winners as $monsterID => $monsterName) {
						echo "<li><strong>". $monsterName['name'] ."</strong>, curently in <em>". $survivers[$monsterID]['currLoc'] ."</em>.</li>";
					}
		echo "</ul>";
?>

</body>
</html>