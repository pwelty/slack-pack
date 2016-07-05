<?php

function r($a,$l='') {
  if ($l) {
    echo $l."=<pre>".print_r($a,TRUE)."</pre>";
  } else {
    echo "<pre>".print_r($a,TRUE)."</pre>";
  }
}

?>
