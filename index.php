<?php

function validate($data){
	$data = trim($data);
  	$data = stripslashes($data);
  	$data = htmlspecialchars($data);
  	$data = str_replace('\\', '', $data);
  	$data = str_replace('/', '', $data);
  	$data = str_replace("'", '', $data);
  	$data = str_replace(";", '', $data);
  	$data = str_replace("(", '', $data);
  	$data = str_replace(")", '', $data);
  	return $data;
}

session_start();

if(isset($_POST['username']) and isset($_POST['password'])){

	$value1 = addslashes($_POST['fullname']);
	$value2 = addslashes($_POST['username']);
	$value3 = addslashes($_POST['password']);
	$value4 = addslashes($_POST['email']);
	$value5 = $_POST['grade'];
	$valuec = $_POST['code'];
	
	$value1 = validate($value1);
	$value2 = validate($value2);
	$value3 = validate($value3);
	$value4 = validate($value4);
	$value5 = validate($value5);
	$valuec = validate($valuec);

	$value3 = password_hash($value3, PASSWORD_DEFAULT);
	
	require_once('php/connect.php');
	
	$sql = "SELECT id,code,name FROM chapters";
	
	$result = mysqli_query($link, $sql);
	
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
	
	$_SESSION['chapter'] = 'nochapter';
	$mychaptername = '';
	
	while(list($thisid,$thiscode,$thisname) = mysqli_fetch_array($result)){
		if($thiscode == $valuec){
			$_SESSION['chapter'] = $thisid;
			$mychaptername = $thisname;
		}
	}
	
	if($_SESSION['chapter'] != 'nochapter'){
	
		$chapter = $_SESSION['chapter'];
		
		$query= "SELECT * FROM users WHERE username='$value2'";

		$result = mysqli_query($link, $query);
	
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}
	
		$count = mysqli_num_rows($result);
	
		if($count == 0){
			$sqlp = "INSERT INTO users (fullname, username, password, email, grade, chapter) VALUES ('$value1', '$value2', '$value3', '$value4', '$value5', '$chapter')";
	
			if (!mysqli_query($link, $sqlp)){
				die('Error: ' . mysqli_error($link));
			}
		
			$mailMessage = "
			<html>
			<h1>Chaptersweet Account Registration</h1>
			<p>Your account has been successfully registered with Chaptersweet.</p>
			<p>To get started, visit <a href='http://chaptersweet.x10host.com'>http://chaptersweet.x10host.com</a>.</p>
			<p>You are registered to chapter : </html> $mychaptername <html></p>
			<p>Your account <b>Name</b> is : </html> $value1 <html></p>
			<p>Your account <b>Username</b> is : </html> $value2 <html></p>
			<p>Your account <b>Grade</b> is : </html> $value5 <html></p>
			<p>If you have any questions or concerns, contact your advisor.</p>
			<p>This email is automated, do not attempt to respond.</p>
			</html>
			";
		
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
			// More headers
			$headers .= 'From: Auto-Mail <chapters@xo7.x10hosting.com>' . "\r\n";
		
		
			mail($valuee,"Chaptersweet Registration",$mailMessage,$headers);
			
			$fmsg = 'Successfully Registered!';
		}
		else{
			$fmsg = 'Username Already in Use!';
		}
	}
	else{
		$fmsg = 'Invalid Chapter Code!';
	}

}

if(isset($_POST['user']) and isset($_POST['pass'])){

	$sessionUsername = $_POST['user'];
	$sessionPassword = $_POST['pass'];
	$chapter = $_POST['chapter'];
	
	$sessionUsername = validate($sessionUsername);
	$sessionPassword = validate($sessionPassword);
	$chapter = validate($chapter);
	
	$_SESSION['chapter'] = $chapter;
	
	require('php/connect.php');
	
	$query= "SELECT id FROM users WHERE username='$sessionUsername' AND chapter='$chapter'";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$count = mysqli_num_rows($result);

	if($count == 1){

		//fetch the rank of that user
		$query2 = "SELECT id, fullname, rank, grade, eventpoints, idnumber, password FROM users WHERE username='$sessionUsername' AND chapter='$chapter'";
		$result2 = mysqli_query($link, $query2);
		if (!$result2){
			die('Error: ' . mysqli_error($link));
		}

		list($idValue, $fullnameValue, $rankValue, $gradeValue, $eventPointsValue, $idnumber, $password) = mysqli_fetch_array($result2);

		if(password_verify($sessionPassword, $password)){

			$_SESSION['id'] = $idValue;
			$_SESSION['username'] = $sessionUsername;
			$_SESSION['rank'] = $rankValue;
			$_SESSION['fullname'] = $fullnameValue;
			$_SESSION['grade'] = $gradeValue;
			$_SESSION['eventpoints'] = $eventPointsValue;
			
			//get the conference
			$conferencequery="SELECT value FROM settings WHERE name='conference' AND chapter='$chapter'";
			
			$conferenceresult = mysqli_query($link, $conferencequery);
			
			if (!$conferenceresult){
				die('Error: ' . mysqli_error($link));
			}
			
			list($conference) = mysqli_fetch_array($conferenceresult);
			
			$_SESSION['conference'] = $conference;

		}

	}
	else{

		$fmsg = "Invalid Login Credentials";

	}

}

if(isset($_SESSION['username'])){

	header('Location: pages/main.php');

}else{

?>

<!DOCTYPE html>

<head>
	<!-- Global site tag (gtag.js) - Google Analytics ~ Will go here-->
		
	<!-- Bootstrap, cause it's pretty hecking neat. Plus we have it locally, cause we're cool -->
	<link rel="stylesheet" href="bootstrap-4.1.0/css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="bootstrap-4.1.0/js/bootstrap.min.js"></script>

    <!-- Google Fonts - Changes to come -->
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	
	<title>
		ProjeX
	</title>

	<!-- Import our CSS -->
	<link href="css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- Navbar -->
	<nav class="navbar navbar-dark bg-primary">
	  <a class="h1 navbar-brand" href="#">
	    <img src="" width="30" height="30" class="d-inline-block align-top" alt="" />
	    ProjeX
	  </a>
	</nav>

<!--Spooky stuff in the middle-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">

			</div>
		</div>
	</div>

</body>

<!--Less spooky stuff at the bottom-->
	<footer class="text-white bg-primary py-3 h5"> 
		<center><p class="bodyTextType2">
			Team 2004-901 2018
		</p></center>
	</footer>
</body>

<script src="js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>