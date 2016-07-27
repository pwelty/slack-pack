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
  $out .= $someText;
  return $out;
}

function mailIt($html,$fromEmail,$toEmail) {
  $from = new SendGrid\Email(null,$fromEmail);
  $subject = "Slack Pack notification";
  $toEmailThing = new SendGrid\Email(null,$toEmail);
  $content = new SendGrid\Content("text/html", "<html>".$html."</html>");
  $mail = new SendGrid\Mail($from, $subject, $toEmailThing, $content);
  $apiKey = getenv('SENDGRID_API_KEY');
  $sendGridGoGo = new \SendGrid($apiKey);
  $response = $sendGridGoGo->client->mail()->send()->post($mail);
  echo $response->statusCode();
  echo $response->headers();
  echo $response->body();
}

?>
