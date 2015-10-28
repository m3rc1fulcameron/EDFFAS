<?php
	define("CALLED", true);
	define("DEBUG", false);

	require_once "lib/mysql.lib.php";
	require_once "lib/security.lib.php";
	require_once "lib/edffasUnique.lib.php";
	require_once "lib/m3Common.lib.php";
	
	if (!secureSessionStart()) {
		secureSessionStart();
	};
	
	$success = array();
	
	if (!checkCSRFToken() && isset($_REQUEST) && !empty($_REQUEST)) {
		$errors = array("Invalid CSRF token. Request not processed.");
	} else {
		$errors = array();
	}
	
	generateCSRFToken();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/control.css">
		
		<script src="js/jquery-min.js"></script>
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/loginShowHide.js"></script>
	</head>
	
	<body>
		<div id="head">
			<nav>
				<ul>
					<?php require_once "templates/nav.template.php"; ?>
				</ul>
			</nav>
		</div>
		
		<div id="body">
			<?php
				if (isset($success) && !empty($success)) {
					$output = "<div id=\"success\" class=\"contentBackground\">";
					foreach ($success as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
				if (isset($errors) && !empty($errors) && !empty($_POST)) {
					$output = "<div id=\"errors\" class=\"contentBackground\">";
					foreach ($errors as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
			
			?>
		</div>
		
		<div id="foot">
		
		</div>
	</body>
</html>