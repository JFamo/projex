<?php

//Basic function to sanitize input data
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

//Function to randomly generate a unique organization code
function createOrgCode(){
	return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
}

//Function to check if organization code is unique
function isCodeUnique($code){
	require('php/connect.php');
	$query= "SELECT id FROM organizations WHERE code='$code'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
	$count = mysqli_num_rows($result);
	if($count == 0){
		return true;
	}
	else{
		return false;
	}
}

session_start();

//Handle user login
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
			$_SESSION['workspace'] = null;

		}

	}
	else{

		$fmsg = "Invalid Login Credentials";

	}

}

if(isset($_POST['register-username']) and isset($_POST['register-password']) and (isset($_POST['organization-name']) or isset($_POST['organization-code']))){

	$username = $_POST['register-username'];
	$password = $_POST['register-password'];
	$orgname = $_POST['organization-name'];
	$orgcode = $_POST['organization-code'];
	$orgaction = $_POST['organization-action'];

	$username = validate($username);
	$password = validate($password);

	$password = password_hash($password, PASSWORD_DEFAULT);
	
	require('php/connect.php');
	
	$query= "SELECT id FROM users WHERE username='$username'";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$count = mysqli_num_rows($result);

	if($count == 0){

		//Check that specified organization code exists if joining
		$orgwithcodecount = 0;

		if($orgaction == "join"){
			$query= "SELECT id FROM organizations WHERE code='$orgcode'";
			$result = mysqli_query($link, $query);
			if (!$result){
				die('Error: ' . mysqli_error($link));
			}
			list($orgid) = mysqli_fetch_array($result);
			$orgwithcodecount = mysqli_num_rows($result);
		}

		if($orgwithcodecount == 1 || $orgaction == "create"){

			//User Creation
			$query2 = "INSERT INTO users (username, password, firstname, lastname) VALUES ('$username', '$password', '$firstname', '$lastname')";
			$result2 = mysqli_query($link, $query2);
			$userid = mysqli_insert_id($link);
			if (!$result2){
				die('Error: ' . mysqli_error($link));
			}

			//Organization Creation
			if($orgaction == "create"){
				//Generate organization code
				$newOrgCode = createOrgCode();
				while(!isCodeUnique($newOrgCode)){
					$newOrgCode = createOrgCode();
				}

				//Actually perform creation query
				$query2 = "INSERT INTO organizations (name, code) VALUES ('$orgname', '$newOrgCode')";
				$result2 = mysqli_query($link, $query2);
				$orgid = mysqli_insert_id($link);
				if (!$result2){
					die('Error: ' . mysqli_error($link));
				}
			}

			//Organization Join
			$query2 = "INSERT INTO user_organization_mapping (organization, user) VALUES ('$orgid', '$userid')";
			$result2 = mysqli_query($link, $query2);
			if (!$result2){
				die('Error: ' . mysqli_error($link));
			}

			//Add Rank for Owner
			if($orgaction == "create"){
				//Actually perform creation query
				$query2 = "INSERT INTO user_ranks (user, scope, rank) VALUES ('$userid', 'organization', 'owner')";
				$result2 = mysqli_query($link, $query2);
				if (!$result2){
					die('Error: ' . mysqli_error($link));
				}
			}

			$fmsg = "Successfully Registered!";

		}
		else{

			$fmsg = "Invalid Organization Code!";

		}

	}
	else{

		$fmsg = "This Username is Taken!";

	}

}

//Authenticate session and force redirect on real session
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
						<form method="POST" class="pt-4">
						  <div class="form-row">
						    <div class="form-group col-md-6">
						      <label for="login-username">Username</label>
						      <input type="text" class="form-control" id="login-username" name="login-username" placeholder="Username">
						    </div>
						    <div class="form-group col-md-6">
						      <label for="login-password">Password</label>
						      <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password">
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
						<form method="POST" class="py-4">
						  <div class="form-row">
						    <div class="form-group col-md-6">
						      <label for="register-username">Username</label>
						      <input type="text" class="form-control" id="register-username" name="register-username" placeholder="Username" value="<?php echo isset($_POST['register-username']) ? $_POST['register-username'] : '' ?>">
						    </div>
						  </div>
						  <div class="form-row">
						  	<div class="form-group col-md-6">
						      <label for="register-password">Password</label>
						      <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" value="<?php echo isset($_POST['register-password']) ? $_POST['register-password'] : '' ?>">
						    </div>
						    <div class="form-group col-md-6">
						      <label for="register-confirm">Confirm Password</label>
						      <input type="password" class="form-control" id="register-confirm" name="register-confirm" placeholder="Retype Password" value="<?php echo isset($_POST['register-confirm']) ? $_POST['register-confirm'] : '' ?>">
						    </div>
						  </div>
						  <div class="form-row">
							<div class="form-group col-md-12">
							  <label for="register-email">Email</label>
							  <input type="email" class="form-control" id="register-email" name="register-email" placeholder="Email" value="<?php echo isset($_POST['register-email']) ? $_POST['register-email'] : '' ?>">
							</div>
						  </div>
						  <div class="form-row">
							<div class="form-group col-md-4">
							  <label for="register-email">I Want To...</label>
							  <select class="form-control" id="organization-action" name="organization-action" onchange="changeOrganizationAction();">
							  	<option value="join">Join an Existing Organization</option>
							  	<option value="create">Create a New Organization</option>
							  </select>
							</div>
							<div class="form-group col-md-8" id="organizationCodeForm">
							  <label for="organization-code">Organization Code</label>
							  <input type="text" class="form-control" id="organization-code" name="organization-code" placeholder="Code">
							</div>
							<div class="form-group col-md-8" id="organizationNameForm" style="display:none;">
							  <label for="organization-name">Organization Name</label>
							  <input type="text" class="form-control" id="organization-name" name="organization-name" placeholder="Name">
							</div>
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