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
	if (!checkCSRFToken()) {
		$errors = array("Invalid CSRF token. Request not processed.");
	} else {
		$errors = array();
	}
	generateCSRFToken();
	
	$mysqlConfig = parse_ini_file("cfg/mysqlConf.ini");
	$dbh = newDBHObject($mysqlConfig);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($errors)) {
		require_once "lib/inputValidate.lib.php";
		
		$rules = array(
			array(
				"inputName" => "un",
				"method" => "username",
				"methodArgs" => array(
					"len" => array(
						"min" => 4,
						"max" => 32,
						"failMsg" => "Your username must be between 4 and 32 characters.",
					),
					"database" => array(
						"handle" => $dbh,
						"table" => "users",
						"column" => "un",
						"failMsg" => "Username already taken.",
					"negate" => true,
					),
				),
				"failMsg" => "Your username must be between 4 and 32 characters.",
			),
			array(
				"inputName" => "em",
				"method" => "email",
				"methodArgs" => array(
					"failMsg" => "Invalid email address.",
				),
			),
			array(
				"inputName" => "pw",
				"method" => "password",
				"methodArgs" => array(
					"securityLevel" => 2,
					"len" => array(
						"failMsg" => "Password must be at least 8 characters in length.",
					),
					"regex" => array(
						"failMsg" => "Password must contain at least one letter and one number.",
					)
				),
			),
			array(
				"inputName" => "cpw",
				"method" => "matches",
				"methodArgs" => array(
					"strings" => array(
						"pw",
						"cpw",
					),
					"failMsg" => "Passwords do not match."
				),
			),
			array(
				"inputName" => "tos",
				"method" => "checked",
				"failMsg" => "You must agree to the terms of service.",
				"methodArgs" => array(
					"failMsg" => "You must agree to the terms of service.",
				)
			),
		);
		
		$input = array();
		foreach($_POST as $name => $field) {
			$input[$name] = $field;
		}
		
		print_r($input);
		
		$validator = new inputValidate($rules,$input);
		
		foreach($validator->getErrors() as $error) {
			array_push($errors,$error);
		}
		
		if (empty($errors)) {
			$config = parse_ini_file("cfg/securityConf.ini");
			$password = password_hash($input["pw"], PASSWORD_BCRYPT, array("cost" => $config["cost"], "salt" => $config["salt"]));
			
			if (isset($input["sem"])) {
				$sem = 1;
			} else {
				$sem = 0;
			}
			
			$query = "INSERT INTO users (un, pw, em, sem) VALUES (:un, :pw, :em, :sem)";
			$queryHandle = $dbh->prepare($query);
			$queryHandle->bindParam(":un", $input["un"]);
			$queryHandle->bindParam(":pw", $password);
			$queryHandle->bindParam(":em", $input["em"]);
			$queryHandle->bindParam(":sem", $sem);
			
			$queryHandle->execute();
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" type="text/css" href="css/global.css">
		<link rel="stylesheet" type="text/css" href="css/register.css">
		
		<script src="js/jquery-min.js"></script>
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="js/underscore-min.js"></script>
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
				
				if (isset($errors) && !empty($errors) && !empty($_REQUEST)) {
					$output = "<div id=\"errors\" class=\"contentBackground\">";
					foreach ($errors as $line) {
						$output .= $line . "<br>";
					}
					$output .= "</div>";
					
					print($output);
				}
				
			
			?>
			<div id="registerFormWrapper" class="contentBackground">
				<form id="registerForm" method="post" action="">
					<input name="un" type="text" placeholder="Username">
					<input name="em" type="email" placeholder="Email"><br><br>
					<input name="pw" type="password" placeholder="Password">
					<input name="cpw" type="password" placeholder="Confirm Password"><br><br>
					<input name="sem" type="checkbox" checked>I wish to recieve emails pertaining to both EDFFAS and my account.<br>
					<input name="tos" type="checkbox">I agree to the <a href="tos.php" title="Terms of Service">Terms of Service</a> for EDFFAS.<br><br>
					<input name="csrfToken" type="hidden" value="<?php print($_SESSION["csrfToken"]); ?>">
					<button type="sbumit">Register</button>
				</form>
			</div>
		</div>
		
		<div id="foot">
		
		</div>
	</body>
</html>