<?php

session_start();

$task = $_SESSION['task'];

    //Grab task variables

    require('connect.php');

    $query = "SELECT tasks.name, tasks.description, tasks.creator, tasks.date, tasks.status FROM tasks WHERE tasks.id = '$task'";
    $result = mysqli_query($link, $query);
    if (!$result){
      die('Error: ' . mysqli_error($link));
    }
    $taskArray = mysqli_fetch_array($result);
    $taskName = $taskArray['name'];
    $taskDesc = $taskArray['description'];
    $taskStatus = $taskArray['status'];
    $taskCreator = $taskArray['creator'];
    $taskDate = $taskArray['date'];

//Create out output variable
$out = "";

//Insert current variables
$out = $out . "<h4>" . $taskName . "</h4>";
$out = $out . "<p>" . $taskDesc . "</p>";
$out = $out . "<small>Task ID : " . $task . "</small><br>";
$out = $out . "<small>Created By : ";
  
	//Grab the name of the user who created this task
	require('connect.php');
	$query = "SELECT firstname, lastname FROM users WHERE id = '$taskCreator'";
	$result3 = mysqli_query($link, $query);
	if (!$result3){
	  die('Error: ' . mysqli_error($link));
	}
	list($firstname, $lastname) = mysqli_fetch_array($result3);

$out = $out . $firstname . " " . $lastname;
$out = $out . " on ";
$out = $out . $taskDate ."</small><br><br>";

//Edit variables
$out = $out . "<h4>Manage</h4><hr>";

//If we are a complete task, we want to be able to edit rating
if($taskStatus == "complete"){

	//Grab my task rating
	require('connect.php');
	$query = "SELECT rating FROM task_ratings WHERE task = '$task'";
	$result3 = mysqli_query($link, $query);
	if (!$result3){
	  die('Error: ' . mysqli_error($link));
	}
	list($thisRating) = mysqli_fetch_array($result3);

$out = $out . " <form method='post'>Rating<br><input type='hidden' value='" . $task . "' name='task-id' /><input type='number' value='" . $thisRating . "' name='rating-value'/><br><input type='submit' class='btn btn-primary' value='Update'></form>";
}

$out = $out . "<form method='post'><input type='hidden' name='edit-task-id' value='" . $task . "'/>Edit Name<br><input type='text' name='edit-task-name' value='" . $taskName . "'/><br><input type='submit' class='btn btn-primary' value='Change'/></form>";
$out = $out . "<form method='post'><input type='hidden' name='edit-task-id' value='" . $task . "'/>Edit Description<br><textarea maxlength='450' type='text' class='form-control' name='edit-task-desc' placeholder='Enter a task description'></textarea><input type='submit' class='btn btn-primary' value='Change'/></form>";

//Finally, show the modal
$out = $out . "<script>$('#taskModal').modal('show');</script>";

//Print output into modal dialogue body
echo $out;

?>