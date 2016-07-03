<?php

require_once 'helpers.php';
require_once 'pocket.class.php';

// Establish connection to Pocket
$action = $_GET['action'];
$pocket = new Pocket($action);

//


?>
