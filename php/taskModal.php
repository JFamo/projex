<?php

session_start();

$task = $_SESSION['task'];

$out = "Dabdab";
$out = $out . $task;
$out = $out . "<script>$('#taskModal').modal('show');</script>";
echo $out;

?>