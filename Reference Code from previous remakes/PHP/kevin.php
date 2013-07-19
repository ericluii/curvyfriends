<?php
    
    require 'facebook-php-sdk/src/facebook.php';
    
    // app id and app secret from here: https://developers.facebook.com/apps
    $fb = new Facebook(array(
                             'appId' => '188051731337789',
                             'secret' => '6a8cd18f7e4286dccb98c4b2810b5374',
                             ));

	$user = $fb->getUser();
	if (!$user) { // if user has not authenticated your app
        $params = array(
                        // the permissions you're requesting - click Extended Profile Properties or one of the other bullets here:
                        // https://developers.facebook.com/docs/concepts/login/permissions-login-dialog/
                        // no additional permissions are needed for our cover photo app, but I'll leave these in here as an example
                        'scope' => 'user_relationships,friends_relationships,read_stream',
                        // this is where the user will be sent after they click Allow or Go To App on the permissions dialog
                        //'redirect_uri' => 'https://apps.facebook.com/thedoggiebag',
                        );
        $login_url = $fb->getLoginUrl($params);
        print '<script>top.location.href = "' . $login_url . '"</script>'; //redirect the user to the permissions dialog
        exit();
    }
    
    // fetch the user's friends' cover photos
    // play with the Graph API Explorer to test out queries: https://developers.facebook.com/tools/explorer
    // click the Get Access Token button on that page to get more permissions for your test queries, but make
    // sure you add them to the 'scope' above if you plan on actually using them in your app
    //$data = $fb->api('/me?fields=friends.fields(picture)');
    //$friends = $data['friends']['data']; // a proper app would also check for errors, this assumes it worked
    //shuffle($friends); // randomize the array
    //foreach ($friends as $friend) {
	//	print "<img src='" . $friend['picture']['data']['url'] . "' >";
	//}

	$friendToMeParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT post_id, actor_id, created_time, message FROM stream WHERE message != "" AND source_id = me() AND actor_id = 563765400 LIMIT 5000',
	);

	$friendsToMe = $fb->api($friendToMeParam);
	$friendsToMe = array_reverse($friendsToMe);

	$firstPost = reset($friendsToMe);
	$comparisonDate = date("YmdHis", $firstPost['created_time']);
	$counter = 0;
	foreach($friendsToMe as $ToMe) {
		print date(DATE_ATOM, $ToMe['created_time'] - $firstPost['created_time']) . '<br />';
		//print date("YmdHis", $ToMe['created_time']) - $comparisonDate . '<br />';
	}

    print "<img src='https://graph.facebook.com/" . $user . "/picture?type=large'>" . '<br />' ;

    $data = $fb->api('/me?fields=friends.fields(name)');
    $friends = $data['friends']['data']; // a proper app would also check for errors, this assumes it worked
    //shuffle($friends); // randomize the array
    /*foreach ($friends as $friend) {
		print $friend['name'] . '<br />';
	}*/

    
    	print_r($fb->getAccessToken());
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Curvy Friends Facbook Application</title>

<script type="text/javascript" src="jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
	function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$.post("rpc.php", {queryString: ""+inputString+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	} // lookup
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
</script>

<style type="text/css">
	body {
		font-family: Helvetica;
		font-size: 11px;
		color: #000;
	}
	
	h3 {
		margin: 0px;
		padding: 0px;	
	}

	.suggestionsBox {
		position: relative;
		left: 30px;
		margin: 10px 0px 0px 0px;
		width: 200px;
		background-color: #212427;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border: 2px solid #000;	
		color: #fff;
	}
	
	.suggestionList {
		margin: 0px;
		padding: 0px;
	}
	
	.suggestionList li {
		
		margin: 0px 0px 3px 0px;
		padding: 3px;
		cursor: pointer;
	}
	
	.suggestionList li:hover {
		background-color: #659CD8;
	}
</style>

</head>

<body>


	<div>
		<form>
			<div>
				<br />
				Please enter your friend's name:
				<br />
				<input type="text" size="30" value="" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" />
			</div>
			
			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<img src="upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />
				<div class="suggestionList" id="autoSuggestionsList">
					&nbsp;
				</div>
			</div>
		</form>
	</div>

</body>
</html>


