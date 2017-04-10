<?php
	//calls database
	require_once "connect2db.php";
	if (isset($_POST['Register']) || isset($_POST['Login'])){
		if($_SESSION["loggedIn"] == "false"){
			if(isset($_REQUEST['Username']) && $_REQUEST['Username'] == ""){
				$loginError = "USERNAME IS REQUIRED!"; 	
			}else if(isset($_REQUEST['Password']) && $_REQUEST['Password'] == ""){
				$loginError = "PASSWORD IS REQUIRED!";
			}else if(isset($_REQUEST['Username']) && strlen($_REQUEST['Username']) < 6 || isset($_REQUEST['Password']) && strlen($_REQUEST['Password']) < 6 ){
				$loginError = "AT LEAST 6 CHARACTERS!";
			}else if(isset($_REQUEST['Username']) && isset($_REQUEST['Password'])){
				if (isset($_POST['Register'])){

					//checks if username is already in the databse
					$query = "select case when exists(select Username from Members where Username='".$_REQUEST['Username']."') then 'true' else 'false' end;";
                   	$Result = pg_query($dbconnect,$query);
					while ($row = pg_fetch_row($Result)) {
						if($row[0] == "true"){
							$loginError = "USERNAME ALREADY EXISTS!";
						}else{ 	
						//if not, will register the account and add it to the database using md5 hashing to encrypt the password
							pg_query($dbconnect,"insert into Members(Username,Password,Email) values('".$_REQUEST['Username']."','".md5($_REQUEST['Password'])."','".$_POST['Email']."');");
							$loginError = "ACCOUNT SUCCESFULLY ADDED!";
						}
        			}

				}else if(isset($_POST['Login'])){
					// finds the password and username
					$query =  "select username,password from Members where username = '".$_REQUEST['Username']."';";
					$Result = pg_query($dbconnect,$query);
					while ($row = pg_fetch_row($Result)) {
						//if the password matches, logs the user in
						if($row[1] == (md5($_REQUEST['Password']))){
							
							$_SESSION["loggedIn"] = "true";
							$loginError = "YOU ARE NOW LOGGED IN!";
							$_SESSION['user'] = $_REQUEST['Username']; 
						}else{
							$loginError = "INCORRECT NAME/ PASSWORD";
						}
					}
					// if no results for the username/password combo
					if(pg_num_rows($Result) == 0){
						$loginError = "INCORRECT NAME/ PASSWORD";
					}	
				}	
			}
		}else{
			$loginError = "ALREADY LOGGED IN AS: ".$_SESSION['user']."!"; 
		}
		echo $loginError;
	}
	/* when refresh posts is called, this generated the posts that are to be displayed given 
		relevant parameters */
	else if (isset($_REQUEST['Posts'])){
		$postResults = "";		
		$query = "select * from posts where (earth_distance(ll_to_earth(to_number('".$_POST['Lat']."','9999.99999999999'),to_number('".$_POST['Lon']."','9999.99999999999')), ll_to_earth(latitude,longitude))/1000 < to_number('".$_POST['Radius']."','9999')) order by postnum desc limit to_number('".$_POST['Pages']."','9999');";
		$Result = pg_query($dbconnect,$query);
		while ($row = pg_fetch_row($Result)) {	
			$postResults = $postResults."<p style='color:".$row[3]."'>".$row[4]."</p><hr width=100%>";
		}
		//Disallows users to post html to alter board
		echo strip_tags($postResults,'<img><p><font><i><br><hr>');
	}
	//Database call to insert either a picture link or text post
	else if (isset($_POST['Upload'])){
		if($_SESSION["loggedIn"] == "true"){
			$query = 'select * from posts order by postNum desc limit 1;';
			pg_query($dbconnect,$query);
			$Result = pg_query($dbconnect,$query);
			while ($row = pg_fetch_row($Result)) {
				$postNum = $row[0];
			}
			$nextPostNum = $postNum + 1;
			$post = pg_escape_string($_POST['Content']);
			if(isset($_POST['Note'])){
				if (isset($_POST['Bold'])){
					$post = '<font size=6>'.$post.'</font>';
				}
				if (isset($_POST['Italic'])){
					$post = "<i>".$post."</i>";
				}
			}
			else if(isset($_POST['Pic'])){
				$post = '<img src="'.$post.'" style="max-width:100%">';
			}
			$query =  "insert into posts (postNum,latitude,longitude,color,post) values (".$nextPostNum.",".$_POST['Lat'].",".$_POST['Lon'].",'".$_POST['Color']."','".$post."');";
			pg_query($dbconnect,$query);	
			echo '"true"';
		}
	}
	else if (isset($_POST['Happy'])){
		if($_SESSION["loggedIn"] == "true"){
			echo 'HAPPY POSTING: '.$_SESSION['user'].'!';
		}else{
			echo "JOIN THE HERD TODAY!";
		}
	}
?>