<!DOCTYPE HTML>

<!-- NOTE:   IF VIEWING ON SAFARI, GOOGLE SERVICES ONLY WORK ON WIFI!!! -->
<html lang="en">
	<head> 
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
	<title>YAK-YIK</title>
	<script yakYik src="jquery-2.1.0.js"></script>
	<script yakYik src="https://maps.googleapis.com/maps/api/js"></script>
	<link rel="stylesheet" media="screen and (max-device-width: 1024px)" href="phone.css">
	<link rel="stylesheet" media="screen and (max-width: 1264px)" href="phone.css">
	<link rel="stylesheet" media="screen and (min-width: 1265px)" href="computer.css">

	<script yakYik type="text/javascript">
		
		/*when called, doesnt actually change page, but shows appropriate divs to be viewed on screen
    	   for the register screen*/
		function switchToRegister(){
			$("#login_table").show(),$("#regYak").show(),$("#Email").show(),$("#login_email").show();
			$("#map-canvas").hide(),$("#posts").hide(),$("#post_edits").hide(),$("#dispNum").hide(),$("#Logout").hide();
			$("#submit").val("Register");
			$("#submit").attr('name', 'Register');
			clearText();
		}
		/*when called, doesnt actually change page, but shows appropriate divs to be viewed on screen
    	   for the Login screen screen Recent local activity can be viewed but no posts can be made on this screen*/
    	function switchToLogin(){
    		$("#login_table").show(),$("#dispNum").show(),$("#posts").show(),$("#Logout").show();
			$("#map-canvas").hide(),$("#post_edits").hide(),$("#regYak").hide(),$("#Email").hide(),$("#login_email").hide();	
    		$("#submit").val("Login");
    		$("#submit").attr('name', 'LOGIN');
    		clearText();
    		happyPosting();
			refreshPosts();
    	}
    	/*when called, doesnt actually change page, but shows appropriate divs to be viewed on screen
    	   for the post screen, only can be viewed when logged in and has google map showing location*/
    	function switchToPosts(){
    		if (happyPosting() == true) {
	    		$("#dispNum").show(),$("#posts").show(),$("#post_edits").show(),$("#map-canvas").show();
				$("#login_error").show(),$("#regYak").hide(),$("#login_table").hide(),$("#Logout").hide();
				/*Creates and resizes the google map and displays it on the left hand side*/
				initializeMap();
				google.maps.event.trigger(map, 'resize');
				refreshPosts();
    		}else{
    			alert("MUST BE LOGGED IN TO POST");	
    		}
    	}
    	/*when called, doesnt actually change page, but shows appropriate divs to be viewed on screen
    	   for the post screen*/
    	function initializeMap() {
			var mapOptions = {zoom: 8};
			map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
		  	if(navigator.geolocation) {
		    	navigator.geolocation.getCurrentPosition(function(position) {
		      		var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			        var infowindow = new google.maps.InfoWindow({
			        	map: map,
			        	position: pos,
			        	content: 'You are here!'
			        });
			      	map.setCenter(pos);
		    	}, function() {
		      		$("#login_error").text("ERROR GETTING COORDINATES");
		    	});
		  	}else{
		    	$("#login_error").text("REQUIRES LOCATION");
		  	}
		}
		/*When called, clears all info on the screen (ie. text box, checkboxes, any input*/
		function clearText(){
			$("#text_area").val("");
			$("#login_username").val(""),$("#login_password").val(""),$("#login_email").val("");
			$('#Bold').attr('checked', false);
			$('#Italic').attr('checked', false);
		}
		/*Function checking if user is logged in, then displays "happy posting: "USERNAME" to notify user is logged in*/
		function happyPosting(){
			var happyString = 'Happy="SET"';
			$.ajax({
	            type: "POST",
	            url: "Register.php",
	            data: happyString,
	            success: function(response){
	            	if(response.length == 27){
	            		$("#login_error").text(response);
	            	}else{
	            		return false;
	            	}
	            }
		    });
		    if ($("#login_error").text() == "JOIN THE HERD TODAY!" || $("#login_error").text() == "YOU ARE NOW LOGGED OUT") {
		    	return false;
		    }else{
		    	return true;
		    }
		}
		/*Called to check the database for all relevant posts to show on the screen*/
		function refreshPosts(){
			navigator.geolocation.getCurrentPosition(function(position){
	    		happyPosting();
				$("#posts").text("");
				var pageNum = $("#pages_select").val();
				var radius = $("#radius_select").val();
				/*Uses current longitude and latitude of the user*/
				var lat = position.coords.latitude;
   				var lon = position.coords.longitude;
				var postString = 'Posts=Posts&Pages='+pageNum+'&Radius='+radius+'&Lon='+lon+'&Lat='+lat;
			        $.ajax({
			            type: "POST",
			            url: "Register.php",
			            data: postString,
			            success: function(response){
			            	$("#posts").html(response);
			            }
			        });
			    return false;
			});
		}
		/*Inserts posts to database. Does not do the actual displaying. Uses all input paramaters on screen 
		   such as textboxes, drop down to satisfy all user requests */
		function makePost(type){
			if (navigator.geolocation){
		       navigator.geolocation.getCurrentPosition(function(position){
		       		if($("#text_area").val().trim() != ""){
			            var content = $("#text_area").val();
			            var color = $("#color_select").val();
			            var radius = $("#radius_select").val();
			            var lat = position.coords.latitude;
   						var lon = position.coords.longitude;
			            var uploadString = 'Color='+color+'&Content='+content+'&'+'Upload=Upload&Lon='+lon+'&Lat='+lat+'&Radius='+radius;
			            /*Checks inputs on screen, and adds them to the JQUERY string if they apply*/
			            if (type){
			            	uploadString = uploadString + '&Pic="True"';
			            }else{
			            	uploadString = uploadString + '&Note="True"';
			            }
			         	if ($('#Bold').is(':checked')){
			         		uploadString = uploadString + '&Bold="True"';
			         	}
			         	if ($('#Italic').is(':checked')){
			         		uploadString = uploadString + '&Italic="True"';
			         	}
				        $.ajax({
				            type: "POST",
				            url: "Register.php",
				            data: uploadString,
				            success: function(response){
				            	if(response.length != 3){
				            		refreshPosts();
				            	}else {
				            		/*Displays messages in 'login_error' div if any errors apply */
				            		$("#login_error").text("MUST BE LOGGED IN TO POST!");
				            	}
				            },
				        });
				        clearText();
			   		 }else{
			    		$("#login_error").text("TEXTBOX IS EMPTY!");
			    	}
			    });
			}else {
				$("#login_error").text("BROWSER DOES NOT SUPPORT THIS SITE");
			}    
	}
	/*At any time the logout button is pressed on home screen, it destroys the session and
		displays logged out message, you can not no longer access the posts page */
	function Logout(){
		var logoutString = "Logout=true";
		$.ajax({
            type: "POST",
            url: "connect2db.php",
            data: logoutString,
            success: function(){
            	$("#login_error").text("YOU ARE NOW LOGGED OUT");
            },
        });
		return false;
	}
	/* When the page is refreshed or first loaded, it initializes the google map, and switches to the login page*/
	$(document).ready(function(locale){

		initializeMap();
	  	google.maps.event.trigger(map, 'resize'); 
	  	switchToRegister();
		switchToLogin();
		/* A listener checking for anytime a login or register post is submitted */
    	$("#Login_Form").submit(function(e){
    		e.preventDefault();
            var username = $("#login_username").val();
            var password = $("#login_password").val();
            var email = $("#login_email").val();
            var login = $("#submit").val();
            var loginString ='Username='+username+'&Password='+password+'&'+login+'='+login+'&Email='+email;
	        $.ajax({
	            type: "POST",
	            url: "Register.php",
	            data: loginString,
	            success: function(response){
	            	$("#login_error").text(response); 
	            }
	        });
	        clearText();   
		});
	});

	</script>
	</head>
	<body>
		<!-- Border divs -->
		<div id="left"></div>
		<div id="right"></div>
		<div id="top"></div>
		<div id="bottom"></div>
		<div id="middle1"></div>
		<div id="middle2"></div>
		<div id="middle3"></div>

		<h1> YAK-YIK </h1>
		<div id="black_yak" onclick='window.location.href="https://www.youtube.com/watch?v=BEgx9t3cw30"' ></div>
		<!--  3 page choices displayed at top -->
		<div id="links">
			<a id="home" onclick="switchToLogin()">HOME</a>
			<a id="register" onclick="switchToRegister()">REGISTER</a>
			<a id="postit" onclick="switchToPosts()">POST-IT!</a>
		</div>
		<!-- Contains the login and register information to be submitted -->
		<div id="content">
			<form id="Login_Form" method="post" autocomplete="off" >
				<table id="login_table"> 
					<tr> <td> <h3 id="Username">Username: <input id="login_username" type="text" name="Username" maxlength="15" size="25" autocomplete="off"> </h3>
      				<td></tr>
      				<tr><td> <h3 id="Password">Password: <input id="login_password" type="password" name="Password" maxlength="15" size="25"> </h3>
					</td></tr>
					<tr><td> <h3 id="Email">Email: <input id="login_email" type="text" name="Email" size="25"> </h3>
					</td></tr>
					<tr> <td> <div id="submit_button">
						<input type="submit" value="Login" name="LOGIN" id="submit">
						<a id="Logout" type="button" onclick="Logout()">Logout</a>
					</td></tr>
				</table> 
			</form>
			<!-- Miscellaneous divs containing images and extras displayed on page -->
			<img src="YAK2.png" id="large_Yak">
			<img src='regYak.png' id='regYak'>
			<div id="posts"></div>
			<div id="map-canvas"></div>
			<div id="login_error">JOIN THE HERD TODAY!</div>
			<!-- Input colletion of items used to to submit a post -->
			<form id="Make_Post"> 
				<div id="dispNum"> <p id="pageNum">DISPLAYING: <select id="pages_select" onchange="refreshPosts()">
					<option value="25">25</option> <option value="50">50</option> <option value="100">100</option>
					<option value="250">250</option> <option value="500">500</option> <option value="1000">1000</option>
					</select> MOST RECENT POSTS</p></div>
				<div id='post_edits'><textarea id="text_area"rows="4" cols="55" placeholder="Text Note: enter text and hit Post Note        Picture: enter url to photo and hit Post Pic"></textarea>
					<table id="post_table"><tr>
						<td><select id="color_select"><option value="black">Black</option><option value="red">Red</option><option value="blue">Blue</option>
							<option value="green">Green</option><option value="orange">Orange</option><option value="pink">Pink</option></select></td>
						<td><input type="checkbox" id="Bold" name="Bold" value="Bold">Bold</td>
						<td><input type="checkbox" id="Italic" name="Italic" value="Italic">Italic</td>
						<td><button type="button" value="Post Note" onclick="makePost(false)" id="Post_Note">Post Note</button></td>
						<td><button type="button" value="Post Pic" onclick="makePost(true)" id="Post_Pic">Post Pic</button></td></tr>
				</div> 
				<!-- Choice of post radius -->
				<div id="radius"> <p id="radiusChoice">SEARCHING FOR POSTS <select id="radius_select" onchange="refreshPosts()">
					<option value="25">25</option> <option value="50">50</option> <option value="100">100</option>
					<option value="250">250</option> <option value="500">500</option> <option value="1000">1000</option>
					</select>  KM FROM YOU</p>
				</div>
			</form>
		</div>
	</body>
</html>
