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
	if($result){
  		$_SESSION['workspace'] = $newworkspace;
  		$_SESSION['project'] = null;
	}

}

//Handle New Project Creation
if(isset($_POST['project-name'])){

	$wsname = $_POST['project-name'];
	$wsname = validate($wsname);
	$username = $_SESSION['username'];
	$myid = $_SESSION['id'];
	$workspace = $_SESSION['workspace'];
	
	require('../php/connect.php');

	//Start by getting organization ID of user
	$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
	list($orgid) = mysqli_fetch_array($result);

	//Next create the project itself
	$query2 = "INSERT INTO projects (workspace, name) VALUES ('$workspace', '$wsname')";
	$result2 = mysqli_query($link, $query2);
	if (!$result2){
		die('Error: ' . mysqli_error($link));
	}
	$wsid = mysqli_insert_id($link); //Save the AI project ID

	//Finally add the checked users to the project
	//Force org owner into project
	$query2 = "INSERT INTO user_project_mapping (project, user) VALUES ('$wsid', '$myid')";
	$result2 = mysqli_query($link, $query2);
	if (!$result2){
		die('Error: ' . mysqli_error($link));
	}
	//Iterate other users
	if(!empty($_POST['project-user'])){
		foreach($_POST['project-user'] as $userID){
			$query2 = "INSERT INTO user_project_mapping (project, user) VALUES ('$wsid', '$userID')";
			$result2 = mysqli_query($link, $query2);
			if (!$result2){
				die('Error: ' . mysqli_error($link));
			}
		}
	}

	$fmsg = "Successfully Created Project!";

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
				    <a class="nav-link" href="account.php">My Account</a>
				    <a class="nav-link" href="organization.php">My Organization</a>
				  </ul>
				  </div>
				</nav>
			</div>
			<div id="pageBody">
			<div class="row">
			<div class="col-sm-12">
				<?php if(isset($fmsg)){ echo "<div class='card'><p>" . $fmsg . "</p></div>"; } ?>
				<h1>Create Project</h1>
				<p>Create a new project in the current workspace.</p>
				<hr>
				<?php 
require('../php/connect.php');
$username = $_SESSION['username'];
$query = "SELECT user_ranks.rank FROM (user_ranks INNER JOIN users ON user_ranks.user = users.id) WHERE user_ranks.rank = 'owner' AND users.username = '$username'";
$result = mysqli_query($link, $query);
if (!$result){
	die('Error: ' . mysqli_error($link));
}
$count = mysqli_num_rows($result);
if($count == 1){
				 ?>
				<form method="POST" class="pt-2">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-name">Project Name</label>
				      <input type="text" class="form-control" id="project-name" name="project-name" placeholder="New Project Name...">
				    </div>
				  </div>
				  <div class="form-row">
				  	<div class="form-group col-md-6">
				      <label for="edit-name">Add Users to Project</label><br>
				      <?php

			        	require('../php/connect.php');

			        	$username = $_SESSION['username'];

			        	//Start by getting organization ID of user
						$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
						$result = mysqli_query($link, $query);
						if (!$result){
							die('Error: ' . mysqli_error($link));
						}
						list($orgid) = mysqli_fetch_array($result);

						//Next grab firstname, lastname, and id of users in same organization
						$query = "SELECT users.id, users.firstname, users.lastname FROM (user_organization_mapping INNER JOIN users ON user_organization_mapping.user = users.id) WHERE user_organization_mapping.organization = '$orgid'";
						$result = mysqli_query($link, $query);
						if (!$result){
							die('Error: ' . mysqli_error($link));
						}
						while($resultArray = mysqli_fetch_array($result)){
						$thisFirstname = $resultArray['firstname'];
						$thisLastname = $resultArray['lastname'];
						$thisID = $resultArray['id'];

			        	?>

			          		<input type="checkbox" <?php if($thisID != $_SESSION['id']){ echo "name='project-user[]'"; } ?> value="<?php echo $thisID; ?>" <?php if($thisID == $_SESSION['id']){ echo "checked='checked' onclick='return false;'"; } ?>> <?php echo $thisFirstname . " " . $thisLastname . " (" . $thisID . ")"; ?><br>

			          <?php } ?>
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Create</button>
				</form>
<?php
}
else{
	echo "<p>You must be the organization owner to create a workspace!</p>";
}
?>
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
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

</body>

<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>