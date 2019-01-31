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

$fmsg = "";
//Handle New Workspace Creation
if(isset($_POST['workspace-name'])){

	$wsname = $_POST['workspace-name'];
	$wsname = validate($wsname);
	$username = $_SESSION['username'];
	$myid = $_SESSION['id'];
	
	require('../php/connect.php');

	//Start by getting organization ID of user
	$query = "SELECT organizations.id FROM ((user_organization_mapping INNER JOIN organizations ON organizations.id = user_organization_mapping.organization) INNER JOIN users ON user_organization_mapping.user = users.id) WHERE users.username = '$username'";
	$result = mysqli_query($link, $query);
	if (!$result){
		die('Error: ' . mysqli_error($link));
	}
	list($orgid) = mysqli_fetch_array($result);

	//Next create the workspace itself
	$query2 = "INSERT INTO workspaces (organization, name) VALUES ('$orgid', '$wsname')";
	$result2 = mysqli_query($link, $query2);
	if (!$result2){
		die('Error: ' . mysqli_error($link));
	}
	$wsid = mysqli_insert_id($link); //Save the AI workspace ID

	//Finally add the checked users to the workspace
	//Force org owner into workspace
	$query2 = "INSERT INTO user_workspace_mapping (workspace, user) VALUES ('$wsid', '$myid')";
	$result2 = mysqli_query($link, $query2);
	if (!$result2){
		die('Error: ' . mysqli_error($link));
	}
	//Iterate other users
	if(!empty($_POST['workspace-user'])){
		foreach($_POST['workspace-user'] as $userID){
			$query2 = "INSERT INTO user_workspace_mapping (workspace, user) VALUES ('$wsid', '$userID')";
			$result2 = mysqli_query($link, $query2);
			if (!$result2){
				die('Error: ' . mysqli_error($link));
			}
		}
	}

	$fmsg = "Successfully Created Workspace!";

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
				<h1>Create Workspace</h1>
				<p>This is a placeholder page to create a new workspace.</p>
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
				      <label for="edit-name">Workspace Name</label>
				      <input type="text" pattern="[a-zA-Z0-9 ]+" class="form-control" id="workspace-name" name="workspace-name" placeholder="New Workspace Name...">
				    </div>
				  </div>
				  <div class="form-row">
				  	<div class="form-group col-md-6">
				      <label for="edit-name">Add Users to Workspace</label><br>
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

			          		<input type="checkbox" <?php if($thisID != $_SESSION['id']){ echo "name='workspace-user[]'"; } ?> value="<?php echo $thisID; ?>" <?php if($thisID == $_SESSION['id']){ echo "checked='checked' onclick='return false;'"; } ?>> <?php echo $thisFirstname . " " . $thisLastname . " (" . $thisID . ")"; ?><br>

			          <?php } ?>
				    </div>
				  </div>
				  <button type="submit" class="btn btn-primary">Create</button>
				</form>
<?php
}
?>
				<br>
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