<?php

require_once 'helpers.php';
require_once 'pocket.class.php';

$key = getenv('POCKET_CONSUMER_KEY');
$pocket = new Pocket($key,'','authorized');

?>
