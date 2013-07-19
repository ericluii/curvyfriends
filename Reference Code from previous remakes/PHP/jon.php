
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

	print_r($fb->getAccessToken());
	print 'before if';
	
	if(!empty($_GET['name'])) {
		print 'triggered';
		$name = $_GET['name'];
		$query = 'SELECT post_id, actor_id, target_id, created_time, message FROM stream WHERE message != "" AND source_id = ' . $user . ' AND actor_id = ' . $name . ' LIMIT 5000';
		print $query;
		// $url = 'http://graph.facebook.com/fql?q=' . urlencode($query);
		// $result = json_decode( file_get_contents($url));
		// $result = $fb->api($query);
		$result = json_decode ( file_get_contents('https://graph.facebook.com/fql?q=' . urlencode($query) ));
		print_r ($result);
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ajax Auto Suggest</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>

<script>
	$('form').submit(function(){
		$.get(
			'nosource/ajax',
			$(this).serialize(),
			function(result) {
				$('body').append(result);
				$('body').append('<br/>');
			}
		);
		return false;
	});
</script>

</head>

<body>
	<form>
	Name: <input type="text" name="name"/>
	</form>
</body>
</html>
