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
        break;
    
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createProvince($conn, $data);
        break;
    
    case "PUT":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"    => "error",
                "message"   => "ID provinsi harus diisi"
            ]);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);
        updateProvince($conn, $_GET['id'], $data);
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"    => "error",
                "message"   => "ID provinsi harus diisi"
            ]);
            exit();
        }

        deleteProvince($conn, $_GET['id']); // <-- Ini perbaikannya
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

function createProvince($conn, $data) {
    if (!isset($data['name']) || empty($data['name'])) {
        echo json_encode([
            "status"    => "error",
            "message"   => "Nama provinsi harus diisi"
        ]);
        return;

    }

    $name = $data['name'];
    $conn->query("INSERT INTO province (province_name) VALUES ('$name')");

    echo json_encode([
        "status"    => "success",
        "message"   => "Province added successfully"
    ]);
}

function updateProvince($conn, $id, $data) {
    if (!isset($data['name']) || empty($data['name'])) {
        echo json_encode([
            "status"    => "error",
            "message"   => "Nama provinsi harus diisi"
        ]);
        return;

    }

    $name = $data['name'];
    $conn->query("UPDATE province SET province_name = '$name' WHERE province_id = $id");

    echo json_encode([
        "status"    => "success",
        "message"   => "Province updated successfully"
    ]);
}

function deleteProvince($conn, $id) {
    $conn->query("DELETE FROM province WHERE province_id = $id");

    echo json_encode([
        "status"    => "success",
        "message"   => "Province deleted successfully"
    ]);
}