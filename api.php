<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST" || true) {
		if (isset($_GET['apiTarget'])) {
			if (isset($_POST['playerName']) && $_POST['playerName'] !== '') {
				define('CALLED', true);
				
				require_once 'lib/mysql.lib.php';
				require_once 'lib/edffasUnique.lib.php';
				
				$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");	
				$dbh = newDBHObject($mysqlConfig);
				
				switch ($_GET['apiTarget']) {
					case 'nameOnly':			
						print(wildCardPlayerLookupNameOnly_secure($dbh,$_POST['playerName']));	
						break;
							
					case 'basic':
						print(playerLookupBasic_secure($dbh,$_POST['playerName']));
						break;
					
					case 'all':
						print(playerLookupAll_secure($dbh,$_POST['playerName']));
						break;
							
					case 'default':
						die('[{"error" : "Invalid API Target"}]');
				}
			} else {
				die('[{"error" : "Missing POST parameter \'playerName\'"}]');
			}
		} else {
			die('[{"error" : "Missing GET parameter \'apiTarget\'"}]');
		}
	} else {
		die('[{"error" : "Invalid Request Type"}]');
	}
?>