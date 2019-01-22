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

//Handle Username Editing
if(isset($_POST['edit-username'])){

	$username = $_POST['edit-username'];
	$username = validate($username);
	$currentusername = $_SESSION['username'];
	
	require('../php/connect.php');
	
	$query= "SELECT id FROM users WHERE username='$username'";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$count = mysqli_num_rows($result);

	if($count == 0){

		$query2 = "UPDATE users SET username='$username' WHERE username='$currentusername'";
		$result2 = mysqli_query($link, $query2);
		if (!$result2){
			die('Error: ' . mysqli_error($link));
		}

		$_SESSION['username'] = $username;

		$fmsg = "Successfully Updated Username!";

	}
	else{

		$fmsg = "Username Already Taken!";

	}

}

if(!isset($_SESSION['username'])){

	header('Location: ../index.php');

}else{

?>

<!DOCTYPE html>

<head>
	<!-- Global site tag (gtag.js) - Google Analytics ~ Will go here-->
		
	<!-- Bootstrap, cause it's pretty hecking neat. Plus we have it locally, cause we're cool -->
	<link rel="stylesheet" href="../bootstrap-4.1.0/css/bootstrap.min.css">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../bootstrap-4.1.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>

    <!-- Google Fonts - Changes to come -->
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	
	<title>
		ProjeX
	</title>

	<!-- Import our CSS -->
	<link href="../css/main.css" rel="stylesheet" type="text/css" />

	<!-- Mobile metas -->
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body>
<!-- Navbar -->
	<nav class="navbar navbar-dark bg-primary">
		<div class="navpadder">
		  	<a class="nav-link" href="main.php" style="flex-basis:20%;"><img src="" width="30" height="30" class="d-inline-block align-top" alt="" />ProjeX</a>
		  	<a class="nav-link" href="#"><img src="../imgs/workspacePlaceholder.png" width="30" height="30" class="d-inline-block align-top" alt="" /></a>
		    <a class="nav-link" href="metrics.php">Metrics</a>
		    <a class="nav-link" href="metrics.php">Backlog</a>
		    <a class="nav-link" href="metrics.php">Active</a>
		    <a class="nav-link" href="metrics.php">Docs</a>
		    <a class="nav-link" href="metrics.php">Messages</a>
		    <a class="nav-link" href="../php/logout.php">Logout</a>
	    </div>
	</nav>

<!--Spooky stuff in the middle-->
	<div class="container-fluid bodycontainer">

	<?php echo $fmsg ?>

		<div class="row">
			<div class="col-sm-12">
				<h1>My Account</h1>
				<p>This is a placeholder page for your account management page.</p>
				<hr>
				<b>Username : </b><?php echo $_SESSION['username']; ?>		<br>
				<b>First Name : </b><?php echo $_SESSION['firstname']; ?>	<br>
				<b>Last Name : </b><?php echo $_SESSION['lastname']; ?>		<br>
				<form method="POST" class="pt-4">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-firstname">Change First Name</label>
				      <input type="text" class="form-control" id="edit-firstname" name="edit-firstname" value="<?php echo $_SESSION['firstname']; ?>">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
				<form method="POST" class="pt-4">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-lastname">Change Last Name</label>
				      <input type="text" class="form-control" id="edit-lastname" name="edit-lastname" value="<?php echo $_SESSION['lastname']; ?>">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
				<form method="POST" class="pt-4">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-username">Change Username</label>
				      <input type="text" class="form-control" id="edit-username" name="edit-username" value="<?php echo $_SESSION['username']; ?>">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
			</div>
		</div>
	</div>

</body>

<!--Main has no footer
<footer class="text-white bg-primary py-3 h5"> 
	<center><p class="bodyTextType2">
		Team 2004-901 2018
	</p></center>
</footer>
-->

<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>