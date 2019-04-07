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
  if(mysqli_num_rows($result)>=1){
    $_SESSION['project'] = $newproject;
  }

}

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

//Create a Task
if(isset($_POST['task-name'])){

  $taskname = validate($_POST['task-name']);
  $taskdesc = validate($_POST['task-desc']);
  $taskgoal = $_POST['task-goal'];
  $thisuser = $_SESSION['id'];

  require('../php/connect.php');
  $query = "INSERT INTO tasks (name, description, creator, date) VALUES ('$taskname','$taskdesc','$thisuser',now())";
  $result = mysqli_query($link,$query);
  $taskid = mysqli_insert_id($link);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  $query = "INSERT INTO goal_task_mapping (task, goal) VALUES ('$taskid','$taskgoal')";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);

  $fmsg = "Successfully Created Task!";

}

//Create a Goal
if(isset($_POST['goal-name'])){

  $goalname = validate($_POST['goal-name']);
  $goalvalue = validate($_POST['goal-value']);
  $thisproject = $_SESSION['project'];
  $thisuser = $_SESSION['id'];

  require('../php/connect.php');
  $query = "INSERT INTO goals (name, creator, date, project, value) VALUES ('$goalname','$thisuser',now(),'$thisproject','$goalvalue')";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);

  $fmsg = "Successfully Created Goal!";

}

//Move Goals to Active
if(isset($_POST['goal-id'])){

  $goalid = $_POST['goal-id'];

  require('../php/connect.php');
  $query = "UPDATE goals SET status='active' WHERE id='$goalid'";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  $query = "UPDATE tasks SET status='active' WHERE id IN (SELECT task FROM goal_task_mapping WHERE goal = '$goalid')";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  $query = "UPDATE tasks SET changedate= now() WHERE id IN (SELECT task FROM goal_task_mapping WHERE goal = '$goalid')";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);

  $fmsg = "Moved Goal to Active!";
}

//Edit Task Name from Modal
if(isset($_POST['edit-task-name'])){

  $task = validate($_POST['edit-task-id']);
  $newname = validate($_POST['edit-task-name']);

  require('../php/connect.php');
  $query = "UPDATE tasks SET name='$newname' WHERE id='$task'";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);

  $fmsg = "Updated Task Name!";
}

//Edit Task Desc from Modal
if(isset($_POST['edit-task-desc'])){

  $task = $_POST['edit-task-id'];
  $newdesc = $_POST['edit-task-desc'];

  require('../php/connect.php');
  $query = "UPDATE tasks SET description='$newdesc' WHERE id='$task'";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);

  $fmsg = "Updated Task Description!";
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
            <a class="nav-link active" href="backlog.php">Backlog</a>
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
      		<h1>Backlog</h1>	
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
        <div class="card" style="width:100%;">
        <h3>Backlog Overview</h3>
          <div class="row">
          <div class="col-md-6">
          <?php if($_SESSION['project'] == null){
            echo "<p>No Project Selected</p>";
          }
          else { ?>
          <p>Number of Goals : <?php
            require('../php/connect.php');

            $activeProject = $_SESSION['project'];

            $query = "SELECT goals.id FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'";
            $result = mysqli_query($link, $query);
            if (!$result){
              die('Error: ' . mysqli_error($link));
            }
            echo mysqli_num_rows($result);
          ?>
          </p><br>
          <p>Number of Tasks : <?php
            require('../php/connect.php');

            $activeProject = $_SESSION['project'];

            $query = "SELECT tasks.id FROM tasks WHERE tasks.id IN (SELECT task FROM goal_task_mapping WHERE goal IN (SELECT goals.id FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'))";
            $result = mysqli_query($link, $query);
            if (!$result){
              die('Error: ' . mysqli_error($link));
            }
            echo mysqli_num_rows($result);
          ?>
          </p>
          <br><p>Total Value : <?php
            require('../php/connect.php');

            $activeProject = $_SESSION['project'];

            $query = "SELECT SUM(value) FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'";
            $result = mysqli_query($link, $query);
            if (!$result){
              die('Error: ' . mysqli_error($link));
            }
            list($valuesum) = mysqli_fetch_array($result);
            echo $valuesum;
          ?>
          </p>
          </div>
          <div class="col-md-6">
          <button class="btn btn-link" data-toggle="modal" data-target="#taskCreationModal">Create Task</button>
          <br>
          <button class="btn btn-link" data-toggle="modal" data-target="#goalCreationModal">Create Goal</button>
          <?php } ?>
          </div>
          </div>
        </div>

          <?php

            //Iterate Goals

            require('../php/connect.php');

            $username = $_SESSION['username'];
            $activeProject = $_SESSION['project'];

            $query = "SELECT goals.name, goals.id, goals.value FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'";
            $result = mysqli_query($link, $query);
            if (!$result){
              die('Error: ' . mysqli_error($link));
            }
            while($resultArray = mysqli_fetch_array($result)){
            $goalName = $resultArray['name'];
            $goalID = $resultArray['id'];
            $goalValue = $resultArray['value'];

          ?>
          <div class="head">
            <h4 style=" float:left;"><?php echo $goalName; ?></h4><h4 style="float:right;"><?php echo $goalValue; ?></h4>
          <form method="post">
            <input type="hidden" value="<?php echo $goalID; ?>" name="goal-id" />
            <input type="submit" class="btn btn-link" value="Move to Active">
          </form>
            <?php

            //Iterate Tasks

            require('../php/connect.php');

            $username = $_SESSION['username'];
            $activeProject = $_SESSION['project'];

            $query = "SELECT tasks.id, tasks.name, tasks.description, tasks.creator, tasks.date FROM tasks WHERE tasks.id IN (SELECT task FROM goal_task_mapping WHERE goal = '$goalID') AND tasks.status='backlog'";
            $result2 = mysqli_query($link, $query);
            if (!$result2){
              die('Error: ' . mysqli_error($link));
            }
            while($taskArray = mysqli_fetch_array($result2)){
            $taskName = $taskArray['name'];
            $taskID = $taskArray['id'];
            $taskDesc = $taskArray['description'];
            $taskCreator = $taskArray['creator'];
            $taskDate = $taskArray['date'];

          ?>

          <form method="post" id="select-task-form-<?php echo $taskID;?>">
            <input type="hidden" name="selected-task" value="<?php echo $taskID; ?>"/>
          </form>

          <div class="card activecard" style="cursor:pointer;" onclick="document.getElementById('select-task-form-<?php echo $taskID;?>').submit();">
            <h4><?php echo $taskName; ?></h4>
            <small><?php echo $taskDesc; ?></small>
          </div>
          <?php
          }
          ?>
          </div>
          <?php
          }
          ?>
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

  <!-- Task Creation Modal -->
  <div class="modal fade" id="taskCreationModal" tabindex="-1" role="dialog" aria-labelledby="taskCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="taskCreationModalLabel">Create A Task</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <form method="POST" class="">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="task-name">Task Name</label>
                <input type="text" maxlength="90" class="form-control" id="task-name" name="task-name" placeholder="Enter a task name">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="task-desc">Task Description</label>
                <textarea maxlength="450" type="text" class="form-control" id="task-desc" name="task-desc" placeholder="Enter the task's description"></textarea>
            </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="task-goal">Add to Goal</label>
                <select class="form-control" id="task-goal" name="task-goal">
                <?php
                require('../php/connect.php');

                $activeProject = $_SESSION['project'];

                $query = "SELECT goals.id, goals.name FROM goals WHERE goals.project = '$activeProject' AND goals.status='backlog'";
                $result = mysqli_query($link, $query);
                if (!$result){
                  die('Error: ' . mysqli_error($link));
                }
                while(list($goalID,$goalName) = mysqli_fetch_array($result)){
                  echo '<option value="' . $goalID . '">'. $goalName . "</option>";
                }
                ?>
                </select>
            </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Goal Creation Modal -->
  <div class="modal fade" id="goalCreationModal" tabindex="-1" role="dialog" aria-labelledby="goalCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="goalCreationModalLabel">Create A Goal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" class="pt-4">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="goal-name">Goal Name</label>
                <input type="text" maxlength="90" class="form-control" id="goal-name" name="goal-name" placeholder="Enter a goal name">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="goal-value">Goal Value</label>
                <br>
                <small>An integer representing the team's relative weighted value of completing this goal.</small>
                <br>
                <input type="number" class="form-control" name="goal-value" id="goal-value" value="1" />
            </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="taskModalLabel">Task</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="taskModalBody">
        
          <?php

            //Iterate Tasks

            require('../php/connect.php');

            $activeTask = $_SESSION['task'];

            $query = "SELECT tasks.name, tasks.description, tasks.creator, tasks.date FROM tasks WHERE tasks.id = '$activeTask'";
            $result2 = mysqli_query($link, $query);
            if (!$result2){
              die('Error: ' . mysqli_error($link));
            }
            while($taskArray = mysqli_fetch_array($result2)){
            $taskName = $taskArray['name'];
            $taskID = $taskArray['id'];
            $taskDesc = $taskArray['description'];
            $taskCreator = $taskArray['creator'];
            $taskDate = $taskArray['date'];

          ?>
          <div class="card">
            <h4><?php echo $taskName; ?></h4>
            <hr>
            <p><?php echo $taskDesc; ?></p>
            <br>
            <small>Created By : <?php
              require('../php/connect.php');
              $query = "SELECT firstname, lastname FROM users WHERE id = '$taskCreator'";
              $result3 = mysqli_query($link, $query);
              if (!$result3){
                die('Error: ' . mysqli_error($link));
              }
              list($firstname, $lastname) = mysqli_fetch_array($result3);
              echo $firstname . " " . $lastname;
            ?> on <?php echo $taskDate; ?></small>
          </div>
          <?php
          }
          ?>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="closeTaskModalButton">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php
  if(isset($_POST['selected-task'])){
    
    $_SESSION['task'] = $_POST['selected-task'];
    $_POST['selected-task'] = null;
    $fmsg = "Selected task " . $_SESSION['task'];
    ?>

    <script> 
    $("#taskModalBody").load("../php/taskModal.php"); 
    document.getElementById('closeTaskModalButton').onclick = function(){ $("#taskModal").modal('hide'); };
    </script>

    <?php
  }
  ?>

</body>

<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>