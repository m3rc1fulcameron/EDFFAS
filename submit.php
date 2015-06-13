<?php
	define("CALLED", true);

	require_once "lib/mysql.lib.php";	
	require_once "lib/secureSessions.lib.php";
	require_once "lib/edffasUnique.lib.php";
	require_once "lib/m3Common.lib.php";
	
	$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
	$dbh = newDBHObject($mysqlConfig);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$errors = array();
		
		if (!isset($_POST["playerName"]) || !validateUsername($_POST["playerName"])) { $errors["playerName"] = true; }
		if (!isset($_POST["faction"])) { $errors["faction"] = true; }
		if (!isset($_POST["power"])) { $errors["power"] = true; }
		if (!isset($_POST["ship"])) { $errors["ship"] = true; }
		if (!isset($_POST["rank"])) { $errors["rank"] = true; }
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