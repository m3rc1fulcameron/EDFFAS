					<li><a href="index.php" title="Search">Search</a></li><!--
					--><li><a href="submit.php" title="Submit Names">Submissions</a></li>
					<?php
						if (!isset($_SESSION["un"])) {
							print("<!--
							--><li>
						<form id=\"loginForm\" action=\"index.php?action=login\" method=\"POST\">
							<input type=\"text\" name=\"username\" placeholder=\"Username\">
							<input type=\"password\" name=\"password\" placeholder=\"Password\">
							<input type=\"hidden\" name=\"csrfToken\" value=\"" . $_SESSION['csrfToken'] . "\">
							<button type=\"submit\" name=\"submit\" title=\"Login\">Login</button>
						</form>
					</li>");
							print("<!--\n\t\t\t\t\t--><li><a href=\"register.php\" title=\"Register\">Register</a></li>");
						} else {
							print("<!--\n\t\t\t\t\t--><li><a href=\"control.php\" title=\"Control Panel\">Control Panel</a></li>");
							print("<!--\n\t\t\t\t\t--><li><a href=\"index.php?action=logout\" title=\"Logout\">Logout</a></li>");
						}
						
						if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {
							print("<!--\n\t\t\t\t\t--><li><a href=\"admin.php\" title=\"Admin Control Panel\">Admin Control Panel</a></li>");
						}
					?>