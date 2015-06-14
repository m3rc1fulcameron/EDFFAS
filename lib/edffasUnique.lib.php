<?php
	if (!CALLED) {
		die("Access denied.");
	}
	
	$rankList = array('Unknown','Harmless','Mostly Harmless','Novice','Competent','Expert','Master','Dangerous','Deadly','Elite');
	
	function spawnRankListJS() {
		global $rankList;
		
		$ranks = "";
		foreach ($rankList as $key => $rank) {
			$ranks .= "'" . $rank . "'";
			
			if ($key < sizeof($rankList) - 1) {
				$ranks .= ",";
			}
		}
		
		print("<script>var rankTable = new Array(" . $ranks . ");</script>");
	}
	
	function spawnRankListDropMenu() {
		global $rankList;
		
		$output = "<select name=\"rank\"><option value=\"null\" disabled selected>Combat Rank</option>";
		
		foreach ($rankList as $key => $rank) {
			$output .= "<option value=\"" . $key . "\">" . $rank . "</option>";
		}
		
		$output .= "</select>";
		
		print($output);
	}
	
	function spawnFactionListDropMenu() {
		global $dbh;
		
		$query = "SELECT factions.id,factions.name,factions.abbreviatedName,superpowers.backgroundColor,superpowers.textColor FROM factions LEFT JOIN superpowers ON factions.allegianceID = superpowers.id ORDER BY factions.allegianceID";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
		
		$result = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		$output = "<select name=\"faction\"><option value=\"null\" disabled selected>Player Faction</option><option value=\"-1\">None/Unknown</option>";
	
		foreach ($result as $faction) {
			if (!$faction['backgroundColor']) { $faction['backgroundColor'] = "#FFFFFF"; }
			if (!$faction['textColor']) { $faction['textColor'] = "#000000"; }
			
			$output .= "<option value = \"" . $faction['id'] . "\" style=\"background-color:" . $faction['backgroundColor'] . ";color:" . $faction['textColor'] .";\">" . $faction['name'] . " [" . $faction['abbreviatedName'] . "]</option>";
		}
		
		$output .= "</select>";
		
		print($output);
	}
	
	function spawnShipListDropMenu() {
		global $dbh;
	
		$query = "SELECT * FROM ships";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
	
		$result = $queryHandle->fetchAll();
	
		$output = "<select name=\"ship\"><option value=\"null\" disabled selected>Player Ship</option><option value=\"1\">Unknown</option>";
	
		foreach ($result as $ship) {
			$output .= "<option value = \"" . $ship['id'] . "\">" . $ship['name'] . "</option>";
		}
	
		$output .= "</select>";
	
		print($output);
	}
	
	function spawnPowerListDropMenu() {
		global $dbh;
	
		$query = "SELECT powers.id,powers.name,superpowers.color FROM powers LEFT JOIN superpowers ON powers.allegianceID = superpowers.id ORDER BY powers.allegianceID";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
	
		$result = $queryHandle->fetchAll();
	
		$output = "<select name=\"power\"><option value=\"null\" disabled selected>Player Power</option>";
	
		foreach ($result as $power) {
			if (!$power['color']) { $power['color'] = "#FFFFFF"; }
			
			$output .= "<option value =\"" . $power['id'] . "\" style=\"background-color:" . $power['color'] . ";\">" . $power['name'] . "</option>";
		}
	
		$output .= "</select>";
	
		print($output);
	}
	
	function removeCMDRFromUsername($username) {
		$output = preg_replace("/^cmdr\s/i","",$username);
		
		return $output;
	}
?>