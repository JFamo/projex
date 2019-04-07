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

//Handle Changing Workspaces
if(isset($_POST['workspace-id'])){

  $newworkspace = $_POST['workspace-id'];
  //~~JOSH~~
  //Need checking that the user really has this workspace here
  //Prevents client-side editing of workspace value to access those of other orgs
  //@Tom
  
	require('../php/connect.php');
  	$userID = $_SESSION['id'];
	$query="SELECT * FROM user_workspace_mapping WHERE workspace='$newworkspace' AND user='$userID'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
  if(mysqli_num_rows($result)>=1){
  		$_SESSION['workspace'] = $newworkspace;
  		$_SESSION['project'] = null;
	}

}

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

//Handle Firstname Editing
if(isset($_POST['edit-firstname'])){

	$firstname = $_POST['edit-firstname'];
	$firstname = validate($firstname);
	$user = $_SESSION['username'];
	
	require('../php/connect.php');

	$query = "UPDATE users SET firstname='$firstname' WHERE username='$user'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$_SESSION['firstname'] = $firstname;

	$fmsg = "Successfully Updated First Name!";

}

//Handle Lastname Editing
if(isset($_POST['edit-lastname'])){

	$lastname = $_POST['edit-lastname'];
	$lastname = validate($lastname);
	$user = $_SESSION['username'];
	
	require('../php/connect.php');

	$query = "UPDATE users SET lastname='$lastname' WHERE username='$user'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$_SESSION['lastname'] = $lastname;

	$fmsg = "Successfully Updated Last Name!";

}

//Handle Password Editing
if(isset($_POST['edit-password'])){

	$password = $_POST['edit-password'];
	$password = validate($password);
	$password = password_hash($password, PASSWORD_DEFAULT);
	$username = $_SESSION['username'];
	
	require('../php/connect.php');

	$query = "UPDATE users SET password='$password' WHERE username='$username'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$fmsg = "Successfully Changed Password!";

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
    <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet">
	
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
	<nav class="navbar navbar-dark bg-grey pb_bottom">
	  	<span id="openNavButton" style="font-size:30px;cursor:pointer;color:white;padding-right:30px;" onclick="toggleNav()">&#9776;</span>
	    <a class="nav-link" href="../php/logout.php">Logout</a>
	</nav>

<!--Spooky stuff in the middle-->
	<div class="container-fluid">
		<div class="row">
			<div id="mySidenav" style="padding-right:0; padding-left:0;" class="sidenav bg-grey">
				<nav style="width:100%;" class="navbar navbar-dark">
				  <div class="container" style="padding-left:0px;">
				  <ul class="nav navbar-nav align-top">
				   <a class="navbar-brand icon" href="#">Projex</a>
				   	<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#workspaceModal">
		              <?php 
		                require('../php/connect.php');
		                $workspace = $_SESSION['workspace'];
		                $query = "SELECT name FROM workspaces WHERE id='$workspace'";
		                $result = mysqli_query($link, $query);
		                if (!$result){
		                  die('Error: ' . mysqli_error($link));
		                }
		                list($name) = mysqli_fetch_array($result);
		                if($_SESSION['workspace'] == null || $_SESSION['workspace'] == null){
		                  echo "Select a Workspace";
		                }
		                else{
		                  echo $name;
		                }
		              ?>
		            </button>
			        <hr class="sidenavHR">
			        <a class="nav-link" href="main.php">Dashboard</a>
				    <a class="nav-link" href="metrics.php">Metrics</a>
				    <a class="nav-link" href="backlog.php">Backlog</a>
				    <a class="nav-link" href="active.php">Active</a>
				    <a class="nav-link" href="complete.php">Complete</a>
				    <a class="nav-link" href="docs.php">Docs</a>
				    <a class="nav-link" href="messages.php">Messages</a>
				    <hr class="sidenavHR">
				    <a class="nav-link active" href="account.php">My Account</a>
				    <a class="nav-link" href="organization.php">My Organization</a>
				  </ul>
				  </div>
				</nav>
			</div>
			<div id="pageBody">
			<div class="row">
			<div class="col-sm-12">
				<?php if(isset($fmsg)){ echo "<div class='card'><p>" . $fmsg . "</p></div>"; } ?>
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
				<form method="POST" class="pt-4">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-password">Change Password</label>
				      <input type="password" class="form-control" id="edit-password" name="edit-password">
				      <label for="repeat-password">Repeat Password</label>
				      <input type="password" class="form-control" id="repeat-password" name="repeat-password">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
				<br>
				<a href="../index.php">Return to Dashboard</a>
			</div>
		</div>
	</div>
	<footer class="bg-grey color-white pb_top">
			<center><p>
				Team 2004-901, 2019, All Rights Reserved
			</p></center>
		</footer>
	</div>
	</div>

	<!-- Workspace Selector Modal -->
  <div class="modal fade" id="workspaceModal" tabindex="-1" role="dialog" aria-labelledby="workspaceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="workspaceModalLabel">Workspaces</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        
          <?php
          require('../php/connect.php');

          $username = $_SESSION['username'];
          $query = "SELECT workspaces.name, workspaces.id FROM ((user_workspace_mapping INNER JOIN workspaces ON workspaces.id = user_workspace_mapping.workspace) INNER JOIN users ON user_workspace_mapping.user = users.id) WHERE users.username = '$username'";
          $result = mysqli_query($link, $query);
          if (!$result){
            die('Error: ' . mysqli_error($link));
          }
          while($resultArray = mysqli_fetch_array($result)){
          $workspaceName = $resultArray['name'];
          $workspaceID = $resultArray['id'];

          ?>
          <form method="POST"><input type="hidden" value="<?php echo $workspaceID; ?>" name="workspace-id"/><input class="dropdown-item <?php if($_SESSION['workspace'] == $workspaceID){ echo 'active-dropdown'; } ?>" type="submit" value="<?php echo $workspaceName; ?>"></form>
          <?php } ?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="workspace.php">Create New</a>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
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