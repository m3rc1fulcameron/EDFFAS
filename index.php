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
	
	$success = array();
	
	if (!checkCSRFToken()) {
		$errors = array("Invalid CSRF token. Request not processed.");
	} else {
		$errors = array();
	}
	
	if (empty($errors)) {
		if (isset($_GET["action"])) {
			switch ($_GET["action"]) {
				case "login":
					if (DEBUG) {
						print("Attempting to log user in.<br>");
					}
					
					$dbh = newDBHObject(parse_ini_file("cfg/mysqlConf.ini"));
					
					if (isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"])) {
						$config = parse_ini_file("cfg/securityConf.ini");
						$password = password_hash($_POST["password"], PASSWORD_BCRYPT, array("cost" => $config["cost"], "salt" => $config["salt"]));
						
						if (DEBUG) {
							print("Hash is " . $password . "<br>");
						}
						
						$query = "SELECT * FROM users LEFT JOIN usergroups ON users.gid = usergroups.id LEFT JOIN players ON users.pid = players.id WHERE users.un = :un AND users.pw = :pw";
						$queryHandle = $dbh->prepare($query);
						$queryHandle->bindParam(":un", $_POST["username"]);
						$queryHandle->bindParam(":pw", $password);
						$queryHandle->execute();
						
						$result = $queryHandle->fetch(PDO::FETCH_ASSOC);
						
						if (empty($result)) {
							array_push($errors, "Invalid username or password. (1)");
						} else {
							array_push($success,"You have successfully been logged in.");
							
							$result["pw"] = null;
							
							$result = array_filter($result,function($a) { return ($a !== null); });
							
							foreach($result as $field=>$value) {
								$_SESSION[$field] = $value;
							}
							
							if (DEBUG) {
								print("<pre>");
								print_r($_SESSION);
								print("</pre>");
							}
						}
					} else {
						array_push($errors, "Invalid username or password. (0)");
						break;
					}
					break;
				case "logout":
					if (DEBUG) {
						print("Destroying session.<br>");
					}
					
					secureSessionDestroy();
					secureSessionRegenerate();
					break;
				default:
					
			}
		}
	}
	
	generateCSRFToken();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/search.css">
		
		<?php spawnRankListJS(); ?>
		<script src="js/jquery-min.js"></script>
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/underscore-min.js"></script>
		<script src="js/loginShowHide.js"></script>
		<script src="js/pcards0.0.2.js"></script>
		<script src="js/autoSuggest.js"></script>
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
				
				if (isset($errors) && !empty($errors) && !empty($_POST)) {
					$output = "<div id=\"errors\" class=\"contentBackground\">";
					foreach ($errors as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
			
			?>
			<input type="search" name="playerSeach" id="playerSearch" placeholder="Search for player by name">
			
			<div id="suggestions">
			</div>
			
			<div id="cardDisplay">
			</div>
		</div>
		
		<div id="foot">
		
		</div>
	</body>
</html>