<?php

require_once 'helpers.php';
require_once 'pocket.class.php';
include_once 'config.php';

if (isset($config->pocket_consumer_key)) {
  $key = $config->pocket_consumer_key;
} else {
  $key = getenv('POCKET_CONSUMER_KEY');
}
$pocket = new Pocket($key,'','pocket-auth',false);

?>
