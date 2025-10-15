<?php
$request = curl_init();

curl_setopt_array($request, array(
    CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => array(
        "key: rkgh9W1v22077473cf283aceutatPEOR",
        "content-type: application/x-www-form-urlencoded",
    ),
    CURLOPT_POSTFIELDS => http_build_query([
        "origin" => "2",
        "destination" => "3",
        "weight" => 1000,
        "courier" => "pos:jne:jnt:sicepat",
        "price" => "lowest"
    ]),
    CURLOPT_RETURNTRANSFER => true,
));

$response   = curl_exec($request);
$error      = curl_error($request);

curl_close($request);

if ($error) {
    echo $error;
}
else {
    $data = json_decode($response, true);

    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

?>