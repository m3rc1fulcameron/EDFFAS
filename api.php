<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST" || true) {
		define('CALLED', true);
		
		if (isset($_POST['playerName']) && $_POST['playerName'] !== '') {
			require_once 'lib/mysql.lib.php';
			require_once 'lib/edffasUnique.lib.php';
			
			$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
			
			$dbh = newDBHObject($mysqlConfig);
			
			print(wildCardPlayerLookupNameOnly_secure($dbh,$_POST['playerName']));
		}
	} else {
		die("Access denied.");
	}
?>