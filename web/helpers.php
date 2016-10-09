<?php

function r($anArray,$aLabel='') {
  $out = '';
  if ($aLabel) {
    $someText = "<p><b>".$aLabel."=</b></p>";
    echo $someText;
    $out .= $someText;
  }
  if (is_array($anArray) || is_object($anArray)) {
    $someText = "<p><pre>".print_r($anArray,TRUE)."</pre></p>";
  } else {
    $someText = "<p><b>".$anArray."</b></p>";
  }
  echo $someText;
  $out .= $someText;
  return $out;
}

function mailIt($html,$fromEmail,$toEmail,$key) {
  echo "<p>Trying SendGrid.</p>";
  $sendGridGoGo = new \SendGrid($key);
  $from = new SendGrid\Email(null,$fromEmail);
  $subject = "Slack Pack notification";
  $toEmailThing = new SendGrid\Email(null,$toEmail);
  $content = new SendGrid\Content("text/html", "<html>".$html."</html>");
  $mail = new SendGrid\Mail($from, $subject, $toEmailThing, $content);
  echo "<p>Connecting...</p>";
  $response = $sendGridGoGo->client->mail()->send()->post($mail);
  echo "<p>".$response->statusCode()."</p>";
  echo "<p>".$response->headers()."</p>";
  echo "<p>".$response->body()."</p>";
}

?>
