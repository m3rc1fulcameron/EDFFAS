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
	
	function spawnRankListDropMenu($default = 0) {
		global $dbh;
		
		$query = "SELECT * FROM ranks ORDER BY id";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
		
		$result = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		$output = "<select name=\"rank\">\n<option value=\"null\" disabled" . ($default == 0 ? " selected":"") . ">Combat Rank</option>\n";
		
		foreach ($result as $rank) {
			$output .= "<option value=\"" . $rank['id'] . "\"" . ($default == $rank['id'] ? " selected":"") . ">" . $rank['name'] . "</option>\n";
		}
		
		$output .= "</select>\n";
		
		print($output);
	}
	
	function spawnFactionListDropMenu($default = 0) {
		global $dbh;
		
		$query = "SELECT factions.id,factions.name,factions.abbreviatedName,superpowers.backgroundColor,superpowers.textColor FROM factions LEFT JOIN superpowers ON factions.allegianceID = superpowers.id ORDER BY factions.allegianceID";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
		
		$result = $queryHandle->fetchAll(PDO::FETCH_ASSOC);
		
		$output = "<select name=\"faction\">\n<option value=\"null\" disabled" . ($default == 0 ? " selected":"") . ">Player Faction</option>\n";
	
		foreach ($result as $faction) {
			if (!$faction['backgroundColor']) { $faction['backgroundColor'] = "#FFFFFF"; }
			if (!$faction['textColor']) { $faction['textColor'] = "#000000"; }
			if (isset($faction['abbreviatedName']) && $faction['abbreviatedName'] != '') {
				$faction['abbreviatedName'] = " [" . $faction['abbreviatedName'] . "]";
			} else {
				$faction['abbreviatedName'] = '';
			}
			
			$output .= "<option value=\"" . $faction['id'] . "\" style=\"background-color:" . $faction['backgroundColor'] . ";color:" . $faction['textColor'] .";\"" . ($default == $faction['id'] ? " selected":"") . ">" . $faction['name'] . $faction['abbreviatedName'] . "</option>\n";
		}
		
		$output .= "</select>\n";
		
		print($output);
	}
	
	function spawnShipListDropMenu($default = 0) {
		global $dbh;
	
		$query = "SELECT * FROM ships ORDER BY name";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
	
		$result = $queryHandle->fetchAll();
	
		$output = "<select name=\"ship\">\n<option value=\"null\" disabled" . ($default == 0 ? " selected":"") . ">Player Ship</option>\n";
	
		foreach ($result as $ship) {
			$output .= "<option value=\"" . $ship['id'] . "\"" . ($default == $ship['id'] ? " selected":"") . ">" . $ship['name'] . "</option>\n";
		}
	
		$output .= "</select>\n";
	
		print($output);
	}
	
	function spawnPowerListDropMenu($default = 0) {
		global $dbh;
	
		$query = "SELECT powers.id,powers.name,superpowers.backgroundColor,superpowers.textColor FROM powers LEFT JOIN superpowers ON powers.allegianceID = superpowers.id ORDER BY powers.allegianceID";
		$queryHandle = $dbh->prepare($query);
		$queryHandle->execute();
	
		$result = $queryHandle->fetchAll();
	
		$output = "<select name=\"power\">\n<option value=\"null\" disabled" . ($default == 0 ? " selected":"") . ">Player Power</option>\n";
	
		foreach ($result as $power) {
			if (!$power['backgroundColor']) { $power['backgroundColor'] = "#FFFFFF"; }
			if (!$power['textColor']) { $power['textColor'] = "#000000"; }
			
			$output .= "<option value=\"" . $power['id'] . "\" style=\"background-color:" . $power['backgroundColor'] . ";color:" . $power['textColor'] .";\"" . ($default == $power['id'] ? " selected":"") . ">" . $power['name'] . "</option>\n";
		}
	
		$output .= "</select>\n";
	
		print($output);
	}
	
	function removeCMDRFromUsername($username) {
		$output = preg_replace("/^cmdr\s/i","",$username);
		
		return $output;
	}
?>