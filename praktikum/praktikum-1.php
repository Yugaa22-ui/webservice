<?php 
$request = curl_init();

curl_setopt_array($request, array(
    CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/district/1",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "key: rkgh9W1v22077473cf283aceutatPEOR"
    ),
    CURLOPT_RETURNTRANSFER => true,
));

$response = curl_exec($request);
curl_close($request);

$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";
?>