<?php
	define("CALLED", true);

	require_once "lib/mysql.lib.php";	
	require_once "lib/secureSessions.lib.php";
	require_once "lib/edffasUnique.lib.php";
	require_once "lib/m3Common.lib.php";
	
	$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
	$dbh = newDBHObject($mysqlConfig);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		require_once "lib/inputValidate.lib.php";
		
		$input = array();
		
		foreach($_POST as $key => $val) {
			$input[$key] = htmlspecialchars($val,ENT_QUOTES);
		}
		
		//print_r($input);
		
		$input["playerName"] = (isset($input["playerName"]) ? removeCMDRFromUsername($input["playerName"]):NULL);
		
		//print($_POST["playerName"]);
		
		$rules = array(
			array("inputName" => "playerName", "method" => "username", "methodArgs" => array("len" => array("min" => 2, "max" => 32, "failMsg" => "The length of the commander's name must be between 2 and 32")), "failMsg" => "The length of the commander's name must be between 2 and 32"),
			array("inputName" => "faction", "method" => "existsInDatabaseTable", "methodArgs" => array("database" => array("handle" => $dbh, "table" => "factions", "column" => "id", "type" => "failMsg", "Invalid faction ID")), "failMsg" => "You must select a faction from the list."),
			array("inputName" => "power", "method" => "existsInDatabaseTable", "methodArgs" => array("database" => array("handle" => $dbh, "table" => "powers", "column" => "id", "type" => "int", "failMsg" => "Invalid power ID")), "failMsg" => "You must select a power from the list."),
			array("inputName" => "ship", "method" => "existsInDatabaseTable", "methodArgs" => array("database" => array("handle" => $dbh, "table" => "ships", "column" => "id", "type" => "int", "failMsg" => "Invalid ship ID.")), "failMsg" => "You must select a ship from the list."),
			array("inputName" => "rank", "method" => "existsInDatabaseTable", "methodArgs" => array("database" => array("handle" => $dbh, "table" => "ranks", "column" => "id", "type" => "int", )), "failMsg" => "You must select a rank from the list."),			
		);
		
		$validator = new inputValidate($rules, $input);
		
		$errors = $validator->getErrors();
		
		if (empty($errors)) {
			$query = "SELECT * FROM players WHERE name = :playerName";
			$queryHandle = $dbh->prepare($query);
			$queryHandle->bindParam(":playerName", $input["playerName"]);
			$queryHandle->execute();
			
			$result = $queryHandle->fetchAll();
			
			if (!empty($result)) {
				$query = "UPDATE players SET faction = :faction, power = :power, ship = :ship, rank = :rank WHERE name = :playerName";
				$queryHandle = $dbh->prepare($query);
				$queryHandle->bindParam(":faction", $input["faction"], PDO::PARAM_INT);
				$queryHandle->bindParam(":power", $input["power"], PDO::PARAM_INT);
				$queryHandle->bindParam(":ship", $input["ship"], PDO::PARAM_INT);
				$queryHandle->bindParam(":rank", $input["rank"], PDO::PARAM_INT);
				$queryHandle->bindParam(":playerName", $input["playerName"]);
				
				try {
					$queryHandle->execute();
				} catch (PDOException $e) {
					array_push($errors, "Processing error: an unknown database exception has occured. Please contact the administrator.");
				}
			} else {
				$query = "INSERT INTO players (name, factionID, powerID, shipID, rankID) VALUES (:playerName, :faction, :power, :ship, :rank)";
				$queryHandle = $dbh->prepare($query);
				$queryHandle->bindParam(":playerName", $input["playerName"]); 
				$queryHandle->bindParam(":faction", $input["faction"], PDO::PARAM_INT);
				$queryHandle->bindParam(":power", $input["power"], PDO::PARAM_INT);
				$queryHandle->bindParam(":ship", $input["ship"], PDO::PARAM_INT);
				$queryHandle->bindParam(":rank", $input["rank"], PDO::PARAM_INT);
				
				try {
					$queryHandle->execute();
				} catch (PDOException $e) {
					array_push($errors, "Processing error: an unknown database exception has occured. Please contact the administrator.");
				}
			}
		}
		
		if (empty($errors)) {
			$success = array("Player record changed/created successfully.");
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/submit.css">
		
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/underscore-min.js"></script>
		<script src="js/loginShowHide.js"></script>
		<script src="js/submit.js"></script>
	</head>
	
	<body>
		<div id="head">
			<nav>
				<ul>
					<li><a href="index.php" title="Search">Search</a></li><!--
					--><li><a href="submit.php" title="Submit Names">Submissions</a></li><!--
					--><li>
						<form id="loginForm" action="index.php?action=login" method="POST">
							<input type="text" name="username" placeholder="Username">
							<input type="password" name="password" placeholder="Password">
							<input type="hidden" name="csrfCookie" value="">
							<button type="submit" name="submit" title="Login">Login</button>
						</form>
					</li><!-- 
					--><li><a href="register.php" title="Register">Register</a></li>
				</ul>
			</nav>
		</div>
		
		<div id="body">
			<?php
				if (isset($success) && !empty($success)) {
					$output = "<div id=\"success\" class=\"contentBackground\">";
					foreach ($success as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
				if (isset($errors) && !empty($errors)) {
					$output = "<div id=\"errors\" class=\"contentBackground\">";
					foreach ($errors as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
			
			?>
			
			<div id="submitFormWrapper" class="contentBackground">
				<form action="submit.php" method="post" id="submitForm">
					<input type="text" name="playerName" placeholder="CMDR Name"><br><br>
					<?php spawnFactionListDropMenu(); ?>
					<?php spawnPowerListDropMenu(); ?><br><br>
					<?php spawnShipListDropMenu(); ?>
					<?php spawnRankListDropMenu(); ?><br><br>
					
					<button type="submit">Submit Player</button>
				</form>
			</div>
		</div>
		
		<div id="foot">
		
		</div>
	</body>
</html>