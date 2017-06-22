<?php
/**
 * Created by PhpStorm.
 * User: kid-dorr
 * Date: 6/14/2017
 * Time: 10:38 AM
 */

$get_login_url = "https://www.000webhost.com/cpanel-login";
$post_login_url = "https://www.000webhost.com/cpanel-login";

// get login page

$curlOptions = array(
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_VERBOSE => TRUE,
    CURLOPT_STDERR => $verbose = fopen('php://temp', 'rw+'),
    CURLOPT_FILETIME => TRUE,
);

$url = $get_login_url;
$handle = curl_init($url);
curl_setopt_array($handle, $curlOptions);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
$content = curl_exec($handle);
//echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
//print_r($content);

preg_match("/<input type=\"hidden\" name=\"csrf_name\" value=\"([a-z0-9]+)\">/", $content, $output_array);
echo $output_array[1];
preg_match("/<input type=\"hidden\" name=\"csrf_value\" value=\"([a-z0-9]*)\">/", $content, $output1_array);
echo $output1_array[1];
//print_r($content);
curl_close($handle);
//$doc = new DOMDocument();
//$doc->loadHTML($content);
//
//print_r($doc);


