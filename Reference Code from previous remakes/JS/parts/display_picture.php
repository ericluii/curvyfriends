<?php

require '../resources/plugin/facebook-php-sdk/src/facebook.php';

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

$friend = $_POST['name'];

$basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name,picture.type(large)),first_name,name,picture.type(large)');
?>
<hr>
<div class="span2 offset1">
	<h3 style="float:right;">
		<?php
		echo $basicInfo['name'] . "</h3></div><div class=\"span2\">";
		echo "<a href='http://www.facebook.com/" . $user . "' class='thumbnail' target='_blank'><img src='" . $basicInfo['picture']['data']['url'] . "'></a>";
		echo "</div>";
		?>
			<div class="span2"><button onclick="window.open('<?php echo "http://www.facebook.com/" . $user . "?and=" . $friend; ?>')" class="btn btn-info">View Friendship</button></div>
		<?php
		echo "<div class=\"span2\"><a href='http://www.facebook.com/" . $friend . "' class='thumbnail' target='_blank'><img src='" . $basicInfo['friends']['data'][0]['picture']['data']['url'] . "'></div></a>";
		echo "<div class=\"span2\"><h3 style=\"float:left;\">" . $basicInfo['friends']['data'][0]['name'];
		?>
	</h3>
</div>