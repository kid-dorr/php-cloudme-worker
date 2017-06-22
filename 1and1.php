<?php
/**
 * Created by PhpStorm.
 * User: kid-dorr
 * Date: 6/21/2017
 * Time: 10:08 AM
 */


function login1and1 ($mailpass) {
    $mail = $mailpass['mail'];
    $pass = $mailpass['pass'];

    $login_url = 'https://account.1and1.com/';


    $login_json = http_build_query(array(
        '__sendingdata' => 1,
        'oaologin.password' => $mail,
        'oaologin.username' => $pass
    ));

    echo $login_json;


    $login_conn = curl_init($login_url);
    curl_setopt($login_conn, CURLOPT_POST, 1);
    curl_setopt($login_conn, CURLOPT_HEADER, array(
        'X-DevTools-Request-Id: 6964.5012',
        'X-DevTools-Emulate-Network-Conditions-Client-Id: 304187d8-3dae-45ed-be27-6b61c6ad8885',
        'Origin: https://www.1and1.com',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Referer: https://www.1and1.com/login?__lf=Static',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.8'
    ));
    curl_setopt($login_conn, CURLOPT_POSTFIELDS, $login_json);
    curl_setopt($login_conn, CURLOPT_VERBOSE, true);
    curl_setopt($login_conn, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($login_conn, CURLOPT_SSL_VERIFYPEER, false);

    $login_resp = curl_exec($login_conn);
    $resp_code = curl_getinfo($login_conn, CURLINFO_HTTP_CODE);
    curl_close($login_conn);
    var_dump($resp_code);

    echo $login_resp;

}

$mailpass = array(
    'mail' => '513327404',
    'pass' => 'Satsatsat1@'
);

login1and1($mailpass);