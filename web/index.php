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

if (!$pocketConsumerKey) {
  die("You need to put the Pocket consumer key in the env vars or the config file before we can do anything.");
}

if ($pocketConsumerKey && !$pocketAccessToken) {
    header('Location: /pocket-auth.php');
    exit;
  }

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

$simulate = true;
if (isset($_GET['live'])) {
  $simulate = false;
}

$skipWeekend = true;
if (isset($_GET['doweekend'])) {
  $skipWeekend = false;
}

$dayOfWeek = date("w");
if (!$simulate && ($dayOfWeek==0 || $dayOfWeek==6) && $skipWeekend) {
  mailIt("Skipping on the weekend",$to_email,$to_email);
  echo "<p>Skipping on the weekend</p>";
  exit;
}

// CREATE COMM OBJECTS
$pocket = new Pocket($pocketConsumerKey,$pocketAccessToken,'action',$simulate,$pocketSuffix);
$slack = new Slack($slackToken,$simulateChannel,$simulate);

// START THE EMAIL REPORT VAR
$out = r($simulate,'simulate');

// LOOP THROUGH THE MAP, STARTING WITH THE (POCKET) TAGS
foreach ($config->map as $tag=>$channel) {
  $out .= r($tag);
  echo "<p>Connecting to Pocket, looking for ".$tag."</p>";
  $posts = $pocket->getAPost($tag);
  $thePosts = $posts->list;
  $out .= r($thePosts);

  if (!empty($thePosts)) {

    foreach ($thePosts as $aPost) {

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
      echo "<p>Posting...".$text." TO ".$channel."</p>";
      echo "<p>Connecting to Slack</p>";
      $response = $slack->postTextToChannel($text,$channel);
      echo "<p>Back from Slack...".$response."</p>";
      $out .= r($response,$channel);
      echo "<p>Untagging...</p>";
      $response = $pocket->untagPost($id,$tag);
      $out .= r($response,'UNTAGGED');
      echo "<p>Completed slack posting for this tag.</p>";

    } // THEPOSTS

  }

} // MAP

echo "<p>Completed all the tags.</p>";

if (isset($config->sendgrid_api_key)) {
  $apiKey = $config->sendgrid_api_key;
} else {
  $apiKey = getenv('SENDGRID_API_KEY');
}
if (!apiKey) {
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

?>
