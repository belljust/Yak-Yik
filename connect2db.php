<?php
	session_save_path("");
	session_start();
	$dbconnect = pg_connect("host=localhost user=belljust password=98555 dbname=belljust");
	if (!$dbconnect) {
  		echo "An error occurred.\n";
  		exit;
	}
	if(!isset($_SESSION["loggedIn"])){
		$_SESSION["loggedIn"] = "false";
		$_SESSION['user'] = "";
	}
	if(isset($_POST['Logout'])){
		$_SESSION["loggedIn"] = "false";
		session_destroy();
	}
?>
	

