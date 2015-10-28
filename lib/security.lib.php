<?php
	if (!CALLED) {
		die("Access denied.");
	}
	
	$securityConfig = parse_ini_file("cfg/securityConf.ini");
	
	function secureSessionDestroy() { //Shamelessly borrowed (stolen) from NikiC on StackExchange
		if (DEBUG) {
			print("Destroying Session<br>");
		}
		
		session_destroy();
		$cookieParams = session_get_cookie_params();
		setcookie(session_name(), '', 0, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
		$_SESSION = array();
	}
	
	function secureSessionRegenerate() {
		session_regenerate_id();
		
		if (DEBUG) {
			print("Generating session fingerprint<br>");
		}
		$_SESSION["fingerprint"] = md5($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
		
		if (DEBUG) {
			print("Generating session timeout<br>");
		}
		$_SESSION["activityTimeout"] = time();
	}
	
	function secureSessionStart() {
		global $securityConfig;
		
		if (DEBUG) {
			print("Starting Session<br>");
		}
		
		session_start();
		
		if (isset($_SESSION["fingerprint"])) {
			if(md5($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]) != $_SESSION["fingerprint"]) {
				if (DEBUG) {
					print("Session fingerprint does not match<br>");
				}
				secureSessionDestroy();
			}
		}
		
		if (isset($_SESSION["activityTimestamp"])) {
			if ($_SESSION["activityTimestamp"] + $securityConfig["timeout"] > time()) {
				$_SESSION["activityTimeout"] = time();
			} else {
				if (DEBUG) {
					print("Session has timed out<br>");
				}
				secureSessionDestroy();
			}
		}
		
		secureSessionRegenerate();
		
		return true;
	}
	
	function checkCSRFToken() {
		if (isset($_SESSION["csrfToken"]) && isset($_REQUEST["csrfToken"])) {
			if ($_SESSION["csrfToken"] == $_REQUEST["csrfToken"]) {
				if (DEBUG) {
					print("CSRF token valid<br>");
				}
				return true;
			} else {
				if (DEBUG) {
					print("CSRF token mismatch<br>");
				}
				return false;
			}
		} else {
			if (DEBUG) {
					print("No CSRF token<br>");
				}
			return true;
		}
	}
	
	
	
	function generateCSRFToken() {
		$token = md5(uniqid($_SESSION["fingerprint"],true));
		
		if (DEBUG) {
			print("New CSRF token: " . $token . "<br>");
		}
		$_SESSION["csrfToken"] = $token;
	}
?>