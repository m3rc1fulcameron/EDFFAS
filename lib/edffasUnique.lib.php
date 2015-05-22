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
	
	function playerLookupBasic_secure($dbh, $playerName) {
		$query = "SELECT mainplayertable.username,mainplayertable.rank,factiontable.factionAbbreviation FROM mainplayertable LEFT JOIN factiontable ON mainplayertable.id = factiontable.factionID WHERE mainplayertable.username = :username";
	
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':username', $playerName);
		$queryHandle->execute();
	
		$result = $queryHandle->fetch();
		
		return json_encode($result);
	}
	
	function playerLookupAll_secure($dbh, $playerName) {
		$query = "SELECT mainplayertable.username,mainplayertable.rank,mainplayertable.ship,mainplayertable.lastKnownPosition,mainplayertable.notes,factiontable.factionAbbreviation,factiontable.factionName FROM mainplayertable LEFT JOIN factiontable ON mainplayertable.id = factiontable.factionID WHERE mainplayertable.username = :username";
	
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':username', $playerName);
		$queryHandle->execute();
	
		$result = $queryHandle->fetch();

		return json_encode($result);
	}
	
	function playerLookupWanted_secure($dbh, $playerName) {
		$query = "SELECT wantedtable.reason,factiontable.factionAbbreviation FROM wantedtable LEFT JOIN factiontable ON wantedtable.factionID = factiontable.factionID WHERE wantedtable.playerID = (SELECT mainplayertable.id FROM mainplayertable WHERE mainplayertable.username = :username)";
		
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':username', $playerName);
		$queryHandle->execute();
		
		$result = $queryHandle->fetchAll();
		
		/*$cleanResult = array();
		foreach ($result as $player) {
			array_push($cleanResult,$player['username']);
		}*/
		
		return json_encode($result);
	}
?>