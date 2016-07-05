<?php

echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

require_once 'helpers.php';
require_once 'pocket.class.php';

// Establish connection to Pocket
// $action = $_GET['action'];
// $pocket = new Pocket($action);
$pocket = new Pocket;
$posts = $pocket->getAPost();
$thePosts = $posts->list;
foreach ($thePosts as $aPost) {
  // r($aPost);
  $excerpt = $aPost->excerpt;
  r($excerpt,"excerpt");
}


?>
