<?php
	if (!CALLED) {
		die("Access denied.");
	}
	
	function wildCardPlayerLookupNameOnly_secure($dbh, $playerName) {
		$playerName = "{$playerName}%";
		
		$query = "SELECT username FROM mainplayertable WHERE username LIKE :username LIMIT 10";
		
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':username', $playerName);
		$queryHandle->execute();
		
		$result = $queryHandle->fetchAll();
		
		$cleanResult = array();
		foreach ($result as $player) {
			array_push($cleanResult,$player['username']);
		}
		
		return json_encode($cleanResult);
	}
?>