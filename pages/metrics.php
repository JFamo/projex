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

<!--Spooky stuff in the middle-->

	<div class="container-fluid bodycontainer">
		<div class="row">
			<div class="col-sm-4">
				<h1>Welcome, <?php echo $_SESSION['firstname']; ?></h1>
				<p>Here's what your teams have been up to in the past week:</p>
				<table class="table">
					<thead class="thead-dark"><tr>
						<th class="bg-red" style="border-bottom:0px;" scope="col">Project</th>
						<th class="bg-red" style="border-bottom:0px;" scope="col">Member</th>
						<th class="bg-red" style="border-bottom:0px;" scope="col">Activity</th>
						<th class="bg-red" style="border-bottom:0px;" scope="col">Date</th>
					</tr></thead>
					<tbody>
					<tr>
						<td>TSA Software Dev</td>
						<td>Jim Marshall</td>
						<td>Closed Issue TS-1233</td>
						<td>12/29/2018</td>
					</tr>
					<tr>
						<td>TSA Software Dev</td>
						<td>Barry Sanders</td>
						<td>Created Task TS-86</td>
						<td>12/29/2018</td>
					</tr>
					<tr>
						<td>TSA Software Dev</td>
						<td>Lawrence Taylor</td>
						<td>Completed Task TS-329</td>
						<td>12/29/2018</td>
					</tr>
					</tbody>
				</table>
				<p>Here's what's assigned to you:</p>
				<table class="table">
					<thead class="thead-dark"><tr>
						<th class="bg-yellow" style="border-bottom:0px;" scope="col">Project</th>
						<th class="bg-yellow" style="border-bottom:0px;" scope="col">Task</th>
						<th class="bg-yellow" style="border-bottom:0px;" scope="col">Deadline</th>
					</tr></thead>
					<tbody>
					<tr>
						<td>TSA Software Dev</td>
						<td>Jim Marshall</td>
						<td>Closed Issue TS-1233</td>
					</tr>
					<tr>
						<td>TSA Software Dev</td>
						<td>Barry Sanders</td>
						<td>Created Task TS-86</td>
					</tr>
					<tr>
						<td>TSA Software Dev</td>
						<td>Lawrence Taylor</td>
						<td>Completed Task TS-329</td>
					</tr>
					</tbody>
				</table>
				<a href="#">Manage My Account</a>
			</div>
			<div class="col-sm-8">
			<canvas id="veloChart" class="dashchart"></canvas>
				<script>
				var ctx = document.getElementById("veloChart");
				var veloChart = new Chart(ctx, {
				    type: 'bar',
				    data: {
				        labels: ["Sprint 1", "Sprint 2", "Sprint 3"],
				        datasets: [{
				            label: 'Commitment',
				            data: [12, 19, 3],
				            backgroundColor: [
				                'rgba(216, 17, 89, 0.8)',
				                'rgba(216, 17, 89, 0.8)',
				                'rgba(216, 17, 89, 0.8)',
				            ],
				            borderWidth: 1
				        },
				        {
				            label: 'Delivered',
				            data: [10, 15, 8],
				            backgroundColor: [
				                'rgba(4, 150, 255, 0.8)',
				                'rgba(4, 150, 255, 0.8)',
				                'rgba(4, 150, 255, 0.8)',
				            ],
				            borderWidth: 1
				        }]
				    },
				    options: {
				    	title: {
				    		display: true,
            				text: 'Velocity Chart'
				    	},
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero:true
				                }
				            }]
				        }
				    }
				});
				</script>
				<canvas id="myChart2" class="dashchart"></canvas>
				<script>
				var ctx = document.getElementById("myChart2");
				var myDoughnutChart = new Chart(ctx, {
				    type: 'doughnut',
				    data: {"labels":["Red","Blue","Yellow"],"datasets":[{"label":"My First Dataset","data":[300,50,100],"backgroundColor":["rgb(216, 17, 89)","rgb(4, 150, 255)","rgb(255, 188, 66)"]}]}
				});
				</script>
			</div>
		</div>
	</div>
		<div>
			<!--Lets do this chart thing somewhere 
			<canvas id="Velocity"> </canvas>
		</div>
		<script>
			var ctx = document.getElementById("Velocity").getContext("2d");

			var VelocityChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Sprint 1', 'Sprint 2', 'Sprint 3', 'Sprint 4'],
					datasets: [{
						label: 'Commitment',
						data: [37,21,23,54],
						},
						{
						label: 'Delivered',
						data: [36,12,19,50]
						
					}]
				},
				options: {

				}
			})
		</script>
		-->
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