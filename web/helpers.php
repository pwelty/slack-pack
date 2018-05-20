<?php

function r($aSomething,$aLabel='') {
  if ($aLabel) {
    $theLabel = "<b>".$aLabel."=</b>";
    //echo $someText;
    //$out .= $someText;
  } else {
    $theLabel = '';
  }
  if (is_array($aSomething) || is_object($aSomething)) {
    if ($aLabel) {
      $theLabel .= "<br/>";
    }
    $someText = "<p>".$theLabel."<pre>".print_r($aSomething,TRUE)."</pre></p>";
  } else {
    $someText = "<p>".$theLabel."<b>".$aSomething."</b></p>";
  }
  echo $someText;
  return $someText;
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
