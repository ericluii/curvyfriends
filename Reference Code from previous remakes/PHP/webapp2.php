<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Curvy Friends</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Le styles -->
	<link href="resources/plugin/bootstrap/css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
	body {
		padding-top: 10px;
		padding-bottom: 40px;
	}
	.sidebar-nav {
		padding: 9px 0;
	}
	.loading {
		opacity:    0.5; 
		background: #FFFFFF; 
		width:      100%;
		height:     100%; 
		z-index:    10;
		top:        0; 
		left:       0; 
		position:   fixed;
		display: none;
	}
	</style>
	<link href="resources/plugin/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Cedarville+Cursive' rel='stylesheet' type='text/css'>
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
var dataPoints;
var chart;
var options;
var postData;
var commentData;
var interactionData;

google.load("visualization", "1", {packages:["corechart"]});

function drawChart(array) {
	dataPoints = google.visualization.arrayToDataTable(array);

	chart = new google.visualization.AreaChart(document.getElementById('chart_div'));

	options = {
		title: 'Friendship Graph',
		areaOpacity: 0.2,
		hAxis: {showTextEvery: 5, slantedText: true, slantedTextAngle: 70 },
		vAxis: {viewWindowMode:'pretty'},
		legend: {position: 'in'}
	};

	chart.draw(dataPoints, options);
}

function graphSelection() {
	if(!$(this).hasClass('disabled')) {
    	$(this)
        	.addClass("disabled btn-info")
        	.siblings()
            	.removeClass("disabled btn-info");
    	loadGraphs(this.id);
	}
}

function loadGraphs(type) {
	console.log(type, postData, commentData, interactionData);
	if ((type == "post" && !postData) || (type == "comment" && !commentData) || (type == "interaction" && !interactionData)) {
		$.ajax({
			url : 'parts/' + type + '_graph.php',
			cache: false,
			type : 'POST',
			dataType : 'json',
			beforeSend: function() {
				$("#report-loading").fadeIn(500);
			},
			success : function (jsonData) {
				if (type == "post")
					postData = jsonData;
				else if (type == "comment")
					commentData = jsonData;
				else if (type == "interaction")
					interactionData = jsonData;
				
				drawChart(jsonData);
				$("#report-loading").fadeOut(500);
			},
			error : function () {
				$("#report-loading").fadeOut(500);
				alert("error");
			}
		})
	}
	else if (type == "post") {
		drawChart(postData);
	}
	else if (type == "comment") {
		drawChart(commentData);
	}
	else if (type == "interaction") {
		drawChart(interactionData);
	}
}
</script>
<?php

require 'resources/plugin/facebook-php-sdk/src/facebook.php';

// app id and app secret from here: https://developers.facebook.com/apps
$fb = new Facebook(array(
	'appId' => '188051731337789',
	'secret' => '6a8cd18f7e4286dccb98c4b2810b5374',
	));

$user = $fb->getUser();

if (!$user) { // if user has not authenticated your app
	$params = array(
		'scope' => 'user_relationships,friends_relationships,read_stream',
		);
	$login_url = $fb->getLoginUrl($params);
	print '<script>top.location.href = "' . $login_url . '"</script>'; //redirect the user to the permissions dialog
	exit();
}

if ($user == 593058366) {
	$friend = 1651350470;
}
else if ($user == 1651350470) {
	$friend = 593058366;
}
else if ($user == 587835121) {
	$friend = 577535260;
}
else if ($user == 577535260) {
	$friend = 587835121;
}
else if ($user == 517328640) {
	$friend = 608622456;
}
else if ($user == 504018919) {
	$friend = 509747333;
}
else {
	$friend = 593058366;
}

$basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name,picture.type(large)),first_name,name,picture.type(large)');
?>
</head>

<body>
	<div id="report-loading" class="loading">
		<div id="progressbar" class="progress progress-striped active">
			<div class="bar" style="width: 100%;">Please be patient... Loading data from Facebook.</div>
		</div>
	</div>

	<div class="container">
		<div class="page-header">
			<h1 style="font-family: 'Cedarville Cursive', cursive; cursor:pointer" onclick="location.href='/'">Curvy Friends</h1>
		</div>
		<p class="lead">A simple web application for analyzing the quality of friendship between you and a Facebook friend.
			<button onclick="window.open('<?php echo "http://www.facebook.com/" . $user . "?and=" . $friend; ?>')" class="btn btn-info">View Friendship</button>
		</p>
		<div class="row">
			<div class="span2 offset2">
				<h3>
					<?php
					echo $basicInfo['name'] . "</h3></div><div class=\"span2\">";
					echo "<a href='http://www.facebook.com/" . $user . "' class='thumbnail' target='_blank'><img src='" . $basicInfo['picture']['data']['url'] . "'></a>";
					echo "</div><div class=\"span2\">";
					echo "<a href='http://www.facebook.com/" . $friend . "' class='thumbnail' target='_blank'><img src='" . $basicInfo['friends']['data'][0]['picture']['data']['url'] . "'></div></a>";
					echo "<div class=\"span2\"><h3>" . $basicInfo['friends']['data'][0]['name'];
					?>
				</h3>
			</div>
		</div>
		<div class="row" id="chart_area">
			<div id="chart_div" style="width: 100%; height: 500px;"></div>
		</div>
	</div>
	<div class="row">
		<br />
		<div class="span2 offset2 lead">
			View Graph:
		</div>
		<div class="span8">
			<div class="btn-group" id="graphSelection">
				<button type="button" class="btn disabled btn-info" id="post">Posts</button>
				<button type="button" class="btn" id="comment">Comments</button>
				<button type="button" class="btn" id="interaction">All Interaction</button>
			</div>
		</div>
	</div>

	<hr>
	<footer>
		<p>Created by Eric Lui, Jonathan Yeung, Kevin Sito, and Hui Chen Puah at the University of Waterloo Facebook Hackathon 2013.</p>
	</footer>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript">
	$("#graphSelection").on("click", "button", graphSelection);
	jQuery(window).load(function () {
		loadGraphs('post');
	});
	</script>
</body>
</html>
