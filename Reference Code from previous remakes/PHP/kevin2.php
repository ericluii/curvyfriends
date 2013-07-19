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
    padding-top: 60px;
    padding-bottom: 40px;
}
.sidebar-nav {
padding: 9px 0;
}
</style>
<link href="resources/plugin/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Cedarville+Cursive' rel='stylesheet' type='text/css'>
<link href="resources/plugin/select2/select2.css" rel="stylesheet"/>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="resources/plugin/bootstrap/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="resources/plugin/bootstrap/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="resources/plugin/bootstrap/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="resources/plugin/bootstrap/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="resources/plugin/bootstrap/ico/favicon.png">
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

<script>
function loadGraph()
{
    $("#graphArea").load("graph.php");
    $(window).scrollTop($("#graphArea").offset().top);
    $("#graphArea").fadeIn(500);
}
</script>
</head>

<body>

<div class="container">
<div class="page-header">
<h1 style="font-family: 'Cedarville Cursive', cursive; cursor:pointer" onclick="location.href='/'">Curvy Friends</h1>
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
<script src="select2.js"></script>
<script>
$(document).ready(function() { $("#e1").select2(); });
</script>
<select id="e1">
    <option value="" selected="selected">Select Friend</option>
<?php
    $friendNames = $fb->api('/me?fields=friends');
    foreach($friendNames['friends']['data'] as $friend) {
        echo '<option value="' . $friend['id'] . '">' . $friend['name'] . '</option>';
    }
    ?>
</select>
<button class="btn btn-info" type="button" onclick="loadGraph()">Submit</button>
</p>
</div>
</div>
<div class="row" id="graphArea">
</div>
<br />

<hr>
<footer>
<p>Created by Eric Lui, Jonathan Yeung, Kevin Sito, and Hui Chen Puah at the University of Waterloo Facebook Hackathon 2013.</p>
</footer>
</div>

<!-- Le javascript
================================================== -->
<!- Placed at the end of the document so the pages load faster -->
<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-transition.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-alert.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-modal.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-dropdown.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-tab.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-tooltip.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-popover.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-button.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-collapse.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-carousel.js"></script>
<script src="resources/plugin/bootstrap/js/bootstrap-typeahead.js"></script>

<script type="text/javascript">
$(function(){  // $(document).ready shorthand
  $('#graphArea').hide();
  });
</script>
</body>
</html>
