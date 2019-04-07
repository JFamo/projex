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

function fullnameFromID($userid){
	require('../php/connect.php');
	$query = "SELECT users.firstname, users.lastname FROM users WHERE id='$userid'";
  	$result3 = mysqli_query($link, $query);
  	if (!$result3){
    	die('Error: ' . mysqli_error($link));
  	}
  	list($userfirst, $userlast) = mysqli_fetch_array($result3);
  	return $userfirst . " " . $userlast;
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

//Handle Changing Projects
if(isset($_POST['project-id'])){

  $newproject = $_POST['project-id'];
  //~~JOSH~~
  //Need checking that the user really has this project here
  //Prevents client-side editing of project value to access those of other orgs
  //@Tom



  require('../php/connect.php');
  $userID = $_SESSION['id'];
  $query="SELECT * FROM user_project_mapping WHERE project='$newproject' AND user='$userID'";
  $result = mysqli_query($link, $query);
  if (!$result){
  	die('Error: ' . mysqli_error($link));
  }
  if($result){
  	$_SESSION['project'] = $newproject;
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
    <script src="../js/Chart.js" type="text/javascript"></script>
	
	<title>
		ProjeX
	</title>

	<!-- Import our CSS -->
	<link href="../css/main.css" rel="stylesheet" type="text/css" />

	<!-- Mobile metas -->
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
				    <a class="nav-link active" href="metrics.php">Metrics</a>
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
			<div class="col-12">
	            <?php if(isset($fmsg)){ echo "<div class='card'><p>" . $fmsg . "</p></div>"; } ?>
	            <h1>Metrics</h1>  
	          </div>
			<div class="col-sm-12">
			<center><h3>Burndown</h3><small>Compares target value to be completed with actual value completed</small></center>
			<canvas id="chartjs-1" class="chartjs" width="883" height="441" style="display: block; height: 294px; width: 589px;"></canvas>
				<script>
				new Chart(document.getElementById("chartjs-1"),{"type":"bar","data":{"labels":
					[
						<?php
		                require('../php/connect.php');
		                $username = $_SESSION['username'];
		                $workspace = $_SESSION['workspace'];
		                $query = "SELECT projects.name, projects.id FROM ((user_project_mapping INNER JOIN projects ON projects.id = user_project_mapping.project) INNER JOIN users ON user_project_mapping.user = users.id) WHERE users.username = '$username' AND projects.workspace = '$workspace'";
		                $result = mysqli_query($link, $query);
		                if (!$result){
		                  die('Error: ' . mysqli_error($link));
		                }
		                while($resultArray = mysqli_fetch_array($result)){
		                	$projectName = $resultArray['name'];
		                	echo '"' . $projectName . '", ';
		            	}
		                ?>
					"All"],
					"datasets":[
					{"label":"Target","data":[
						<?php
			            require('../php/connect.php');
			            $username = $_SESSION['username'];
		                $workspace = $_SESSION['workspace'];
		                $query = "SELECT projects.id FROM ((user_project_mapping INNER JOIN projects ON projects.id = user_project_mapping.project) INNER JOIN users ON user_project_mapping.user = users.id) WHERE users.username = '$username' AND projects.workspace = '$workspace'";
		                $result = mysqli_query($link, $query);
		                if (!$result){
		                  die('Error: ' . mysqli_error($link));
		                }
		                while($resultArray = mysqli_fetch_array($result)){
		                	$activeProject = $resultArray['id'];

				            $query = "SELECT SUM(value) FROM goals WHERE goals.project = '$activeProject'";
				            $result2 = mysqli_query($link, $query);
				            if (!$result2){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result2);
				            echo $valuesum . ", ";

				        }
				        $query = "SELECT SUM(value) FROM goals";
				            $result2 = mysqli_query($link, $query);
				            if (!$result2){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result2);
				            echo $valuesum;
			          ?>
					],"fill":false,"backgroundColor":["rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)","rgba(255, 0, 0, 0.2)"],"borderColor":["rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)","rgb(255, 0, 0)"],"borderWidth":1},
					{"label":"Complete","data":[
						<?php
			            require('../php/connect.php');
			            $username = $_SESSION['username'];
		                $workspace = $_SESSION['workspace'];
		                $query = "SELECT projects.id FROM ((user_project_mapping INNER JOIN projects ON projects.id = user_project_mapping.project) INNER JOIN users ON user_project_mapping.user = users.id) WHERE users.username = '$username' AND projects.workspace = '$workspace'";
		                $result = mysqli_query($link, $query);
		                if (!$result){
		                  die('Error: ' . mysqli_error($link));
		                }
		                while($resultArray = mysqli_fetch_array($result)){
		                	$activeProject = $resultArray['id'];

				            $query = "SELECT IFNULL(SUM(value),0) FROM goals WHERE goals.project = '$activeProject' AND goals.status='complete'";
				            $result2 = mysqli_query($link, $query);
				            if (!$result2){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result2);
				            echo $valuesum . ", ";

				        }
				        $query = "SELECT SUM(value) FROM goals WHERE goals.status='complete'";
				            $result2 = mysqli_query($link, $query);
				            if (!$result2){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result2);
				            echo $valuesum;
			          ?>
					],"fill":false,"backgroundColor":["rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)","rgba(0, 0, 255, 0.2)"],"borderColor":["rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)","rgb(0, 0, 255)"],"borderWidth":1}
					]},
					"options":{"scales":{"yAxes":[{"ticks":{"beginAtZero":true}}]}}});
				</script>
			<center><h3>Status Breakdown</h3><small>Shows value point distribution by status</small>
<div class="dropdown">
              <div class="btn-group">
                <button type="button" class="btn btn-secondary"><?php 
                  require('../php/connect.php');
                  $project = $_SESSION['project'];
                  $query = "SELECT name FROM projects WHERE id='$project'";
                  $result = mysqli_query($link, $query);
                  if (!$result){
                    die('Error: ' . mysqli_error($link));
                  }
                  list($name) = mysqli_fetch_array($result);
                  if($_SESSION['project'] == null || $_SESSION['workspace'] == null){
                    echo "Select a Project";
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
                $workspace = $_SESSION['workspace'];
                $query = "SELECT projects.name, projects.id FROM ((user_project_mapping INNER JOIN projects ON projects.id = user_project_mapping.project) INNER JOIN users ON user_project_mapping.user = users.id) WHERE users.username = '$username' AND projects.workspace = '$workspace'";
                $result = mysqli_query($link, $query);
                if (!$result){
                  die('Error: ' . mysqli_error($link));
                }
                while($resultArray = mysqli_fetch_array($result)){
                $projectName = $resultArray['name'];
                $projectID = $resultArray['id'];

                ?>
                <form method="POST"><input type="hidden" value="<?php echo $projectID; ?>" name="project-id"/><input class="dropdown-item <?php if($_SESSION['project'] == $projectID){ echo 'active-dropdown'; } ?>" type="submit" value="<?php echo $projectName; ?>"></form>
                <?php } ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="project.php">Create New</a>
              </div>
            </div>
            </div>
			</center>
			<canvas id="chartjs-4" class="chartjs" width="883" height="441" style="display: block; height: 294px; width: 589px;"></canvas>
				<script>
				new Chart(document.getElementById("chartjs-4"),{"type":"doughnut","data":{"labels":["Backlog","Active","Complete"],"datasets":[{"label":"Breakdown Values","data":[
					<?php
					require('../php/connect.php');
					$activeProject = $_SESSION['project'];
					$query = "SELECT IFNULL(SUM(value),0) FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'";
				            $result = mysqli_query($link, $query);
				            if (!$result){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result);
				            echo $valuesum . ", ";
				    $query = "SELECT IFNULL(SUM(value),0) FROM goals WHERE goals.project = '$activeProject' AND goals.status='active'";
				            $result = mysqli_query($link, $query);
				            if (!$result){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result);
				            echo $valuesum . ", ";
				    $query = "SELECT IFNULL(SUM(value),0) FROM goals WHERE goals.project = '$activeProject' AND goals.status='complete'";
				            $result = mysqli_query($link, $query);
				            if (!$result){
				              die('Error: ' . mysqli_error($link));
				            }
				            list($valuesum) = mysqli_fetch_array($result);
				            echo $valuesum . ", ";
					?>
					],"backgroundColor":["rgb(255, 99, 132)","rgb(54, 162, 235)","rgb(255, 205, 86)"]}]}});
				</script>


				<center><h3>Contribution Breakdown</h3><small>Shows task distribution by user</small>
<div class="dropdown">
              <div class="btn-group">
                <button type="button" class="btn btn-secondary"><?php 
                  require('../php/connect.php');
                  $project = $_SESSION['project'];
                  $query = "SELECT name FROM projects WHERE id='$project'";
                  $result = mysqli_query($link, $query);
                  if (!$result){
                    die('Error: ' . mysqli_error($link));
                  }
                  list($name) = mysqli_fetch_array($result);
                  if($_SESSION['project'] == null || $_SESSION['workspace'] == null){
                    echo "Select a Project";
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
                $workspace = $_SESSION['workspace'];
                $query = "SELECT projects.name, projects.id FROM ((user_project_mapping INNER JOIN projects ON projects.id = user_project_mapping.project) INNER JOIN users ON user_project_mapping.user = users.id) WHERE users.username = '$username' AND projects.workspace = '$workspace'";
                $result = mysqli_query($link, $query);
                if (!$result){
                  die('Error: ' . mysqli_error($link));
                }
                while($resultArray = mysqli_fetch_array($result)){
                $projectName = $resultArray['name'];
                $projectID = $resultArray['id'];

                ?>
                <form method="POST"><input type="hidden" value="<?php echo $projectID; ?>" name="project-id"/><input class="dropdown-item <?php if($_SESSION['project'] == $projectID){ echo 'active-dropdown'; } ?>" type="submit" value="<?php echo $projectName; ?>"></form>
                <?php } ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="project.php">Create New</a>
              </div>
            </div>
            </div>
			</center>
			<canvas id="chartjs-5" class="chartjs" width="883" height="441" style="display: block; height: 294px; width: 589px;"></canvas>
				<script>
				new Chart(document.getElementById("chartjs-5"),{"type":"doughnut","data":{"labels":[
					<?php
						//Give each user as a label
						require('../php/connect.php');
						$activeProject = $_SESSION['project'];
						$query = "SELECT user FROM user_project_mapping WHERE project='$activeProject'";
				        $result = mysqli_query($link, $query);
				        if (!$result){
				            die('Error: ' . mysqli_error($link));
				        }
				        while(list($userid) = mysqli_fetch_array($result)){
				        	echo '"' . fullnameFromID($userid) . '", ';
				    	}
					?>
					],"datasets":[{"label":"Breakdown Values","data":[
					<?php
					$activeProject = $_SESSION['project'];
						//Iterate each user to look for their goals
						$query = "SELECT user FROM user_project_mapping WHERE project='$activeProject'";
				        $result = mysqli_query($link, $query);
				        if (!$result){
				            die('Error: ' . mysqli_error($link));
				        }
				        while(list($userid) = mysqli_fetch_array($result)){
				        	$query = "SELECT * FROM user_task_mapping WHERE user='$userid'";
					        $result2 = mysqli_query($link, $query);
					        if (!$result2){
					            die('Error: ' . mysqli_error($link));
					        }
					        echo mysqli_num_rows($result2) . ", ";
				        }
					?>
					],"backgroundColor":["rgb(255, 99, 132)","rgb(54, 162, 235)","rgb(255, 205, 86)"]}]}});
				</script>
				
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

<script src="../js/scripts.js" type="text/javascript"></script>


</html>

<?php 

}

?>