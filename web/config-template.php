<?php

// CREATE (POCKET) TAG TO (SLACK) CHANNEL MAP
$map = array();
$channels = array();

$channels[] = 'DESTINATION_SLACK_CHANNEL_NAME';
$map['POCKET_TAG_NAME']=$channels;

$channels[] = 'OTHER_DESTINATION_SLACK_CHANNEL_NAME';
$map['OTHER POCKET_TAG_NAME']=$channels;

// $channels[] = 'ai';
// $map['sg-ai']=$channels;

// $channels[] = 'sparks_grove_creative';
// $map['sg-creative']=$channels;

?>
