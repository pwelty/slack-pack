<?php

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

// IMPORT THE CONFIG FILE, ESP. THE POCKET->SLACK CHANNELS MAP
require_once 'config.php';

// GET ENV VARS. FOR LOCAL USING .HTACCESS FILE
$simulateChannel = getenv("SIMULATE_CHANNEL");

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

if ($pocketConsumerKey) {
  // Ok, with the key we can do something
  if (!$pocketAccessToken) {
    header('Location: /pocket-auth.php');
    exit;
  }
} else {
  die("You need to put the Pocket consumer key in the env vars or the config file before we can do anything.");
}

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

print_r(var_dump(get_defined_vars()));

exit;

$simulate = true;
if (isset($_GET['live'])) {
  $simulate = false;
}

$toEmail = getenv('TO_EMAIL');
echo "emails going to ".$toEmail;

$dayOfWeek = date("w");
if (!$simulate && ($dayOfWeek==0 || $dayOfWeek==6)) {
  mailIt("Skipping on the weekend",$to_email,$to_email);
  echo "Skipping on the weekend";
  exit;
}

// CREATE COMM OBJECTS
$pocket = new Pocket($pocketConsumerKey,$pocketAccessToken,'action',$simulate);
$slack = new Slack($slackToken,$simulateChannel,$simulate);

// START THE EMAIL REPORT VAR
$out = r($simulate,'simulate');

// LOOP THROUGH THE MAP, STARTING WITH THE (POCKET) TAGS
foreach ($map as $tag=>$channel) {
  $out .= r($tag);
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
      $response = $slack->postTextToChannel($text,$channel);
      $out .= r($response,$channel);
      $response = $pocket->untagPost($id,$tag);
      $out .= r($response,'UNTAGGED');

    } // THEPOSTS

  }

} // MAP

mailIt($out,$toEmail,$toEmail);

?>
