<?php

class Pocket {
	private $consumer_key = '';
	private $access_token = '';

	function untagPost($id,$tag,$simulate=false) {
		$endpoint = 'https://getpocket.com/v3/send';
		$vars = array();
		$vars['consumer_key'] = $this->consumer_key;
		$vars['access_token'] = $this->access_token;
		$vars['actions'] = array();
		$action1 = array();
		$action1['action'] = 'tags_remove';
		$action1['item_id'] = $id;
		$action1['tags'] = $tag;
		$action2 = array();
		$action2['action'] = 'tags_add';
		$action2['item_id'] = $id;
		$action2['tags'] = $tag.'-posted';
		$vars['actions'][] = $action1;
		$vars['actions'][] = $action2;
		if ($simulate) {
			return $vars;
		} else {
			return $this->post_something($endpoint,$vars);
		}
	}

	public function getAPost($tag='sg-slack-general',$debug=false) {
		$endpoint = 'https://getpocket.com/v3/get';
		$vars = array();
		$vars['consumer_key'] = $this->consumer_key;
		$vars['access_token'] = $this->access_token;
		$vars['tag'] = $tag;
		$vars['count'] = '1';
		$vars['sort'] = 'newest';
		// $vars['detailType'] = 'complete';
		$vars['detailType'] = 'simple';
		$response = $this->post_something($endpoint,$vars,$debug);
		// $this->r($response);
		return $response;
	}

	function __construct($consumer_key,$access_token,$action='') {
		$this->consumer_key = $consumer_key;
		$this->access_token = $access_token;
		if ($action=='authorized') {
			echo ("back");
			$code=$_GET['code'];
			$endpoint = 'https://getpocket.com/v3/oauth/authorize';
			$vars = array();
			$vars['consumer_key'] = $this->consumer_key;
			$vars['code'] = $code;
			$response = $this->post_something($endpoint,$vars,true);
			echo "put this access token in the env vars";
			$this->r($response);
		} elseif($action=='pocket-auth') {
			// Connect to Pocket and get a token
			$vars = array();
			$vars['consumer_key'] = $this->consumer_key;
			$vars['redirect_uri'] = 'http://'.$_SERVER['HTTP_HOST'].'/pocket-authorized.php';
			$endpoint = 'https://getpocket.com/v3/oauth/request';
			$response = $this->post_something($endpoint,$vars);
			// echo ("end 1");
			$this->r($response);
			$code = $response->code;
			// echo ($code);
			// exit;
			// $this->code = $code;
			$redirect_uri = htmlentities($vars['redirect_uri'].'?code='.$code);
			echo $redirect_uri;
			$new_url = 'https://getpocket.com/auth/authorize?request_token='.$code.'&redirect_uri='.$redirect_uri;
			r($new_url);
			exit;
			header('Location: '.$new_url);
			//echo ("end 2");
			//$this->r($response);
		} elseif ($action=='get') {
			$endpoint = 'https://getpocket.com/v3/get';
			$vars = array();
			// $vars['consumer_key'] = getenv('POCKET_CONSUMER_KEY');
			// $vars['access_token'] = getenv('POCKET_ACCESS_TOKEN');
			$vars['tag'] = 'sg-slack';
			$vars['count'] = '5';
			$vars['sort'] = 'newest';
			// $vars['detailType'] = 'complete';
			$vars['detailType'] = 'simple';
			$response = $this->post_something($endpoint,$vars);
			$this->r($response);
		} else {

		}
	}

	private function post_something($url,$vars,$debug=false) {
		$headers = array();
		$headers['Content-Type'] = 'application/json; charset=UTF8';
		$headers['X-Accept'] = 'application/json';
		$_headers = array();
		foreach($headers as $k=>$v){
			$_headers[] = $k.": ".$v;
		}

		$post_data = json_encode($vars);

		// foreach($vars as $key=>$value) { $post_data .= $key.'='.$value.'&'; }
		// $post_data = rtrim($post_data, '&');

		if ($debug) {
 			$this->r($post_data);
		}

	  $options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			// CURLOPT_HEADER         => true,
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "UTF8",       //
			CURLOPT_USERAGENT      => "Slack Pack", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_POST 		   	   => 1,
			CURLOPT_POSTFIELDS     => $post_data,
			CURLOPT_HTTPHEADER	   => $_headers,
			CURLINFO_HEADER_OUT	   => true,
	  );

	  $ch = curl_init($url);
	  curl_setopt_array($ch, $options);
		$response = curl_exec($ch);

		if(curl_error($ch)) {
		  echo 'error:' . curl_error($ch);
		}

		if ($debug) {
			$this->r($response,"response");
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			// $headersSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
			// $headersSent = str_replace("\n", "<br>", $headersSent);
			echo "result=".$result."<br>";
			echo "http=".$http_code."<br>";
			// echo "headersSent=".$headersSent."<br>";
		}
		curl_close($ch);
		$response = json_decode($response);
		return $response;
	}

	private function r($a,$l='') {
		if ($l) {
			echo $l."=<pre>".print_r($a,TRUE)."</pre>";
		} else {
			echo "<pre>".print_r($a,TRUE)."</pre>";
		}
	}

} // Pocket

?>
