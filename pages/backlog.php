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
           <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Workspaces
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">

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
                <form method="POST"><input type="hidden" value="<?php echo $workspaceID; ?>" name="workspace-id"/><input class="dropdown-item" type="submit" value="<?php echo $workspaceName; ?>"></form>
                <?php } ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="workspace.php">Create New</a>
              </div>
              <hr class="sidenavHR">
              <a class="nav-link active" href="main.php">Dashboard</a>
            <a class="nav-link" href="metrics.php">Metrics</a>
            <a class="nav-link" href="backlog.php">Backlog</a>
            <a class="nav-link" href="active.php">Active</a>
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
        <div class="col-12">
        	<form method="get" class="backlog-form">
        		<div class="backlog-form">
        			<label for="task">Enter the task:</label>
        			<input type="text" task="task" id="task" required>
        		</div>
        		<div class="backlog-form">
        			<label for="task-desc">Enter the task's description:</label>
        			<input type="task-desc" task="task-desc" id="task-desc" required>
        		</div>
        		<div class="backlog-form">
        			<input type="submit"
        			value="Submit">
        		</div>
        	</div>
        </form>
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