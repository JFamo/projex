<?php

session_start();

$task = $_SESSION['task'];

$out = "This is a test. My ID is : ";
$out = $out . $task;
$out = $out . "<script>$('#taskModal').modal('show');</script>";
echo $out;

?>