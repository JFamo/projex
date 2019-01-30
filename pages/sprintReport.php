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
		ProjeX Metrics
	</title>

	<!-- Import our CSS -->
	<link href="../css/main.css" rel="stylesheet" type="text/css" />

	<!-- Mobile metas -->
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Stuff for sidebar -->
	    <style>
    	.sidenav {
		  height: 100%;
		  width: 0;
		  position: fixed;
		  z-index: 1;
		  top: 0;
		  left: 0;
		  background-color: #111;
		  overflow-x: hidden;
		  transition: 0.5s;
		  padding-top: 60px;
		}

		.sidenav a {
		  padding: 8px 8px 8px 32px;
		  text-decoration: none;
		  font-size: 25px;
		  color: #818181;
		  display: block;
		  transition: 0.3s;
		}

		.sidenav a:hover {
		  color: #f1f1f1;
		}

		.sidenav .closebtn {
		  position: absolute;
		  top: 0;
		  right: 25px;
		  font-size: 36px;
		  margin-left: 50px;
		}
		@media screen and (max-height: 450px) {
		  .sidenav {padding-top: 15px;}
		  .sidenav a {font-size: 18px;}
		}
		</style>
	</head>

<body>
<!-- Navbar -->
	<nav class="navbar navbar-dark bg-primary">
		<div class="navpadder">
		  	<a class="nav-link" href="#" style="flex-basis:20%;"><img src="" width="30" height="30" class="d-inline-block align-top" alt="" />ProjeX</a>
		  	<a class="nav-link" href="#"><img src="../imgs/workspacePlaceholder.png" width="30" height="30" class="d-inline-block align-top" alt="" /></a>
		    <a class="nav-link" href="metrics.php">Metrics</a>
		    <a class="nav-link" href="metrics.php">Backlog</a>
		    <a class="nav-link" href="metrics.php">Active</a>
		    <a class="nav-link" href="metrics.php">Docs</a>
		    <a class="nav-link" href="metrics.php">Messages</a>
		    <a class="nav-link" href="../php/logout.php">Logout</a>
	    </div>
	</nav>

<!-- Sidebar for graphs -->

	<div id="mySidenav" class="sidenav">
	  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	  <a href="/projex/pages/metrics.php">Velocity Chart</a>
	  <a href="/projex/pages/relativeContribution.php">Relative Contribution</a>
	  <a href="#">Sprint Report</a>
	</div>

	<h2>Sprint Report</h2>
	<span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; Charts</span>

	<script>
	function openNav() {
	  document.getElementById("mySidenav").style.width = "250px";
	}

	function closeNav() {
	  document.getElementById("mySidenav").style.width = "0";
	}
	</script>
<!--Spooky stuff in the middle-->

	<div class="container-fluid bodycontainer">
		<div class="row">
			<div class="col-sm-8">
			<canvas id="myChart" class="dashchart"></canvas>
				<script>
				var ctx = document.getElementById("myChart");
				var myChart = new Chart(ctx, {
				    type: 'line',
				    data: {
				labels: ["Day 0", "Day 3","Day 5","Day 7", "Day 9", "Day 11","Day 14"],
				datasets: [{
					label: 'Ideal',
					backgroundColor: 'rgba(216, 17, 89, 0.8)',
					borderColor: 'rgba(216, 17, 89, 0.8)',
					data: [
						{
							x:0,
							y:14
						},
						{
							x:3,
							y:14
						},
						{
							x:5,
							y:14
						},
						{
							x:7,
							y:14
						},
						{
							x:9,
							y:14
						},
						{
							x:11,
							y:14
						},
						{
							x:14,
							y:0
						}
					],
					fill: false,
				}, {
					label: 'Real',
					fill: false,
					backgroundColor: 'rgba(4, 150, 255, 0.8)',
					borderColor: 'rgba(4, 150, 255, 0.8)',
					data: [{
							x:0,
							y:14
						},
						{
							x:3,
							y:11
						},
						{
							x:5,
							y:9
						},
						{
							x:7,
							y:8
						},
						{
							x:9,
							y:5
						},
						{
							x:11,
							y:3
						},
						{
							x:14,
							y:1
						}
					],
				}]
			},
				    options: {
				    	layout: {
				            padding: {
				                left: 50,
				                right: 0,
				                top: 0,
				                bottom: 0
				            }
				        },
				    	title: {
				    		display: true,
            				text: 'Sprint Burndown'
				    	},
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero:true
				                }
				            }]
				        }
				    }
				})
				</script>
				
			</div>
		</div>
	</div>
		<div>
</body>


<footer class="text-white bg-primary py-3 h5"> 
	<center><p class="bodyTextType2">
		Team 2004-901 2018
	</p></center>
</footer>


<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>