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
  
  $_SESSION['workspace'] = $newworkspace;
  $_SESSION['project'] = null;

}

//Handle Organization Name Editing
if(isset($_POST['edit-name'])){

	$newname = $_POST['edit-name'];
	$newname = validate($newname);
	$username = $_SESSION['username'];
	
	require('../php/connect.php');
	$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
	list($orgid) = mysqli_fetch_array($result);

	$query2 = "UPDATE organizations SET name = '$newname' WHERE id = '$orgid'";
	$result2 = mysqli_query($link, $query2);
	if (!$result2){
		die('Error: ' . mysqli_error($link));
	}

	$fmsg = "Successfully Updated Name!";

}

//Handle Workspace Name Editing
if(isset($_POST['workspace-name'])){

	$newname = $_POST['workspace-name'];
	$wsid = $_POST['workspace-id'];
	$newname = validate($newname);
	$wsid = validate($wsid);
	$username = $_SESSION['username'];
	
	//~~JOSH~~
	//Need checking that the user really has this workspace
	//Prevents client-side editing of wsid to access those of other orgs
	//@Tom
	require('../php/connect.php');
	$query = "UPDATE workspaces SET name='$newname' WHERE id='$wsid'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$fmsg = "Successfully Updated Name!";

}

//Handle Removing Users from Workspaces
if(isset($_POST['workspace-userid'])){

	$userid = $_POST['workspace-userid'];
	$wsid = $_POST['workspace-id'];
	$userid = validate($userid);
	$wsid = validate($wsid);
	$username = $_SESSION['username'];
	
	//~~JOSH~~
	//Need checking that the user really has this workspace
	//Prevents client-side editing of wsid to access those of other orgs
	//@Tom
	require('../php/connect.php');
	$query = "DELETE FROM user_workspace_mapping WHERE user='$userid' AND workspace='$wsid'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$fmsg = "Successfully Removed User from Workspace!";

}

//Handle Adding Users to Workspaces
if(isset($_POST['workspace-adduser'])){

	$userid = $_POST['workspace-adduser'];
	$wsid = $_POST['workspace-id'];
	$userid = validate($userid);
	$wsid = validate($wsid);
	$username = $_SESSION['username'];
	
	//~~JOSH~~
	//Need checking that the user really has this workspace
	//Prevents client-side editing of wsid to access those of other orgs
	//@Tom
	require('../php/connect.php');
	$query = "INSERT INTO user_workspace_mapping (workspace, user) VALUES ('$wsid', '$userid')";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	$fmsg = "Successfully Added User to Workspace!";

}

//Handle Workspace Deletion
if(isset($_POST['workspace-delete'])){

	if($_POST['workspace-delete'] == 'yes'){

		$wsid = $_POST['workspace-id'];
		$wsid = validate($wsid);
		$username = $_SESSION['username'];
		
		//~~JOSH~~
		//Need checking that the user really has this workspace
		//Prevents client-side editing of wsid to access those of other orgs
		//@Tom
		require('../php/connect.php');
		$query = "DELETE FROM workspaces WHERE id = '$wsid'";
		$result = mysqli_query($link, $query);
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}
		$query = "DELETE FROM user_workspace_mapping WHERE workspace = '$wsid'";
		$result = mysqli_query($link, $query);
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}

		$fmsg = "Successfully Deleted Workspace!";

	}
	else{
		$fmsg = "Failed to Confirm Workspace Deletion!";
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
				    <a class="nav-link active" href="organization.php">My Organization</a>
				  </ul>
				  </div>
				</nav>
			</div>
			<div id="pageBody">
			<div class="row">
			<div class="col-sm-12">
				<?php if(isset($fmsg)){ echo "<div class='card'><p>" . $fmsg . "</p></div>"; } ?>
				<h1>My Organization</h1>
				<p>This is a placeholder page for your organization management page.</p>
				<hr>
				<b>Name : </b><?php 
require('../php/connect.php');
$username = $_SESSION['username'];
$query = "SELECT organizations.name FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
$result = mysqli_query($link, $query);
if (!$result){
	die('Error: ' . mysqli_error($link));
}
list($name) = mysqli_fetch_array($result);
echo $name;
				 ?>		<br>
				<b>ID : </b><?php 
require('../php/connect.php');
$username = $_SESSION['username'];
$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
$result = mysqli_query($link, $query);
if (!$result){
	die('Error: ' . mysqli_error($link));
}
list($orgid) = mysqli_fetch_array($result);
echo $orgid;
				 ?>		<br>
				<small>This is the unique identifier used by ProjeX to identify your organization</small><br>
				<b>Join Code : </b><?php 
require('../php/connect.php');
$username = $_SESSION['username'];
$query = "SELECT organizations.code FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
$result = mysqli_query($link, $query);
if (!$result){
	die('Error: ' . mysqli_error($link));
}
list($orgcode) = mysqli_fetch_array($result);
echo $orgcode;
				 ?>	<br>
				<small>This is the unique identifier other users will need to join your organization</small><br>
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
				<form method="POST" class="pt-4">
				  <div class="form-row">
				    <div class="form-group col-md-6">
				      <label for="edit-name">Change Organization Name</label>
				      <input type="text" class="form-control" id="edit-name" name="edit-name" placeholder="New Name...">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
<?php
}
?>
				<br>
				<h1>My Workspaces</h1>
				<p>Edit the workspaces within this organization.</p>
				<hr>
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
	        	<div class="row">
	        	<div class="col-sm-6">
	        	<h4><?php echo $workspaceName ?> (ID#<?php echo $workspaceID ?>)</h4>
	        	<b>Users</b>
		        	<?php

		        	require('../php/connect.php');

					$query = "SELECT users.firstname, users.lastname, users.id FROM (user_workspace_mapping INNER JOIN users ON user_workspace_mapping.user = users.id) WHERE user_workspace_mapping.workspace = '$workspaceID'";
					$result2 = mysqli_query($link, $query);
					if (!$result2){
						die('Error: ' . mysqli_error($link));
					}
					while($userArray = mysqli_fetch_array($result2)){
					$thisFirstname = $userArray['firstname'];
					$thisLastname = $userArray['lastname'];
					$thisID = $userArray['id'];
		        	?>
					<form method="POST" class="pt-1">
					  <div class="form-row">
					    <div class="form-group col-md-8" style="margin-bottom:0;">
					      	<label><?php echo $thisFirstname . " " . $thisLastname . " (#" . $thisID . ")"; ?></label>
					      	<input type="hidden" name="workspace-userid" value="<?php echo $thisID; ?>">
					      	<input type="hidden" name="workspace-id" value="<?php echo $workspaceID; ?>">
					    </div>
					    <div class="form-group col-md-4" style="margin-bottom:0;">
					    	<?php if($thisID != $_SESSION['id']){ ?>
					    	<button style="padding-top:0;" type="submit" class="btn btn-link text-danger">Remove</button>
					    	<?php } ?>
					    </div>
					  </div>
					</form>
					<?php } ?>
				<form method="POST" class="pt-1">
				  <div class="form-row">
				    <div class="form-group col-md-8" style="margin-bottom:0;">
				      	<input type="hidden" name="workspace-id" value="<?php echo $workspaceID; ?>">
				      	<select name="workspace-adduser" class="form-control">
						  <?php

				        	require('../php/connect.php');

				        	$username = $_SESSION['username'];

				        	//Start by getting organization ID of user
							$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
							$result2 = mysqli_query($link, $query);
							if (!$result2){
								die('Error: ' . mysqli_error($link));
							}
							list($orgid) = mysqli_fetch_array($result2);

							//Next grab firstname, lastname, and id of users in same organization
							$query = "SELECT a.id, a.firstname, a.lastname FROM users a, user_organization_mapping b WHERE a.id = b.user AND b.organization = '$orgid' AND a.id NOT IN (SELECT user FROM user_workspace_mapping WHERE workspace = '$workspaceID')";
							$result2 = mysqli_query($link, $query);
							if (!$result2){
								die('Error: ' . mysqli_error($link));
							}
							while($userArray = mysqli_fetch_array($result2)){
							$thisFirstname = $userArray['firstname'];
							$thisLastname = $userArray['lastname'];
							$thisID = $userArray['id'];

				        	?>
				        	<option value="<?php echo $thisID; ?>"><?php echo $thisFirstname . " " . $thisLastname . " (#" . $thisID . ")"; ?></option>
				        	<?php } ?>
						</select>
				    </div>
				    <div class="form-group col-md-4" style="margin-bottom:0;">
				    	<button type="submit" class="btn btn-link">Add</button>
				    </div>
				  </div>
				</form>
				</div>
				<div class="col-sm-6">
				<form method="POST" class="pt-2">
				  <div class="form-row">
				  	<div class="col-12">
				  	<label for="edit-name">Change Workspace Name</label>
				  	</div>
				    <div class="form-group col-md-9">
				      <input type="hidden" name="workspace-id" value="<?php echo $workspaceID; ?>">
				      <input type="text" class="form-control" name="workspace-name" value="<?php echo $workspaceName; ?>">
				    </div>
				    <div class="form-group col-md-3">
				      <button type="submit" class="btn btn-link">Change</button>
				    </div>
				  </div>
				</form>
				<form method="POST" class="pt-2">
				  <div class="form-row">
				  	<div class="col-12">
				  	<label>Delete Workspace</label>
				  	</div>
				    <div class="form-group col-md-9">
				      <input type="hidden" name="workspace-id" value="<?php echo $workspaceID; ?>">
				      <select class="form-control" name="workspace-delete">
				      	<option value="no">No</option>
				      	<option value="yes">Yes</option>
				      </select>
				    </div>
				    <div class="form-group col-md-3">
				      <button type="submit" class="btn btn-link text-danger">Delete</button>
				    </div>
				  </div>
				</form>
				</div>
				</div>
				<hr><br>
				<?php } ?>
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