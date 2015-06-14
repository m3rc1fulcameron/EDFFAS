<?php
	if (isset($_REQUEST['apiTarget'])) {
		if (isset($_REQUEST['name']) && $_REQUEST['name'] !== '') {
			define('CALLED', true);
			
			require_once 'lib/mysql.lib.php';
			
			$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
			$dbh = newDBHObject($mysqlConfig);
			
			require_once 'lib/edffasUnique.lib.php';
			require_once 'lib/api.lib.php';
			
			switch ($_REQUEST['apiTarget']) {
				case 'wildcardByName':			
					print(json_encode(wildcardPlayerLookupByName($_REQUEST['name'])));
					break;
				case 'comprehensive':
					print(json_encode(playerComprehensiveLookupByName($_REQUEST['name'])));
					break;
				case 'default':
					die('{"error" : "apiInvalidTarget"}');
			}
		} else {
			die('{"error" : "missingParameter"}');
		}
	} else {
		die('{"error" : "apiInvalidTarget"}');
	}
?>