<?php
	//header('Content-type: application/json');
    require 'resources/plugin/facebook-php-sdk/src/facebook.php';
    require 'resources/plugin/friend_graph.php';
    
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
    // The Post from the Form
    $friend = $_POST['name'];

    // grabbing info from the facebook API
    $basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name),first_name,name');

    // Query strings
    // POSTS
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
    
    $myfriendPosts = count($friendsToMe);
    $myPostCount = count($meToFriends);
    

    //$postData = $graphMethods->createGraphData($friendsToMe, $meToFriends);

    // COMMENTS
    $friendToMeComm = array(
    'method' => 'fql.query',
    'query' => 'SELECT comments.count FROM stream WHERE source_id = me() AND actor_id = ' . $friend . ' AND comments.count > 0 AND created_time > 0 LIMIT 5000',
    );
    $meToFriendsComm = array(
    'method' => 'fql.query',
    'query' => 'SELECT comments.count FROM stream WHERE source_id = ' . $friend . ' AND actor_id = me() AND comments.count > 0 AND created_time > 0 LIMIT 5000',
    );

    $friendsToMeComments = $fb->api($friendToMeComm);
    $meToFriendsComments = $fb->api($meToFriendsComm);
    
    // $commentData = $graphMethods->createGraphData($friendsToMe, $meToFriends);
    $myfriendComments = count($friendsToMeComments);
    $myCommentCount = count($meToFriendsComments);

    // Stuff to return back.
    $userName =  $basicInfo['name'];
    $friendName = $basicInfo['friends']['data'][0]['name'];
    $userID = $user;
    $friendID = $friend;

    echo "{";
	echo '"userName": ', json_encode($userName),",", "\n";
	echo '"userID": ', json_encode($userID),",", "\n";
	echo '"friendName": ', json_encode($friendName),",", "\n";
	echo '"friendID": ', json_encode($friendID),",", "\n";
    echo '"myPostCount": ', json_encode($myPostCount),",","\n";
    echo '"myCommentCount": ', json_encode($myCommentCount),",","\n";
    echo '"myfriendPosts": ', json_encode($myfriendPosts),",", "\n";
    //echo '"myCommentCount": ', json_encode($myCommentCount),"," "\n";
    echo '"myfriendComments": ', json_encode($myfriendComments), "\n";
	echo "}";
?>

