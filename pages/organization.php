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
	        	<h4><?php echo $workspaceName ?> (ID#<?php echo $workspaceID ?>)</h4>
	        	<div class="row">
	        	<div class="col-sm-6">
				<form method="POST" class="pt-2">
				  <div class="form-row">
				    <div class="form-group col-md-12">
				      <label for="edit-name">Change Workspace Name</label>
				      <input type="text" class="form-control" name="edit-name" placeholder="New Name...">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
				</div>
				<div class="col-sm-6">
				<form method="POST" class="pt-2">
				  <div class="form-row">
				    <div class="form-group col-md-12">
				      <label for="edit-name">Change Workspace Name</label>
				      <input type="hidden" name="workspace-id" value="<?php echo $workspaceID; ?>">
				      <input type="text" class="form-control" name="workspace-name" value="<?php echo $workspaceName; ?>">
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Change</button>
				</form>
				</div>
				</div>
				<br>
				<?php } ?>
				<a href="../index.php">Return to Dashboard</a>
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