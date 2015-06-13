<?php
	if (!CALLED) {
		die("Access denied.");
	}
	
	function validateUsername($username,$maxLength = 16) {
		if ($username != "") {
			if (sizeof($usernme) <= $maxlength) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
?>