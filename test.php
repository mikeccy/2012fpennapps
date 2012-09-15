<?php
/**
 * test by Wei Dai 
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require 'src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '524346920915287',
  'secret' => '1cad981acc720a38f1cb1b9be5083f0f',
));

// Get User ID
$user = $facebook->getUser();

$error = '';

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    $facebook->setAccessToken($facebook->getAccessToken());
    $user_feed_result = $facebook->api(
        array('method'   => 'fql.query',
              'query'    => "SELECT post_id, actor_id, target_id, message FROM stream WHERE filter_key in (SELECT filter_key FROM stream_filter WHERE uid=me() AND type='newsfeed') AND is_hidden = 0 LIMIT 50",
              'access_token' => $facebook->getAccessToken(),
              'callback' => ''
    ));

  } catch (FacebookApiException $e) {
    $error = $e;
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $params = array(
    'scope' => 'read_stream',
    'redirect_uri' => 'http://www.mikeccy.co.cc/pennapps/test.php'
  );
  $loginUrl = $facebook->getLoginUrl($params);
}

// This call will always work since we are fetching public data.
// $naitik = $facebook->api('/naitik');

// mysql test
$mysql_username = 'mikeccy_pennapps';
$mysql_password = '2012fpennapps';
$mysql_server = 'localhost';
$mysql_database = 'mikeccy_pennapps';
$mysql_connection = mysql_connect($mysql_server, $mysql_username, $mysql_password);

if ($mysql_connection) {
    $mysql_dataconnection = mysql_select_db($mysql_database);
    if ($mysql_dataconnection) {
    
    } else {
	die('get database failed');
}
} else {
    die('mysql connection failed');
}

// mysql test table


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>php-sdk-test</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>php-sdk-test</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($user_profile); ?></pre>

      <h3>Your news feed</h3>
      <pre><?php print_r($user_feed_result); ?></pre>

    <?php else: ?>
      <pre><?php print_r($error); ?></pre>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>

  </body>
</html>
