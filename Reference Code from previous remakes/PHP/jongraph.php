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

    $friend = $_POST['name'];

    $basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name),first_name,name');

?>

<hr>
<br/>
<div class="row">
<div class="span2 offset2"><h3>
<?php
    echo $basicInfo['name'] . "</h3></div><div class=\"span2\">";
    echo "<img src='https://graph.facebook.com/" . $user . "/picture?type=large'>";
    echo "</div><div class=\"span2\">";
    echo "<img src='https://graph.facebook.com/" . $friend . "/picture?type=large'></div>";
    echo "<div class=\"span2\"><h3>" . $basicInfo['friends']['data'][0]['name'];
    ?>
</h3>
</div>
</div>
<div class="row">
<div id="chart_div" style="width: 100%; height: 500px;"></div>
</div>

<?php
	
$friendToMeParam = array(
	'method' => 'fql.query',
	'query' => 'SELECT post_id, actor_id, created_time, message FROM stream WHERE message != "" AND source_id = me() AND actor_id = ' . $friend . ' LIMIT 5000',
);
$meToFriendsParam = array(
	'method' => 'fql.query',
	'query' => 'SELECT post_id, actor_id, target_id, created_time, message FROM stream WHERE message != "" AND source_id = ' . $friend . ' AND actor_id = me() AND created_time > 0 LIMIT 5000'
);

$friendsToMe = $fb->api($friendToMeParam);
$meToFriends = $fb->api($meToFriendsParam);

$friendsToMe = array_reverse($friendsToMe);
$meToFriends = array_reverse($meToFriends);

$friendsToMeArray = new ArrayObject($friendsToMe);
$meToFriendsArray = new ArrayObject($meToFriends);

$toMeIterator = $friendsToMeArray->getIterator();
$fromMeIterator = $meToFriendsArray->getIterator();

$currentDate = min($friendsToMe[0]['created_time'], $meToFriends[0]['created_time']);

$toMeCounter = 0;
$fromMeCounter = 0;
$rangeIndex = 0;

$graphData = array();

while($toMeIterator->valid() || $fromMeIterator->valid()) {
	if ($toMeIterator->valid()) {
		$postDetailsToMe = $toMeIterator->current();
	}

	if ($fromMeIterator->valid()) {
		$postDetailsFromMe = $fromMeIterator->current();
	}

	if ($toMeIterator->valid() && round(($postDetailsToMe['created_time'] - $currentDate) / 86400) <= 7) {
		$toMeCounter++;
		$toMeIterator->next();
	}

	if ($fromMeIterator->valid() && round(($postDetailsFromMe['created_time'] - $currentDate) / 86400) <= 7) {
		$fromMeCounter++;
		$fromMeIterator->next();
	}

	if ((!$toMeIterator->valid() || round(($postDetailsToMe['created_time'] - $currentDate) / 86400) > 7) && (!$fromMeIterator->valid() || round(($postDetailsFromMe['created_time'] - $currentDate) / 86400) > 7)) {

		array_push($graphData, array('Week ' . $rangeIndex, $toMeCounter + $fromMeCounter, $toMeCounter, $fromMeCounter));
		$rangeIndex++;
		$toMeCounter = 0;
		$fromMeCounter = 0;
		$currentDate += 604800;
	}
}

?>
