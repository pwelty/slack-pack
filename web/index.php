<?php

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

// Establish connection to Pocket
// $action = $_GET['action'];

if (isset($_GET['simulate'])) {
  $simulate = true;
} else {
  $simulate = false;
}

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
  $simulate_channel['sg'] = getenv("SIMULATE_SG");
  $simulate_channel['nh'] = getenv("SIMULATE_NH");
  $pocket = new Pocket($pocket_consumer_key,$pocket_access_token);
  $slack_sg = new Slack($slack_token_sg,'sg',$simulate_channel['sg']);
  $slack_nh = new Slack($slack_token_nh,'nh',$simulate_channel['nh']);

  $map = array();
  $channels = array();
  $channels['sg'] = '_general';
  $channels['nh'] = 'general';
  $map['sg-general']=$channels;
  $channels['sg'] = 'tech';
  $channels['nh'] = 'tech-and-digital';
  $map['sg-tech']=$channels;
  // $channels['sg'] = '_ai';
  // $channels['nh'] = 'ai';
  // $map['sg-ai']=$channels;
  // $channels['sg'] = 'creative';
  // $channels['nh'] = 'creative';
  // $map['sg-creative']=$channels;

  $out = '';

  foreach ($map as $tag=>$channels) {
    $out .= r($tag);
    $posts = $pocket->getAPost($tag);
    $thePosts = $posts->list;
    $out .= r($thePosts);

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
        $out .= r($response,'SG');
        $response = $slack_nh->postTextToChannel($text,$channels['nh'],$simulate);
        $out .= r($response,'NH');
        $response = $pocket->untagPost($id,$tag,$simulate);
        $out .= r($response,'UNTAG');
      } // THEPOSTS

    }

  } // MAP

  $to_email = getenv('TO_EMAIL');
  mailIt($out,$to_email,$to_email);

} // ACTIONS IF

?>
