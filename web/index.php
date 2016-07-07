<?php

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

// Establish connection to Pocket
$action = $_GET['action'];
// $pocket = new Pocket($action);

if ($action=='pocket-auth') {
  $pocket = new Pocket($action);
} elseif ($action=='authorized') {
  $pocket = new Pocket($action);
} else {
  $slack_token = getenv('SLACK_TOKEN');
  $pocket_consumer_key = getenv("POCKET_CONSUMER_KEY");
  $pocket_access_token = getenv("POCKET_ACCESS_TOKEN");
  $pocket = new Pocket($pocket_consumer_key,$pocket_access_token);
  $slack = new Slack($slack_token);

  $tag = 'sg-tech';
  $channel = 'tech';

  $posts = $pocket->getAPost($tag);
  $thePosts = $posts->list;

  foreach ($thePosts as $aPost) {
    $excerpt = $aPost->excerpt;
    $url = $aPost->resolved_url;
    $title = $aPost->resolved_title;
    $id = $aPost->item_id;
    $text = $url;
    // $text = "*".$title."*\n".$excerpt."\n<".$url."> ";
    // r($excerpt,"excerpt");
    // $channel = '@paul';
    $response = $slack->postTextToChannel($text,$channel,$slack_token);
    r($response);
    $response = $pocket->untagPost($id,$tag);
    r($response);
  }

}

?>
