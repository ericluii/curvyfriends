<?php

require '../resources/plugin/facebook-php-sdk/src/facebook.php';
require '../resources/plugin/friend_graph.php';

$graphMethods = new GraphMethods();

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

 $friend = $_POST['name'];

$basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name),first_name,name');

if ($graphMethods->getCommentGraphData() === null) {
	$friendToMeParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT actor_id, created_time, comments.count FROM stream WHERE source_id = me() AND comments.count > 0 AND actor_id = ' . $friend . ' AND created_time > 0 LIMIT 5000',
		);
	$meToFriendsParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT actor_id, created_time, comments.count FROM stream WHERE source_id = ' . $friend . ' AND comments.count > 0 AND actor_id = me() AND created_time > 0 LIMIT 5000',
		);

	$friendsToMe = $fb->api($friendToMeParam);
	$meToFriends = $fb->api($meToFriendsParam);

	$graphMethods->createCommentGraphData($friendsToMe, $meToFriends);
}

if ($graphMethods->getPostGraphData() === null) {
	$friendToMeParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT actor_id, created_time FROM stream WHERE message != "" AND source_id = me() AND actor_id = ' . $friend . ' LIMIT 5000',
		);
	$meToFriendsParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT actor_id, created_time FROM stream WHERE message != "" AND source_id = ' . $friend . ' AND actor_id = me() AND created_time > 0 LIMIT 5000',
		);

	$friendsToMe = $fb->api($friendToMeParam);
	$meToFriends = $fb->api($meToFriendsParam);

	$graphMethods->createPostGraphData($friendsToMe, $meToFriends);
}

$interactionData = $graphMethods->createInteractionGraphData();

if(count($interactionData) == 0) {
	array_unshift($interactionData, array(gmdate("M d, Y", time()), 0, 0 ,0));
}

array_unshift($interactionData, array("Week", "Overall Interaction", $basicInfo['friends']['data'][0]['first_name'] . " to " . $basicInfo['first_name'], $basicInfo['first_name'] . " to " . $basicInfo['friends']['data'][0]['first_name']));

echo json_encode($interactionData);
?>