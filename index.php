<?php
	define("CALLED", true);

	require_once "lib/mysql.lib.php";
	require_once "lib/secureSessions.lib.php";
	require_once "lib/edffasUnique.lib.php";
	require_once "lib/m3Common.lib.php";
	
	/*$mySQLConfig = parse_ini_file("cfg/mysqlConf.ini");
	initializeMySQLConnection($mySQLConfig);*/
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/search.css">
		
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/loginShowHide.js"></script>
		<script src="js/autoSuggest.js"></script>
	</head>
	
	<body>
		<div id="head">
			<nav>
				<ul>
					<li><a href="index.php" title="Search">Search</a></li><!--
					--><li><a href="submit.php" title="Submit Names">Submissions</a></li><!--
					--><li>
						<form>
							<input type="text" name="username" placeholder="Username">
							<input type="password" name="password" placeholder="Password">
							<input type="hidden" name="csrfCookie" value="">
							<button type="submit" name="submit" title="Login">Login</button>
						</form>
					</li>
				</ul>
			</nav>
		</div>
		
		<div id="body">
			<input type="search" name="playerSeach" id="playerSearch" placeholder="Search for player by name">
			
			<div id="searchResults">
			</div>
		</div>
		
		<div id="foot">
		
		</div>
	</body>
</html>