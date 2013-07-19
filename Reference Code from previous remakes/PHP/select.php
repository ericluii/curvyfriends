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

<link href="resources/plugin/select2/select2.css" rel="stylesheet"/>

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
<script src="resources/plugin/select2/select2.js"></script>
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
<button class="btn btn-info" type="button">Submit</button>
</p>