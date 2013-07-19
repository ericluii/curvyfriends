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
		opacity:    0.8; 
		background: rgba(255, 255, 255, 0.5);
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
var options = {
	title: 'Friendship Graph',
	areaOpacity: 0.2,
	hAxis: {showTextEvery: 5, slantedText: true, slantedTextAngle: 70 },
	vAxis: {viewWindowMode:'pretty', minValue:4, gridlines: {count:4}},
	legend: {position: 'in'}
};
var postData;
var commentData;
var interactionData;
var formData;

google.load("visualization", "1", {packages:["corechart"]});

function drawChart(array) {
	dataPoints = google.visualization.arrayToDataTable(array);

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
			data: formData,
			dataType : 'json',
			beforeSend: function() {
				$('#generalError').hide();
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
				$('#generalError').show();
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

function submitForm(formData){
	postData = null;
	commentData = null;
	interactionData = null;
	$('#graphSelection').children().removeClass("disabled btn-info");
	$('#post').addClass("disabled btn-info");

	$.ajax({    
		type: 'POST',
		url: 'parts/display_picture.php',        
		data: formData,
		dataType: 'html',
		cache: false,
		beforeSend: function() {
			$('#generalError').hide();
			$("#report-loading").fadeIn(500);
		},
		success: function(data) {
			$("#picture_area").html(data);
			$("#picture_area").fadeIn(500);
		},
		error : function () {
			$('#generalError').show();
		}
	})

	$.ajax({    
		type: 'POST',
		url : 'parts/post_graph.php',        
		data: formData,
		dataType: 'json',
		cache: false,
		beforeSend: function() {
			$('#generalError').hide();
			$("#report-loading").fadeIn(500);
			$("#chart_area").fadeIn(500);
			chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
		},
		success: function(jsonData) { 
			postData = jsonData;

			drawChart(jsonData);
			$("#report-loading").fadeOut(500);
			$(window).scrollTop($('#picture_area').offset().top);
		},
		error : function () {
			$("#report-loading").fadeOut(500);
			$('#generalError').show();
		}
	})
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

		<div class="alert alert-error" id="noFriend" style="display:none;">  
			<a class="close" data-dismiss="alert">×</a>  
			<strong>Error:</strong> You must select a friend.
		</div>
		<div class="alert alert-error" id="generalError" style="display:none;">  
			<a class="close" data-dismiss="alert">×</a>  
			<strong>Error:</strong> Something went wrong.
		</div> 

		<div class="row" id="chooseUser">
			<div class="span5 offset1" style="text-align:center">
				<img src="resources/img/homepage.jpg">
				<p><h2>Welcome to Curvy Friends!</h2></p>
				<p><h4>The simple web application that helps you analyze your Facebook Friendships.</h4></p>
			</div>
			<div class="span3 offset1" style="text-align:center">
				<br />
				<br />
				<br />
				<h5>
					We take the data you gave Facebook and use it to create a humorous graph for you and your friends to laugh about.
				</h5>
				<h6>
					Disclaimer: We take no responsibility for broken friendships.
				</h6>
				<p>
					<h4>Pick your friend and hit submit:</h4>
				</p>
				<p>
					<select id="e1">
						<option value="" selected="selected">Select Friend</option>
						<?php
						$friendNames = $fb->api('/me?fields=friends');
						sort($friendNames['friends']['data']);
						foreach($friendNames['friends']['data'] as $friend) {
							echo '<option value="' . $friend['id'] . '">' . $friend['name'] . '</option>';
						}
						?>
					</select>
					<form name="input">
						<input type="hidden" name="name" id="name"/>
						<input id="submit" type="submit" class="btn btn-info" value="Submit"/>
					</form>
				</p>
			</div>
		</div>

		<div class="row" id="picture_area" style="display: none;"></div>
		<div class="row" id="chart_area" style="display: none;">
			<div id="chart_div" style="width: 100%; height: 500px;"></div>
			<div class="alert" style="text-align: center;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Note:</strong> Data past a certain time period may not be represented due to Facebook's API Restrictions.
			</div>
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
	</div>

	<br />
	<br />
	<br />
	<br />
	<hr>
	<footer class="offset1">
		<p>Created by Eric Lui, Jonathan Yeung, Kevin Sito, and Hui Chen Puah at the University of Waterloo Facebook Hackathon 2013.</p>
	</footer>
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="resources/plugin/bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript">
	$('.alert .close').live("click", function(e) {
		$(this).parent().hide();
	});

	$("#graphSelection").on("click", "button", graphSelection);

	$(document).ready(function(){
		$("#e1").change(function(){
			var uid = $("#e1").find(":selected").val();
			$("#name").val(uid);
		});

		$('#submit').click(function(e){
			e.preventDefault();
			formData = $('form').serialize();
			if (formData == "name=") {
				$('#noFriend').show();
			}
			else {
				$('#noFriend').hide();
				submitForm(formData);
			}
		});
	});
	</script>
</body>
</html>
