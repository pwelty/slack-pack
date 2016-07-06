<?php

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

// Establish connection to Pocket
// $action = $_GET['action'];
// $pocket = new Pocket($action);
$pocket = new Pocket;
$slack = new Slack;
$tag = 'sg-tech';
$posts = $pocket->getAPost($tag);
$thePosts = $posts->list;
phpinfo();
exit;
$slack_token = getenv('SLACK_TOKEN');
r($slack_token,'slack token');
exit;
foreach ($thePosts as $aPost) {
  // r($aPost);
  $excerpt = $aPost->excerpt;
  $url = $aPost->resolved_url;
  $title = $aPost->resolved_title;
  $id = $aPost->item_id;
  $text = $url;
  // $text = "*".$title."*\n".$excerpt."\n<".$url."> ";
  // r($excerpt,"excerpt");
  $channel = '@paul';
  $response = $slack->postTextToChannel($text,$channel,$slack_token);
  r($response);
  $pocket->untagPost($id,$tag);
}


?>
