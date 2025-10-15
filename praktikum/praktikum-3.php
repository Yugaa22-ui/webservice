<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "webservice");
if ($conn->connect_error) {
    die(json_encode([
        "status"    => "error",
        "message"   => "Connection failed"
    ]));
}

$method = $_SERVER["REQUEST_METHOD"];
switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            getDetailProvince($conn, $_GET['id']);
        }
        else {
            getAllProvince($conn);
        }
        break;

    default:
        echo json_encode([
            "status"    => "error",
            "message"   => "Method not allowed"
        ]);
        break;
}

function getAllProvince($conn) {
    $query = $conn->query("SELECT * FROM province");

    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status"    => "success",
        "results"      => $data
    ]);
}

function getDetailProvince($conn, $id) {
    $query = $conn->query("SELECT * FROM province WHERE province_id = $id");
    $data = $query->fetch_assoc();

    echo json_encode([
        "status"    => "success",
        "results"      => $data
    ]);
}