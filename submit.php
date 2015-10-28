<?php
	define("CALLED", true);
	define("DEBUG", false);

	require_once "lib/mysql.lib.php";	
	require_once "lib/security.lib.php";
	require_once "lib/edffasUnique.lib.php";
	require_once "lib/m3Common.lib.php";
	
	if (!secureSessionStart()) {
		secureSessionStart();
	};
	
	if (!checkCSRFToken()) {
		$errors = array("Invalid CSRF token. Request not processed.");
	} else {
		$errors = array();
	}
	generateCSRFToken();
	
	$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
	$dbh = newDBHObject($mysqlConfig);
	
	if ((isset($_SESSION["canPost"]) && $_SESSION["canPost"]) || (isset($_SESSION["canEdit"]) && $_SESSION["canEdit"])) {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			require_once "lib/inputValidate.lib.php";
			
			$input = array();
			
			foreach($_POST as $key => $val) {
				$input[$key] = htmlspecialchars($val,ENT_QUOTES);
			}
			
			if (DEBUG) {
				print("<pre>");
				print_r($input);
				print("</pre>");
			}
			
			$input["playerName"] = (isset($input["playerName"]) ? removeCMDRFromUsername($input["playerName"]):NULL);
			
			//print($_POST["playerName"]);
			
			$rules = array(
				array("inputName" => "playerName",
					"method" => "username",
					"methodArgs" => array(
						"len" => array(
							"min" => 2,
							"max" => 32,
							"failMsg" => "The length of the commander's name must be between 2 and 32",
						),
					),
					"failMsg" => "The length of the commander's name must be between 2 and 32",
				),
				array(
					"inputName" => "faction",
					"method" => "existsInDatabaseTable",
					"methodArgs" => array(
						"database" => array(
							"handle" => $dbh,
							"table" => "factions",
							"column" => "id",
							"type" => "int",
							"failMsg" => "Invalid faction ID",
						),
					),
					"failMsg" => "You must select a faction from the list.",
				),
				array(
					"inputName" => "power",
					"method" => "existsInDatabaseTable",
					"methodArgs" => array(
						"database" => array(
							"handle" => $dbh,
							"table" => "powers",
							"column" => "id",
							"type" => "int",
							"failMsg" => "Invalid power ID",
						),
					), "failMsg" => "You must select a power from the list.",
				),
				array(
					"inputName" => "ship",
					"method" => "existsInDatabaseTable",
					"methodArgs" => array(
						"database" => array(
							"handle" => $dbh,
							"table" => "ships",
							"column" => "id",
							"type" => "int",
							"failMsg" => "Invalid ship ID.",
						),
					),
					"failMsg" => "You must select a ship from the list.",
				),
				array(
					"inputName" => "rank",
					"method" => "existsInDatabaseTable",
					"methodArgs" => array(
						"database" => array(
							"handle" => $dbh,
							"table" => "ranks",
							"column" => "id",
							"type" => "int",
							"failMsg" => "Invalid rank ID",
						),
					),
					
				),			
			);
			
			$validator = new inputValidate($rules, $input);
			
			foreach ($validator->getErrors() as $error) {
				array_push($errors,$error);
			}
			
			if (empty($errors)) {
				$query = "SELECT * FROM players WHERE name = :playerName";
				$queryHandle = $dbh->prepare($query);
				$queryHandle->bindParam(":playerName", $input["playerName"]);
				$queryHandle->execute();
				
				$result = $queryHandle->fetch(PDO::FETCH_ASSOC);
				
				if (!empty($result)) {
					if (isset($_SESSION["canEdit"]) && $_SESSION["canEdit"]) {
						$notes = "";
						if (isset($input["notes"]) && $input["notes"]) {
							if (isset($result["notes"])) {
								$notes = $result["notes"] . "<br>&quot;" . $input["notes"] . "&quot;";
							} else {
								$notes = "&quot;" . $input["notes"] . "&quot;";
							}
						}				
						
						$query = "UPDATE players SET factionID = :faction, powerID = :power, shipID = :ship, rankID = :rank, notes = :notes WHERE id = :id";
						$queryHandle = $dbh->prepare($query);
						$queryHandle->bindParam(":faction", $input["faction"], PDO::PARAM_INT);
						$queryHandle->bindParam(":power", $input["power"], PDO::PARAM_INT);
						$queryHandle->bindParam(":ship", $input["ship"], PDO::PARAM_INT);
						$queryHandle->bindParam(":rank", $input["rank"], PDO::PARAM_INT);
						$queryHandle->bindParam(":notes", $notes);
						$queryHandle->bindParam(":id", $result['id']);
						
						try {
							$queryHandle->execute();
						} catch (PDOException $e) {
							array_push($errors,"Processing error: an unknown database exception has occured. Please contact the administrator.");
						}
					} else {
						array_push($errors,"You do not have permission to update this player's profile.");
					}
				} else {
					if (isset($_SESSION["canPost"]) && $_SESSION["canPost"]) {
						$query = "INSERT INTO players (name, factionID, powerID, shipID, rankID, notes) VALUES (:playerName, :faction, :power, :ship, :rank, :notes)";
						$queryHandle = $dbh->prepare($query);
						$queryHandle->bindParam(":playerName", $input["playerName"]); 
						$queryHandle->bindParam(":faction", $input["faction"], PDO::PARAM_INT);
						$queryHandle->bindParam(":power", $input["power"], PDO::PARAM_INT);
						$queryHandle->bindParam(":ship", $input["ship"], PDO::PARAM_INT);
						$queryHandle->bindParam(":rank", $input["rank"], PDO::PARAM_INT);
						$queryHandle->bindParam(":notes", $input["notes"]);
						
						try {
							$queryHandle->execute();
						} catch (PDOException $e) {
							array_push($errors, "Processing error: an unknown database exception has occured. Please contact the administrator.");
						} 
					} else {
						array_push($errors,"You do not have permission to create this player profile.");
					}
				}
			}
			
			if (empty($errors)) {
				$success = array("Player record changed/created successfully.");
			}
		}
	} else {
		array_push($errors, "You do not have permission to submit or edit new players. Are you logged in?");
	}
	
	if (DEBUG) {
		print("<pre>");
		print_r($errors);
		print("</pre>");
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/submit.css">
		
		<script src="js/jquery-min.js"></script>
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/underscore-min.js"></script>
		<script src="js/loginShowHide.js"></script>
		<script src="js/submit.js"></script>
		
		<?php
			$defaults  = array(
				"submitForm" => array(
					"playerName" => "",
					"faction" => 0,
					"power" => 0,
					"ship" => 0,
					"rank" => 0,
				),
			);
			
			if (isset($_GET["p"])) {
				$defaults["submitForm"]["playerName"] = htmlspecialchars($_GET["p"], ENT_QUOTES);
			}
			
			if (isset($_GET["f"])) {
				$defaults["submitForm"]["faction"] = (int) htmlspecialchars($_GET["f"], ENT_QUOTES);
			}
			
			if (isset($_GET["pw"])) {
				$defaults["submitForm"]["power"] = (int) htmlspecialchars($_GET["pw"], ENT_QUOTES);
			}
			
			if (isset($_GET["s"])) {
				$defaults["submitForm"]["ship"] = (int) htmlspecialchars($_GET["s"], ENT_QUOTES);
			}
			
			if (isset($_GET["r"])) {
				$defaults["submitForm"]["rank"] = (int) htmlspecialchars($_GET["r"], ENT_QUOTES);
			}
		?>
	</head>
	
	<body>
		<div id="head">
			<nav>
				<ul>
					<?php require_once "templates/nav.template.php"; ?>
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
					<input type="text" name="playerName" id="playerName" placeholder="CMDR Name" value="<?php print($defaults["submitForm"]["playerName"]); ?>"><br><br>
					<?php spawnFactionListDropMenu($defaults["submitForm"]["faction"]); ?>
					<?php spawnPowerListDropMenu($defaults["submitForm"]["power"]); ?><br><br>
					<?php spawnShipListDropMenu($defaults["submitForm"]["ship"]); ?>
					<?php spawnRankListDropMenu($defaults["submitForm"]["rank"]); ?><br><br>
					<textarea name="notes" placeholder="Notes" style="resize:none;" cols="40" rows="10"></textarea><br><br>
					<input type="hidden" value = "<?php print($_SESSION["csrfToken"]); ?>" name="csrfToken">
					<button type="submit">Submit</button>
				<form>
			</div>
		</div>
	</body>
</html>