<?php

$config = new stdClass;

$config->map = array();
$config->map['POCKET_TAG_NAME']='SLACK_CHANNEL_NAME'; // NOTE: don't need "#" in front
//$map['OTHER POCKET_TAG_NAME']='OTHER_DESTINATION_SLACK_CHANNEL_NAME';

$config->suffix = "-posted"; // not used right now but maybe

$config->simulate_channel = ''; // where you want your simulate slack messages to go. Use "@username" to send to yourself.

$config->slack_token = ''; // Could put this in ENV vars, too

$config->pocket_consumer_key = ''; // Could put this in ENV vars, too
$config->pocket_access_token = ''; // Could put this in ENV vars, too

$config->heroku_subdomain = ''; // really needed??

$config->sendgrid_api_key = ''; // Could put this in ENV vars, too

$config->to_email = ''; // where email reports are sent

?>
