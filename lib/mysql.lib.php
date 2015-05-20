<?php
	if (!CALLED) {
		die("Access denied.");
	}
	
	function newDBHObject($mysqlConfig) {
		try {
			$dbh = new PDO("mysql:host={$mysqlConfig['host']};dbname={$mysqlConfig['dbname']}",$mysqlConfig['username'],$mysqlConfig['password']);
			
			return $dbh;
		} catch (PDOException $e) {
			return false;
		}
	}
?>