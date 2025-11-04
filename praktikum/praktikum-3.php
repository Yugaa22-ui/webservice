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

    if ($method == "POST" || $method == "PUT") {
        if (stripos($headers["content-type"] ?? "", "application/json") !== 0) {
            jsonResponse(415, "error", "Unsupported Media Type: 'Content-Type: application/json'");
            exit;
        }
    }
}

function jsonResponse($code, $status, $message = "", $data = []) {
    http_response_code($code);
    $resp = ['status' => $status];

    if ($message !== "") {
        $resp['message'] = $message;
    }

    if (!empty($data)) {
        $resp['data'] = $data;
    }

    echo json_encode($resp);
}

// =======================
// Database Connection
// =======================
try {
    $conn = new mysqli("localhost", "root", "", "webservice");
} catch (mysqli_sql_exception $e) {
    jsonResponse(500, "error", "Database connection failed: " . $e->getMessage());
    die;
}

// =======================
// Routing
// =======================
$method = $_SERVER["REQUEST_METHOD"];
requireHeaders($method);

switch ($method) {
    case "GET":
        if (isset($_GET["id"])) {
            getDetailProvince($conn, $_GET["id"]);
        } else {
            getAllProvince($conn);
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createProvince($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        updateProvince($conn, $_GET["id"], $data);
        break;

    case "DELETE":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        deleteProvince($conn, $_GET["id"]);
        break;

    default:
        jsonResponse(405, "error", "Method not allowed");
        break;
}

// =======================
// Data Operation
// =======================
function getAllProvince($conn) {
    $query = $conn->query("SELECT * FROM province");
    $data = [];

    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    jsonResponse(200, "success", "", $data);
}

function getDetailProvince($conn, $id) {
    $query = $conn->query("SELECT * FROM province WHERE province_id = '$id'");
    $data = $query->fetch_assoc();

    jsonResponse(200, "success", "", $data);
}

function createProvince($conn, $data) {
    if (!isset($data["name"]) || empty($data["name"])) {
        jsonResponse(400, "error", "Field 'name' is required");
        return;
    }

    $name = $data["name"];
    $conn->query("INSERT INTO province (province_name) VALUES ('$name')");

    jsonResponse(201, "success", "Province added successfully");
}

function updateProvince($conn, $id, $data) {
    if (!isset($data["name"]) || empty($data["name"])) {
        jsonResponse(400, "error", "Field 'name' is required");
        return;
    }

    $name = $data["name"];
    $conn->query("UPDATE province SET province_name = '$name' WHERE province_id = '$id'");

    jsonResponse(200, "success", "Province updated successfully");
}

function deleteProvince($conn, $id) {
    $conn->query("DELETE FROM province WHERE province_id = '$id'");
    jsonResponse(200, "success", "Province deleted successfully");
}
?>
