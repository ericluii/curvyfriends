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

//////////////////////////////////////////////	
		
	//$db = new mysqli('localhost', 'USERNAME' ,'PASSWORD', 'DATABASE');
	
	/*$fquery = array(
		'method' => 'fql.query',
		'query' => 'SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())',
	);*/

	
	$param = array(
		'method' => 'fql.query',
		'query' => 'SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())',
	);
	
	$db = $fb->api($param);
	
	if(!$db) {
		// Show error if we cannot connect.
		echo 'ERROR: Could not connect to the database.';
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $db->real_escape_string($_POST['queryString']);
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
				// Run the query: We use LIKE '$queryString%'
				// The percentage sign is a wild-card, in my example of countries it works like this...
				// $queryString = 'Uni';
				// Returned data = 'United States, United Kindom';
				
				// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
				// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$queryString%' LIMIT 10
				
				$param2 = array(
							'method' => 'fql.query',
							'query' => 'SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) LIKE '$queryString%' LIMIT 10');
						
				$query = $fb->api($param2);
				/*$query = array(
					'method' => 'fql.query',
					'query' => "SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) LIKE '$queryString%' LIMIT 10");*/
								
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					while ($result = $query ->fetch_object()) {
						// Format the results, im using <li> for the list, you can change it.
						// The onClick function fills the textbox with the result.
					print $result . <br />;	
						// YOU MUST CHANGE: $result->value to $result->your_colum
	         			echo '<li onClick="fill(\''.$result->'name'.'\');">'.$result->'name'.'</li>';
	         		}
				} else {
					echo 'ERROR: There was a problem with the query.';
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
?>
