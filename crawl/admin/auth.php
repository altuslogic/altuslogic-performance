<?php 
	error_reporting(E_ERROR | E_PARSE);	
	$admin = "";
	$admin_pw = "";

	session_start();
	
if (!isset($_SESSION['admin'])) {

	
		$_SESSION['admin'] = $admin;
		$_SESSION['admin_pw'] = $admin_pw;
	
	header("Location: admin.php");
} elseif ((isset($_SESSION['admin']) && isset($_SESSION['admin_pw']) &&$_SESSION['admin'] == $admin && $_SESSION['admin_pw'] == $admin_pw ) || (getenv("REMOTE_ADDR")=="")) {

} else {
	
	?>
	<html>
	<head>
	<title>Spider Login</title>
		<LINK REL=STYLESHEET HREF="admin.css" TYPE="text/css">
	</head>

	<body>
	<center>
	<br><br>
	
	<fieldset style="width:30%;"><legend><b>CRAWL Login</b></legend>
	<form action="auth.php" method="post">
	
	<table>
	<tr><td>Username</td><td><input type="text" name="user"></td></tr>
	<tr><td>Password</td><td><input type="password" name="pass"></td></tr>
	<tr><td></td><td><input type="submit" value="Log in" id="submit"></td>
	</tr></table>
	</form>
	</fieldset>
	</center>
	</body>
	</html>
	<?php 
	exit();
}


$settings_dir = "../settings";
include "$settings_dir/database.php";

?>