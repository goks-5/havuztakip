<?php

echo "deneme";
die;
//$URL='http://enerjiyonetim.com:2095/devices';
$URL='http://enerjiyonetim.com:2095/device/EOS_FCB200220001';
$token = "NqAzCxXSCkny1mlFP1XT";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$URL);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
$result=curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close ($ch);

echo $result;
