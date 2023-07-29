<?php
$prop_id = 903;
$user_id = 598;

$arrayp = array(
    'domain' => "hassan",
    'min_price' => "",
    'max_price' => "",
    'type' => "",
    'status' => "",
    'bedrooms' => "",
    'bathrooms' => "",
    'sortby' => "",
    'min_area' => "",
    'max_area' => "",
    'search_location' => "",
    'limit' => 10,
    'start' => 0
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://dev.propertysalers.com/sapi/searchproperty');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$post = $arrayp;
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$d = json_decode($result,true);
// $property = $d['properties'];
// $s = $d['start_with_no'];
// $nF = $d['no_fetched'];
// $tnp = $d['total_no_properties'];

print_r($result);