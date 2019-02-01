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
				   <a class="navbar-brand icon" href="#"><img src="../imgs/workspacePlaceholder.png" alt="icon" width="60" height="60">Projex</a>
				   <div class="dropdown">
              <div class="btn-group dropright">
                <button type="button" class="btn btn-secondary"><?php 
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
                ?></button>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
              <div class="dropdown-menu dropdown-menu-right">

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
            </div>
            </div>
			        <hr class="sidenavHR">
			        <a class="nav-link active" href="main.php">Dashboard</a>
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
				<div class="col-md-8">
					<h1>Welcome, <?php echo $_SESSION['firstname']; ?></h1>
					<p>Here are some useful links to effectively navigate <b class="color-primary">Projex</b></p>
					<div class="card">
						<p>Projex is a comprehensive suite of tools designed to provide project-management utility to all areas of industry. It is based on the principles of Agile software development and implements artifacts of many popular project management frameworks. For detailed help using Projex, refer to the documentation linked below.</p>
						<br>
						<a href="http://agilemanifesto.org/">The Agile Manifesto</a>
						<a href="#">More About Projex</a>
					</div>
					<div class="card">
					<h5>You are a member of organization <b><?php 
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT name FROM organizations WHERE id IN (SELECT organization FROM user_organization_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        while(list($taskname) = mysqli_fetch_array($result2)){
					        	echo $taskname;
					        }
					?></b></h5>
					<p>Your organization has 
						<?php 
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT * FROM user_organization_mapping WHERE organization IN (SELECT organization FROM user_organization_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        echo mysqli_num_rows($result2);
					?>
					members</p>
					<p>Your organization's join code is  
						<?php 
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT code FROM organizations WHERE id IN (SELECT organization FROM user_organization_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					       	list($orgcode) = mysqli_fetch_array($result2);
					       	echo $orgcode;
					?></p>
					<br>
					<a href="organization.php">Manage My Organization</a>
					</div>
					<div class="card">
					<h5>Your username is <b><?php echo $_SESSION['username']; ?></b></h5>
					<p>Your full name is <?php echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
					<br>
					<a href="account.php">Manage My Account</a>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card">
						<h4>My Tasks</h4>
						<a href="active.php">Active Tasks</a>
						<hr>
						<?php
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT name FROM tasks WHERE id IN (SELECT task FROM user_task_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        while(list($taskname) = mysqli_fetch_array($result2)){
					        	echo "<p>" . $taskname . "</p>";
					        }

					    ?>
					</div>
					<div class="card">
						<h4>My Workspaces</h4>
						<hr>
						<?php
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT name FROM workspaces WHERE id IN (SELECT workspace FROM user_workspace_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        while(list($taskname) = mysqli_fetch_array($result2)){
					        	echo "<p>" . $taskname . "</p>";
					        }

					    ?>
					</div>
					<div class="card">
						<h4>My Projects</h4>
						<hr>
						<?php
						require('../php/connect.php');
						$myid = $_SESSION['id'];
						$query = "SELECT name FROM projects WHERE id IN (SELECT project FROM user_project_mapping WHERE user='$myid')";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        while(list($taskname) = mysqli_fetch_array($result2)){
					        	echo "<p>" . $taskname . "</p>";
					        }

					    ?>
					</div>
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
</body>

<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>