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
//Moving goals to active
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
  $query = "DELETE FROM task_ratings WHERE task IN (SELECT task FROM goal_task_mapping WHERE goal = '$goalid')";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);
  $fmsg = "Moved Goal to Active!";
}
//Updating Ratings
if(isset($_POST['task-id'])){
  $taskID = $_POST['task-id'];
  $rating = $_POST['rating-value'];
  require('../php/connect.php');
  $query = "INSERT INTO task_ratings (task, rating) VALUES ('$taskID', '$rating') ON DUPLICATE KEY UPDATE rating='$rating'";
  $result = mysqli_query($link,$query);
  if (!$result){
      die('Error: ' . mysqli_error($link));
  }
  mysqli_close($link);
  $fmsg = "Rating Updated!";
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
            <a class="nav-link active" href="complete.php">Complete</a>
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
            <h1>Complete</h1>  
          </div>
          <div class="col-12">
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
          <br>
          <hr>
          <br>
          <?php
            require('../php/connect.php');
            $username = $_SESSION['username'];
            $activeProject = $_SESSION['project'];
            $query = "SELECT goals.name, goals.id, goals.value FROM goals WHERE goals.project = '$activeProject' AND goals.status='complete'";
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
            <input type="submit" class="btn btn-link" value="Return to Active">
          </form>
            <?php
            require('../php/connect.php');
            $username = $_SESSION['username'];
            $activeProject = $_SESSION['project'];
            $query = "SELECT tasks.id, tasks.name, tasks.description, tasks.creator, tasks.date FROM tasks WHERE tasks.id IN (SELECT task FROM goal_task_mapping WHERE goal = '$goalID') AND tasks.status='complete'";
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
            <h4><?php echo $taskName;
              echo " (";
              $query = "SELECT users.firstname, users.lastname FROM users WHERE users.id IN (SELECT user FROM user_task_mapping WHERE task='$taskID')";
              $result3 = mysqli_query($link, $query);
              if (!$result3){
                die('Error: ' . mysqli_error($link));
              }
              list($userfirst, $userlast) = mysqli_fetch_array($result3);
              echo $userfirst;
              echo " ";
              echo $userlast;
              echo ")";
             ?></h4>
            <hr>
            <p><?php echo $taskDesc; ?></p>
            
            <?php
              //Grab and save this task's rating
              require('../php/connect.php');
              $query = "SELECT rating FROM task_ratings WHERE task = '$taskID'";
              $result4 = mysqli_query($link, $query);
              if (!$result4){
                die('Error: ' . mysqli_error($link));
              }
              list($thisRating) = mysqli_fetch_array($result4);
            ?>

            <form method="post">
              Rating: <input type="hidden" value="<?php echo $taskID; ?>" name="task-id" />
              <input type="number" value="<?php echo $thisRating; ?>" name="rating-value"/>
              <input type="submit" class="btn btn-link" value="Update">
            </form>

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
          <button type="button" class="btn btn-primary">Save changes</button>
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