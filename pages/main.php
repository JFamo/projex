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
				<a href="account.php">Manage My Account</a>
			</div>
			<div class="col-sm-8">
			<canvas id="myChart" class="dashchart"></canvas>
				<script>
				var ctx = document.getElementById("myChart");
				var myChart = new Chart(ctx, {
				    type: 'bar',
				    data: {
				        labels: ["Jim", "Paul", "Sean", "Barry", "Lawrence", "Matt"],
				        datasets: [{
				            label: 'Contributions',
				            data: [12, 19, 3, 5, 2, 3],
				            backgroundColor: [
				                'rgba(216, 17, 89, 0.8)',
				                'rgba(255, 188, 66, 0.8)',
				                'rgba(4, 150, 255, 0.8)',
				                'rgba(0, 107, 166, 0.8)',
				                'rgba(102, 17, 102, 0.8)',
				                'rgba(255, 159, 64, 0.8)'
				            ],
				            borderColor: [
				                'rgba(216, 17, 89, 1)',
				                'rgba(255, 188, 66, 1)',
				                'rgba(4, 150, 255, 1)',
				                'rgba(0, 107, 166, 1)',
				                'rgba(102, 17, 102, 1)',
				                'rgba(255, 159, 64, 1)'
				            ],
				            borderWidth: 1
				        }]
				    },
				    options: {
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

</body>

<!--Main has no footer
<footer class="text-white bg-primary py-3 h5"> 
	<center><p class="bodyTextType2">
		Team 2004-901 2018
	</p></center>
</footer>
-->

<script src="../js/scripts.js" type="text/javascript"></script>

</html>

<?php 

}

?>