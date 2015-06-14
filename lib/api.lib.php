<?php 
	function wildcardPlayerLookupByName($partialName) {
		global $dbh;
		
		$partialName = "{$partialName}%";
		
		$query = "SELECT name FROM players WHERE name LIKE :partialName LIMIT 10";
		
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':partialName', $partialName);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		if (sizeof($results) == 0) {
			$results = array("error" => "missingPlayer");
		}
		
		return $results;
	}
	
	function powerLookupByID($id) {
		global $dbh;
		
		$query = "SELECT powers.name,superpowers.backgroundColor,superpowers.textColor FROM powers LEFT JOIN superpowers ON powers.allegianceID = superpowers.id WHERE powers.id = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function factionLookupByID($id) {
		global $dbh;
		
		$query = "SELECT factions.name,factions.abbreviatedName,superpowers.backgroundColor,superpowers.textColor FROM factions LEFT JOIN superpowers ON factions.allegianceID = superpowers.id WHERE factions.id = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerWantedAdvisoryLookupByID($id) {
		global $dbh;
		
		$query = "SELECT factions.name AS creator,factions.abbreviatedName AS creatorAbbreviatedName,wantedadvisories.reason FROM wantedadvisories LEFT JOIN factions ON wantedadvisories.factionID = factions.id WHERE wantedadvisories.targetPlayerID = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerBountyLookupByID($id) {
		global $dbh;
		
		$query = "SELECT players.name AS creator,bounties.amount,bounties.reason FROM bounties LEFT JOIN players ON bounties.postPlayerID = players.id WHERE bounties.targetPlayerID = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerBasicLookupByName($name) {
		global $dbh;
		
		$query = "SELECT players.name,players.id,players.factionID,players.powerID,players.notes,ranks.name AS rank,ships.name AS ship FROM players LEFT JOIN ranks ON players.rankID = ranks.id LEFT JOIN ships ON players.shipID = ships.id WHERE players.name = :name";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":name",$name);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerComprehensiveLookupByName($name) {
		global $dbh;
		
		//Get basic info.
		$info = playerBasicLookupByName($name);
		
		//Check if player was found.
		if (empty($info)) {
			return array('error' => 'missingPlayer');
		}
		
		//Get info on faction if any.
		if (isset($info['factionID']) && $info['factionID'] != '') {
			$info['faction'] = factionLookupByID($info['factionID']);
		}
		
		//Get info on power if any.
		if (isset($info['powerID']) && $info['powerID'] != '') {
			$info['power'] = powerLookupByID($info['powerID']);
		}
		
		//Get wanted advisories if any.
		$wantedAdvisories = playerWantedAdvisoryLookupByID($info['id']);
		if (!empty($wantedAdvisories)) {
			$info['wantedAdvisories'] = $wantedAdvisories;
		}
		
		//Get bounties if any.
		$bounties = playerBountyLookupByID($info['id']);
		if (!empty($bounties)) {
			$info['bounties'] = $bounties;
		}
		
		return $info;
	}
?>