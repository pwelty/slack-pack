<?php

require('../vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \PWelty\PocketPoll\Pocket;

$log = new Logger('my_logger');
$log->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
$log->addInfo('Starting app',array('starting'=>'whatevs'),'extra');

require_once 'helpers.php';
// require_once 'pocket.class.php';
require_once 'slack.class.php';


// IMPORT THE CONFIG FILE, ESP. THE POCKET->SLACK CHANNELS MAP
if (file_exists('config.php')) {
	$log->addInfo('Using local config.php file.');
	include_once 'config.php';
} else {
	$log->addWarning('Didn\'t find config file. Hope there are some env vars!');
}

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

if (!$pocketConsumerKey) {
  die("You need to put the Pocket consumer key in the env vars or the config file before we can do anything.");
}

if (!$pocketAccessToken) {
	die("No Pocket access token!");
}

if (!$slackToken) {
	die("No Slack token!");
}

if (!$simulateChannel) {
	die("No simulation channel!");
}

if (!$pocketSuffix) {
	die("No Pocket suffix!");
}

if (!$channelMap) {
	die("No channel map!");
}



if ($pocketConsumerKey && !$pocketAccessToken) {
  header('Location: /pocket-auth.php');
  $log->addInfo("Redirecting to get Pocket access token");
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

$log->addInfo("Seems ok. Starting!");

// CREATE COMM OBJECTS
// $pocket = new Pocket($pocketConsumerKey,$pocketAccessToken,'action',$simulate,$pocketSuffix);
$pocket = new Pocket($pocketConsumerKey,$pocketAccessToken,$simulate);

// $result = $pocket->getPosts('sg-general-posted','1','oldest','complete');
// $list = $result->list;
// print_r($list);
// exit;


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

  $posts = $pocket->getPosts($tag,0);

  if (!empty($posts->list)) {
    foreach ($posts->list as $post) {
      $post->tag = $tag;
      $post->channel = $channel;
      $thePosts[] = $post;
    }
  }
}

function cmp($a, $b) {
  return $a->time_added < $b->time_added;
}
usort($thePosts, "cmp");



if (!empty($thePosts)) {

//  $out .= r($thePosts);

  $numberPosts = count($thePosts);
  $out .= "<p>".$numberPosts . " posts</p>";

	foreach ($thePosts as $aPost) {
		$out .= "<p>".$aPost->resolved_title . "(".date("m/d/Y",$aPost->time_added).") [".$aPost->channel."]</p>";
	}

//   $thePostIndex = rand(0,$numberPosts-1);
  $thePostIndex = 0;
  $thePost = $thePosts[$thePostIndex];
  $out .= r($thePost);
  $out .= r(date("r",$thePost->time_added));
  postAPost($thePost);
  // foreach ($thePosts as $aPost) {
  //
  //   $postAPost($aPost);
  //
  // } // THEPOSTS
}


echo "<p>Completed all the tags.</p>";

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
  echo "<p>To be specific ... " . $id . " - " . $aPost->tag.$pocketSuffix . "</p>";
  $response = $pocket->tagPost($id,$aPost->tag.$pocketSuffix);
  $response = $pocket->untagPost($id,$aPost->tag);
  // $out .= r($response,'UNTAGGED');
  echo "<p>Completed slack posting for this tag.</p>";

}

?>
