<?php

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

// IMPORT THE CONFIG FILE, ESP. THE POCKET->SLACK CHANNELS MAP
include_once 'config.php';

// GET ENV VARS. FOR LOCAL USING .HTACCESS FILE

if (isset($config->pocket_suffix)) {
  $pocketSuffix = $config->pocket_suffix;
} else {
  $pocketSuffix = getenv("POCKET_SUFFIX");
}

if (isset($config->simulate_channel)) {
  $simulateChannel = $config->simulate_channel;
} else {
  $simulateChannel = getenv("SIMULATE_CHANNEL");
}

if (isset($config->slack_token)) {
  $slackToken = $config->slack_token;
} else {
  $slackToken = getenv('SLACK_TOKEN');
}

if (isset($config->pocket_consumer_key)) {
  $pocketConsumerKey = $config->pocket_consumer_key;
} else {
  $pocketConsumerKey = getenv("POCKET_CONSUMER_KEY");
}

if (isset($config->pocket_access_token)) {
  $pocketAccessToken = $config->pocket_access_token;
} else {
  $pocketAccessToken = getenv("POCKET_ACCESS_TOKEN");
}

if (isset($config->channel_map)) {
  $map = $config->channel_map;
} else {
  $map = getenv("CHANNEL_MAP");
}
$channelMap = json_decode($map);

if (!$pocketConsumerKey) {
  die("You need to put the Pocket consumer key in the env vars or the config file before we can do anything.");
}

if ($pocketConsumerKey && !$pocketAccessToken) {
  header('Location: /pocket-auth.php');
  error_log("Redirecting to get Pocket access token");
  exit;
}

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

$simulate = true;
if (isset($_GET['live'])) {
  $simulate = false;
}

$specifiedTag = '';
if (isset($_GET['tag'])) {
  $specifiedTag = $_GET['tag'];
}

$skipWeekend = true;
if (isset($_GET['doweekend'])) {
  $skipWeekend = false;
}

$dayOfWeek = date("w");
if (!$simulate && ($dayOfWeek==0 || $dayOfWeek==6) && $skipWeekend) {
  mailIt("Skipping on the weekend",$to_email,$to_email);
  die("<p>Skipping on the weekend</p>");
}

error_log("Seems ok. Starting!");

// CREATE COMM OBJECTS
$pocket = new Pocket($pocketConsumerKey,$pocketAccessToken,'action',$simulate,$pocketSuffix);
$slack  = new Slack($slackToken,$simulateChannel,$simulate);

// START THE EMAIL REPORT VAR
$out = r($simulate,'simulate');
$out .= r($specifiedTag,'specified tag');
$out .= r($_SERVER['SERVER_NAME'],'server');
$out .= r($channelMap,'map');

// LOOP THROUGH THE MAP, STARTING WITH THE (POCKET) TAGS
$thePosts = array();
foreach ($channelMap as $tag=>$channel) {

  if ($specifiedTag!='') {
    if ($tag!=$specifiedTag) {
      $out .= "<p>Skipped ".$tag."</p>";
      continue;
    }
  }
  // $out .= r($tag);
  echo "<p>Connecting to Pocket, looking for ".$tag."</p>";
  $posts = $pocket->getAPost($tag);
  if (!empty($posts->list)) {
    foreach ($posts->list as $post) {
      $post->tag = $tag;
      $post->channel = $channel;
      $thePosts[] = $post;
    }
  }
}

if (!empty($thePosts)) {
  $out .= r($thePosts);
  $numberPosts = count($thePosts);
  $out .= $numberPosts;
  $thePostIndex = rand(0,$numberPosts-1);
  $thePost = $thePosts[$thePostIndex];
  $out .= r($thePost);
  postAPost($thePost);
  // foreach ($thePosts as $aPost) {
  //
  //   $postAPost($aPost);
  //
  // } // THEPOSTS
}


echo "<p>Completed all the tags.</p>";

if (isset($config->sendgrid_api_key)) {
  $apiKey = $config->sendgrid_api_key;
} else {
  $apiKey = getenv('SENDGRID_API_KEY');
}
if (!$apiKey) {
  die("No sendgrid key!");
}

if (isset($config->to_email)) {
  $toEmail = $config->to_email;
} else {
  $toEmail = getenv('TO_EMAIL');
}

if (!$toEmail) {
  die ("No to email address.");
}

echo "<p>emails going to ".$toEmail."</p>";

mailIt($out,$toEmail,$toEmail,$apiKey);

function postAPost($aPost) {
  global $slack,$pocket,$out;
  // BUILD THE POST TEXT
  $excerpt = $aPost->excerpt;
  $url = $aPost->resolved_url;
  $loc = strpos($url,"?");
  if ($loc) {
    $url = substr($url,0,$loc);
  }
  $title = $aPost->resolved_title;
  $id = $aPost->item_id;
  $text = $url;
  // $text = "*".$title."*\n".$excerpt."\n<".$url."> ";
  // r($excerpt,"excerpt");

  // POST
  $ttt = "<p>Posting ".$title." (".$text.") TO ".$aPost->channel." (from ".$aPost->tag.")</p>";
  echo $ttt;
  $out .= $ttt;

  echo "<p>Connecting to Slack to post in the ".$aPost->channel." channel</p>";
  $response = $slack->postTextToChannel($text,$aPost->channel);
  echo "<p>Back from Slack...".$response."</p>";
  // $out .= r($response,$channel);
  echo "<p>Untagging...</p>";
  $response = $pocket->untagPost($id,$aPost->tag);
  // $out .= r($response,'UNTAGGED');
  echo "<p>Completed slack posting for this tag.</p>";

}

?>
