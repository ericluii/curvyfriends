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
    
    if ($user == 593058366) {
        $friend = 1651350470;
    }
    else if ($user == 1651350470) {
        $friend = 593058366;
    }
    else if ($user == 587835121) {
        $friend = 577535260;
    }
    else if ($user == 577535260) {
        $friend = 587835121;
    }
    else if ($user == 517328640) {
        $friend = 608622456;
    }
    else if ($user == 504018919) {
        $friend = 509747333;
    }
    else {
        $friend = 1651350470;
    }
    
    $basicInfo = $fb->api('me?fields=friends.uid(' . $friend . ').fields(first_name,name),first_name,name');
 ?>
<hr>
<br />
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