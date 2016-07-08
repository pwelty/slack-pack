<?php

function r($a,$l='') {
  if (is_array($a) || is_object($a)) {
    if ($l) {
      echo "<p>".$l."=</p><pre>".print_r($a,TRUE)."</pre>";
    } else {
      echo "<pre>".print_r($a,TRUE)."</pre>";
    }
    return "<pre>".print_r($a,TRUE)."</pre>";
  } else {
    if ($l) {
      echo "<p>".$l."=</p><p>".$a."</p>";
    } else {
      echo "<p>".$a."</p>";
    }
    return "<p>".$a."</p>";
  }
}

function mailIt($html) {
  $from = new SendGrid\Email(null, "paul.welty@sparksgrove.com");
  $subject = "Hello World from the SendGrid PHP Library";
  $to = new SendGrid\Email(null, "ponch@paulwelty.com");
  $content = new SendGrid\Content("text/html", "<html>".$html."</html");
  $mail = new SendGrid\Mail($from, $subject, $to, $content);
  $apiKey = getenv('SENDGRID_API_KEY');
  $sg = new \SendGrid($apiKey);

  $response = $sg->client->mail()->send()->post($mail);
  echo $response->statusCode();
  echo $response->headers();
  echo $response->body();
}

?>
