<?php
header("Content-Type: application/json");

function requireHeaders($method) {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);

    if (($headers["authorization"] ?? "") !== "Bearer xyz123") {
        jsonResponse(401, "error", "Unauthorized: header 'Authorization: Bearer xyz123'");
        exit;
    }

    if (stripos($headers["accept"] ?? "", "application/json") === false) {
        jsonResponse(406, "error", "Not Acceptable: 'Accept: application/json'");
        exit;
    }

    if (in_array($method, ["POST", "PUT"])) {
        if (stripos($headers["content-type"] ?? "", "application/json") !== 0) {
            jsonResponse(415, "error", "Unsupported Media Type: 'Content-Type: application/json'");
            exit;
        }
    }
}

function jsonResponse($code, $status, $message = "", $data = []) {
    http_response_code($code);
    $resp = ["status" => $status];

    if ($message !== "") $resp["message"] = $message;
    if (!empty($data)) $resp["data"] = $data;

    echo json_encode($resp);
}

try {
    $conn = new mysqli("localhost", "root", "", "utswebservice");
} catch (mysqli_sql_exception $e) {
    jsonResponse(500, "error", "Database connection failed: " . $e->getMessage());
    exit;
}
?>
