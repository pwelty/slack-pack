<?php

class Slack {

  private $token = '';
  private $team = '';
  private $simulate_channel = '';

  public function postTextToChannel($text='test',$channel,$simulate=false) {
    $url = 'https://slack.com/api/chat.postMessage';
    $vars = array();
    $vars['text'] = $text;
    $vars['as_user'] = 'true';
    if ($simulate) {
      $vars['channel'] = $this->simulate_channel;
    } else {
      $vars['channel'] = $channel;
    }
    $vars['token'] = $this->token;
    r($vars);
    $response = $this->get_something($url,$vars);
    return $response;
  }

  function __construct($token,$team,$simulate_channel) {
    $this->token = $token;
    $this->team = $team;
    $this->simulate_channel = $simulate_channel;
  }

  private function get_something($url,$vars) {
    $args = array();
    foreach ($vars as $k=>$v) {
      $args[]=$k.'='.urlencode($v);
    }
    $arg_string = implode($args,"&");
    return file_get_contents($url."?".$arg_string);
  }

  // private function post_something($url,$vars,$debug=false) {
	// 	$headers = array();
	// 	$headers['Content-Type'] = 'application/json';
	// 	// $headers['X-Accept'] = 'application/json';
	// 	$_headers = array();
	// 	foreach($headers as $k=>$v){
	// 		$_headers[] = $k.": ".$v;
	// 	}
  //
	// 	$post_data = json_encode($vars);
  //
	// 	// foreach($vars as $key=>$value) { $post_data .= $key.'='.$value.'&'; }
	// 	// $post_data = rtrim($post_data, '&');
  //
	// 	if ($debug) {
 // 			$this->r($post_data);
	// 	}
  //
	//   $options = array(
	// 		CURLOPT_RETURNTRANSFER => true,     // return web page
	// 		// CURLOPT_HEADER         => true,
	// 		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	// 		CURLOPT_ENCODING       => "UTF8",       //
	// 		CURLOPT_USERAGENT      => "Slack Pack", // who am i
	// 		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	// 		CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	// 		CURLOPT_TIMEOUT        => 120,      // timeout on response
	// 		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	// 		CURLOPT_POST 		   	   => 1,
	// 		CURLOPT_POSTFIELDS     => $post_data,
	// 		CURLOPT_HTTPHEADER	   => $_headers,
	// 		CURLINFO_HEADER_OUT	   => true,
	//   );
  //
	//   $ch = curl_init($url);
	//   curl_setopt_array($ch, $options);
	// 	$response = curl_exec($ch);
  //
	// 	if(curl_error($ch)) {
	// 	  echo 'error:' . curl_error($ch);
	// 	}
  //
	// 	if ($debug) {
	// 		$this->r($response,"response");
	// 		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// 		// $headersSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
	// 		// $headersSent = str_replace("\n", "<br>", $headersSent);
	// 		echo "result=".$result."<br>";
	// 		echo "http=".$http_code."<br>";
	// 		// echo "headersSent=".$headersSent."<br>";
	// 	}
	// 	curl_close($ch);
	// 	$response = json_decode($response);
	// 	return $response;
	// }
  //
  // private function r($a,$l='') {
	// 	if ($l) {
	// 		echo $l."=<pre>".print_r($a,TRUE)."</pre>";
	// 	} else {
	// 		echo "<pre>".print_r($a,TRUE)."</pre>";
	// 	}
	// }


}

?>
