<?php
	class inputValidate {
		private $rules;
		private $input;
		private $errors = array();
		
		private function stringLen($rule) {
			if (!isset($rule["inputName"]) || !isset($this->input[$rule["inputName"]])) {
				user_error("Invalid rule structure: inputName undefined or no matching entry in table.",E_USER_WARNING);
				return false;
			}
			
			$string = $this->input[$rule["inputName"]];
			
			if (isset($rule["methodArgs"]["len"]["min"]) && $rule["methodArgs"]["len"]["min"] !== -1) {
				if (strlen($string) <= $rule["methodArgs"]["len"]["min"]) {
					array_push($this->errors, (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: supplied string is too short."));
				}
			}
			
			if (isset($rule["methodArgs"]["len"]["max"]) && $rule["methodArgs"]["len"]["max"] !== -1) {
				if (strlen($string) >= $rule["methodArgs"]["len"]["max"]) {
					array_push($this->errors, (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: supplied string is too long."),E_USER_NOTICE);
				}
			}
		}
		
		private function matches($rule) {
			if (!isset($rule["methodArgs"]["strings"]) || count($rule["methodArgs"]["strings"]) <2) {
				user_error("Invalid rule structure: must supply at least two strings to compare.",E_USER_WARNING);
				return false;
			}
			
			$strings = $rule["methodArgs"]["strings"];
			
			foreach($strings as $string) {
				if (!isset($this->input[$string])) {
					user_error("Invalid rule structure: input '" . $string . "' does not exist in input table.",E_USER_WARNING);
					return false;
				}
			}
			
			if (isset($rule["methodArgs"]["ci"]) && $rule["methodArgs"]["ci"]) {
				$base = strtolower($this->input[$strings[0]]);
				
				for ($i=1;$i<count($strings);$i++) {
					if (strtolower($this->input[$strings[$i]]) != $base) {
						array_push($this->errors, "Validation error: string '" . $strings[$i] . "' (" . $this->input[$strings[$i]] . ") does not match string '" . $strings[0] . "' (" . $base . ").");
					}
				}
			} else {
				$base = $this->input[$strings[0]];
				
				for ($i=1;$i<count($strings);$i++) {
					if ($this->input[$strings[$i]] != $base) {
						array_push($this->errors, "Validation error: string '" . $strings[$i] . "' (" . $this->input[$strings[$i]] . ") does not match string '" . $strings[0] . "' (" . $base . ").");
					}
				}
			}
		}
		
		private function existsInDatabaseTable($rule) {
			if (!$rule["methodArgs"]["database"]["handle"]) {
				user_error("Database connection error: validator class has no PDO database handle. Please contact the administrator.",E_USER_WARNING);
				return false;
			}
			
			if (!isset($rule["inputName"]) || !isset($this->input[$rule["inputName"]])) {
				user_error("Invalid rule structure: inputName undefined or no matching entry in table.",E_USER_WARNING);
				return false;
			}
			
			if (!isset($rule["methodArgs"]["database"]["table"]) || !isset($rule["methodArgs"]["database"]["column"])) {
				user_error("Invalid rule structure: must provide an argument defining table, column, and search.",E_USER_WARNING);
				return false;
			}
			
			$dbh = $rule["methodArgs"]["database"]["handle"];
			
			$query = "SELECT * FROM :table WHERE :column = :search";
			
			$queryHandle = $dbh->prepare($query);
			$queryHandle->bindParam(":table",$rule["methodArgs"]["table"]);
			$queryHandle->bindParam(":column",$rule["methodArgs"]["column"]);
			$queryHandle->bindParam(":search",$this->input[$rule["inputName"]]);
			$queryHandle->execute();
			
			$result = $queryHandle->fetchAll(PDO::FETCH_NUM);
			
			if (count($result) == 0) {
				array_push($this->errors, "Validation error: no entry in table '" . $rule["methodArgs"]["table"] . "' with value '" . $rule["methodArgs"]["search"] . "' in column '" . $rule["methodArgs"]["column"] . "' found in database.");
			}
		}
		
		private function username($rule) {
			if (!isset($rule["inputName"]) || !isset($this->input[$rule["inputName"]])) {
				user_error("Invalid rule structure: inputName undefined or no matching entry in table.",E_USER_WARNING);
				return false;
			}
			
			if (isset($rule["methodArgs"]["database"])) { $this->existsInDatabaseTable($rule); };
			if (isset($rule["methodArgs"]["len"])) { $this->stringLen($rule); };
		}
		
		private function email($rule) {
			if (!isset($rule["inputName"]) || !isset($this->input[$rule["inputName"]])) {
				user_error("Invalid rule structure: inputName undefined or no matching entry in table.",E_USER_WARNING);
				return false;
			}
			
			$email = $this->input[$rule["inputName"]];
			
			if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
				array_push($this->errors, (isset($this->input[$rule["inputName"]]["methodArgs"]["failMsg"]) ? $this->input[$rule["inputName"]]["methodArgs"]["failMsg"]:"Validation error: invalid email address"));
			}
		}
		
		private function password($rule) {
			if (!isset($rule["inputName"]) || !isset($this->input[$rule["inputName"]])) {
				user_error("Invalid rule structure: inputName undefined or no matching entry in table.",E_USER_WARNING);
				return false;
			}
			
			$password = $this->input[$rule["inputName"]];
			
			if (isset($rule["methodArgs"]["securityLevel"])) {
				switch ($rule["methodArgs"]["securityLevel"]) {
					//Note: All cases except default leave the maximum length at 32 characters.
					//Note: Security level completely ignores other arguments.
					case 1: //Length greater than 6
						$this->stringLen(array(
							"inputName" => $rule["inputName"],
							"methodArgs" => array(
								"len" => array(
									"min" => 6,
									"failMsg" => (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: password must be longer than 6 characters."),
								),
							),
						));
						break;
					case 2: //Length greater than 8 w/ letter & number
						$this->stringLen(array(
							"inputName" => $rule["inputName"],
							"methodArgs" => array(
								"len" => array(
									"min" => 8,
									"failMsg" => (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: password must be longer than 8 characters."),
								),
							),
						));
						
						if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d).*$/i",$password)) {
							array_push($this->errors, (isset($rule["methodArgs"]["regex"]["failMsg"]) ? $rule["methodArgs"]["regex"]["failMsg"]:"Validation error: password must contain at least one of each of the following: a letter and a number."));
						}
						
						break;
					case 3: //Length greater than 8 w/ UC and LC letter + number
						$this->stringLen(array(
							"inputName" => $rule["inputName"],
							"methodArgs" => array(
								"len" => array(
									"min" => 8,
									"failMsg" => (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: password must be longer than 8 characters."),
								),
							),
						));
						
						if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/i",$password)) {
							array_push($this->errors, (isset($rule["methodArgs"]["regex"]["failMsg"]) ? $rule["methodArgs"]["regex"]["failMsg"]:"Validation error: password must contain at least one of each of the following: an upper-case letter, a lower-case letter, and a number."));
						}
						
						break;
					case 4: //Length greater than 8 w/ UC + LC letter + number + symbol
						$this->stringLen(array(
							"inputName" => $rule["inputName"],
							"methodArgs" => array(
								"len" => array(
									"min" => 8,
									"failMsg" => (isset($rule["methodArgs"]["len"]["failMsg"]) ? $rule["methodArgs"]["len"]["failMsg"]:"Validation error: password must be longer than 8 characters."),
								),
							),
						));
						
						if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$-\/:-?{-~!\"^_`\[\]]).*$/i",$password)) {
							array_push($this->errors, (isset($rule["methodArgs"]["regex"]["failMsg"]) ? $rule["methodArgs"]["regex"]["failMsg"]:"Validation error: password must contain at least one of each of the following: an upper-case letter, a lower-case letter, a number, and a symbol."));
						}
						
						break;
					default:
						if (isset($rule["methodArgs"]["len"]) && (isset($rule["methodArgs"]["len"]["min"]) || isset($rule["methodArgs"]["len"]["max"]))) {
							$this->stringLen($rule);
						}
						
						if (isset($rule["methodArgs"]["regex"]["pattern"])) {
							if (!preg_match($rule["methodArgs"]["regex"]["pattern"],$password)) {
								array_push($this->errors, (isset($rule["methodArgs"]["regex"]["failMsg"]) ? $rule["methodArgs"]["regex"]["failMsg"]:"Validation error: string does not match the supplied regular expression."));
							}
						}
				}
			}
		}
		
		private function singleValidate(array $rule) {
			if (isset($rule["method"])) { //Check to see if method is defined within the rule.
				switch ($rule["method"]) {
					case "password":
						$this->password($rule);							
						break;
					case "username":
						$this->username($rule);
						break;
					case "email":
						$this->email($rule);
						break;
					case "matches":
						$this->matches($rule);
						break;
					default: //Errors if method is invalid.
						user_error("Invalid rule structure: Invalid method.");
				}
			} else {
				user_error("Invalid rule structure: No method defined.",E_USER_NOTICE);
			}
		}
		
		private function validate() {
			$rules = $this->rules;
			foreach ($rules as $rule) {
				try {
					$this->singleValidate($rule);
				} catch (Exception $e) {
					array_push($this->errors,$e);
				}
			}
		}
		
		public function getErrors() {
			return $this->errors;
		}
		
		public function __construct(array $rules,array $input) {
			$this->rules = $rules;
			$this->input = $input;
			$this->validate();
		}
	}
?>

<?php 
/*
 * *************************NOTICE*****************************
 * The following is debug code. Remove in final version.
 */

/*
 * For demo purposes only
$validator = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$input = array();
	$rules = array(
		array("inputName" => "un", "method" => "username", "methodArgs" => array("len" => array("min" => 2))),
		array("inputName" => "em", "method" => "email"),
		array("inputName" => "pw", "method" => "password", "methodArgs" => array("securityLevel" => 3)),
		array("method" => "matches", "methodArgs" => array("strings" => array("pw", "cpw"))),
	);
	
	foreach($_POST as $key => $val) {
		$input[$key] = htmlspecialchars($val,ENT_QUOTES);
	}
	
	$validator = new inputValidate($rules, $input);
}
?>

<!DOCTYPE html>
<html>
	<body>
		<?php 
			if ($validator) {
				print("<pre>");
				print_r($validator->getErrors());
				print("</pre>");
			}
		?>
		<form action="" method="post">
			<input type="text" name="un" placeholder="username">
			<input type="email" name="em" placeholder="email">
			<input type="password" name="pw" placeholder="password">
			<input type="password" name="cpw" placeholder="confirm password">
			
			<button type="submit">Submit!</button>
		</form>
	</body>
</html>
*/