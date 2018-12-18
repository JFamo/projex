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

if(isset($_POST['login-username']) and isset($_POST['login-password'])){

	$username = $_POST['login-username'];
	$password = $_POST['login-password'];
	
	$username = validate($username);
	$password = validate($password);
	
	require('php/connect.php');
	
	$query= "SELECT id FROM users WHERE username='$username'";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$count = mysqli_num_rows($result);

	if($count == 1){

		//fetch the rank of that user
		$query2 = "SELECT id, password, firstname, lastname FROM users WHERE username='$username'";
		$result2 = mysqli_query($link, $query2);
		if (!$result2){
			die('Error: ' . mysqli_error($link));
		}

		list($idValue, $passwordValue, $firstnameValue, $lastnameValue) = mysqli_fetch_array($result2);

		if(password_verify($password, $passwordValue)){

			$_SESSION['id'] = $idValue;
			$_SESSION['username'] = $username;
			$_SESSION['firstname'] = $firstnameValue;
			$_SESSION['lastname'] = $lastnameValue;

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
				<?php

					if(isset($fmsg)){
						echo "<p>" . $fmsg . "</p>";
					}

				?>
				<div class="row">
					<div class="col-12">
						<center>
							<h3 class="pt-4">Login</h3>
						</center>
						<form method="post" class="pt-4">
						  <div class="form-row">
						    <div class="form-group col-md-6">
						      <label for="login-username">Username</label>
						      <input type="text" class="form-control" id="login-username" name="login-username" placeholder="Username">
						    </div>
						    <div class="form-group col-md-6">
						      <label for="login-password">Password</label>
						      <input type="password" class="form-control" id="login-password" placeholder="Password">
						    </div>
						  </div>
						  <button type="submit" class="btn btn-primary">Log In</button>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<center>
							<h3 class="pt-4">Register</h3>
						</center>
						<form method="post" class="py-4">
						  <div class="form-row">
						    <div class="form-group col-md-6">
						      <label for="reigster-username">Username</label>
						      <input type="text" class="form-control" id="reigster-username" name="reigster-username" placeholder="Username">
						    </div>
						  </div>
						  <div class="form-row">
						  	<div class="form-group col-md-6">
						      <label for="reigster-password">Password</label>
						      <input type="password" class="form-control" id="reigster-password" name="reigster-password" placeholder="Password">
						    </div>
						    <div class="form-group col-md-6">
						      <label for="reigster-confirm">Confirm Password</label>
						      <input type="password" class="form-control" id="reigster-confirm" name="reigster-confirm" placeholder="Retype Password">
						    </div>
						  </div>
						  <div class="form-group">
						    <label for="reigster-email">Email</label>
						    <input type="email" class="form-control" id="reigster-email" name="reigster-email" placeholder="1234 Main St">
						  </div>
						  <button type="submit" class="btn btn-primary">Sign Up</button>
						</form>
					</div>
				</div>
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

<script src="js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>