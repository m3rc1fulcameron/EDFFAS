<?php 
	function wildcardPlayerLookupByName($partialName, $rows = 10) {
		global $dbh;
		
		$partialName = "$partialName%";
		
		$query = "SELECT name FROM players WHERE name LIKE :partialName ORDER BY id ASC LIMIT :rows";
		
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(':partialName', $partialName);
		$queryHandle->bindParam(':rows', $rows, PDO::PARAM_INT);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll();
		
		if (sizeof($results) == 0) {
			$results = array("error" => "missingPlayer");
		}
		
		return $results;
	}
	
	function powerLookupByID($id) {
		global $dbh;
		
		//Get power name and text colors.
		$query = "SELECT powers.name,superpowers.backgroundColor,superpowers.textColor FROM powers LEFT JOIN superpowers ON powers.allegianceID = superpowers.id WHERE powers.id = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function factionLookupByID($id) {
		global $dbh;
		
		//Get faction name, abbreviated faction name, and text colors.
		$query = "SELECT factions.name,factions.abbreviatedName,superpowers.backgroundColor,superpowers.textColor FROM factions LEFT JOIN superpowers ON factions.allegianceID = superpowers.id WHERE factions.id = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerWantedAdvisoryLookupByID($id) {
		global $dbh;
		
		//Get faction name, faction abbreviated name, and reason.
		$query = "SELECT factions.name AS creator,factions.abbreviatedName AS creatorAbbreviatedName,wantedadvisories.reason FROM wantedadvisories LEFT JOIN factions ON wantedadvisories.factionID = factions.id WHERE wantedadvisories.targetPlayerID = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerBountyLookupByID($id) {
		global $dbh;
		
		//Get creator name, amount, and reason.
		$query = "SELECT players.name AS creator,bounties.amount,bounties.reason FROM bounties LEFT JOIN players ON bounties.postPlayerID = players.id WHERE bounties.targetPlayerID = :id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":id",$id);
		$queryHandle->execute();
		
		$results = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerBasicLookupByName($name) {
		global $dbh;
		
		//Get name, ID, factionID, powerID, notes, rank name, and ship name.
		$query = "SELECT players.name,players.id,players.factionID,players.powerID,players.notes,players.updated,ranks.name AS rank,ranks.id AS rankID,ships.name AS ship,ships.id AS shipID FROM players LEFT JOIN ranks ON players.rankID = ranks.id LEFT JOIN ships ON players.shipID = ships.id WHERE players.name = :name";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->bindParam(":name",$name);
		$queryHandle->execute();
		
		$results = $queryHandle->fetch(PDO::FETCH_ASSOC);
		
		return $results;
	}
	
	function playerSpotsLookupByID($id,$rows) {
		
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