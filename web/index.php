<?php

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

require('../vendor/autoload.php');

require_once 'helpers.php';
require_once 'pocket.class.php';
require_once 'slack.class.php';

if (isset($_GET['live'])) {
  $simulate = false;
} else {
  $simulate = true;
}

$to_email = getenv('TO_EMAIL');
echo "email=".$to_email;

$dayOfWeek = date("w");
if (!$simulate && ($dayOfWeek==0 || $dayOfWeek==6)) {
  mailIt("Skipping on the weekend",$to_email,$to_email);
  echo "Skipping on the weekend";
  exit;
}

// GET ENV VARS. FOR LOCAL USING .HTACCESS FILE
$slack_token = getenv('SLACK_TOKEN');
$pocket_consumer_key = getenv("POCKET_CONSUMER_KEY");
$pocket_access_token = getenv("POCKET_ACCESS_TOKEN");
$simulate_channel = getenv("SIMULATE_CHANNEL");

// CREATE COMM OBJECTS
$pocket = new Pocket($pocket_consumer_key,$pocket_access_token);
$slack = new Slack($slack_token,$simulate_channel);

// IMPORT THE POCKET->SLACK CHANNELS MAP
require_once('config.php');

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
      $url = substr($url,0,strlen($url)-$loc);
      $title = $aPost->resolved_title;
      $id = $aPost->item_id;
      $text = $url;
      // $text = "*".$title."*\n".$excerpt."\n<".$url."> ";
      // r($excerpt,"excerpt");

      // POST
      $response = $slack->postTextToChannel($text,$channel,$simulate);
      $out .= r($response,$channel);
      $response = $pocket->untagPost($id,$tag,$simulate);
      $out .= r($response,'UNTAGGED');

    } // THEPOSTS

  }

} // MAP

mailIt($out,$to_email,$to_email);

?>
