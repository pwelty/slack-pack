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
  $slack_token_sg = getenv('SLACK_TOKEN');
  $slack_token_nh = getenv('SLACK_TOKEN_NH');
  $pocket_consumer_key = getenv("POCKET_CONSUMER_KEY");
  $pocket_access_token = getenv("POCKET_ACCESS_TOKEN");
  $pocket = new Pocket($pocket_consumer_key,$pocket_access_token);
  $slack_sg = new Slack($slack_token_sg,'sg');
  $slack_nh = new Slack($slack_token_nh,'nh');

  $map = array();
  $simulate = true;
  // $map_item['sg-tech']='tech';
  // $map[] = $map_item;
  $channels = array();
  $channels['sg'] = 'general';
  $channels['nh'] = 'general';
  $map['sg-general']=$channels;
  $channels['sg'] = 'tech';
  $channels['nh'] = 'tech-and-digital';
  $map['sg-tech']=$channels;

  foreach ($map as $tag=>$channels) {
    r($tag);
    $posts = $pocket->getAPost($tag);
    $thePosts = $posts->list;
    r($thePosts);

    if (!empty($thePosts)) {

      foreach ($thePosts as $aPost) {
        $excerpt = $aPost->excerpt;
        $url = $aPost->resolved_url;
        $loc = strpos($url,"?");
        $url = substr($url,0,strlen($url)-$loc);
        $title = $aPost->resolved_title;
        $id = $aPost->item_id;
        $text = $url;
        // $text = "*".$title."*\n".$excerpt."\n<".$url."> ";
        // r($excerpt,"excerpt");
        // $channel = '@paul';
        $response = $slack_sg->postTextToChannel($text,$channels['sg'],$simulate);
        $response = $slack_nh->postTextToChannel($text,$channels['nh'],$simulate);
        r($response);
        $response = $pocket->untagPost($id,$tag,$simulate);
        r($response);
      }

    }

  }

}

?>
