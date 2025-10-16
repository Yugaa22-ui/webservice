<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "webservice");
if ($conn->connect_error) {
    die(json_encode([
        "status"  => "error",
        "message" => "Connection failed"
    ]));
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            getDetailCity($conn, $_GET['id']);
        } else {
            getAllCity($conn);
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createCity($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"  => "error",
                "message" => "ID kota harus diisi"
            ]);
            exit();
        }

        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);

        // Jika JSON gagal terbaca, coba baca data urlencoded
        if ($data === null) {
            parse_str($raw, $data);
        }

        updateCity($conn, $_GET['id'], $data);
        break;

        
    case "DELETE":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"  => "error",
                "message" => "ID kota harus diisi"
            ]);
            exit();
        }

        deleteCity($conn, $_GET['id']);
        break;

    default:
        echo json_encode([
            "status"  => "error",
            "message" => "Method not allowed"
        ]);
        break;
}

function getAllCity($conn) {
    $query = $conn->query("SELECT city.*, province.province_name FROM city INNER JOIN province ON city.province_id = province.province_id");
    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status"  => "success",
        "results" => $data
    ]);
}

function getDetailCity($conn, $id) {
    $query = $conn->query("SELECT city.*, province.province_name FROM city INNER JOIN province ON city.province_id = province.province_id WHERE city_id = $id");
    $data = $query->fetch_assoc();

    echo json_encode([
        "status"  => "success",
        "results" => $data
    ]);
}

function createCity($conn, $data) {
    if (!isset($data['name']) || empty($data['name']) || !isset($data['province_id'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama kota dan ID provinsi harus diisi"
        ]);
        return;
    }

    $name = $data['name'];
    $province_id = $data['province_id'];
    $conn->query("INSERT INTO city (city_name, province_id) VALUES ('$name', $province_id)");

    echo json_encode([
        "status"  => "success",
        "message" => "City added successfully"
    ]);
}

function updateCity($conn, $id, $data) {
    if (!isset($data['name']) || empty($data['name']) || !isset($data['province_id'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama kota dan ID provinsi harus diisi"
        ]);
        return;
    }

    $name = $data['name'];
    $province_id = $data['province_id'];
    $conn->query("UPDATE city SET city_name = '$name', province_id = $province_id WHERE city_id = $id");

    echo json_encode([
        "status"  => "success",
        "message" => "City updated successfully"
    ]);
}

function deleteCity($conn, $id) {
    $conn->query("DELETE FROM city WHERE city_id = $id");

    echo json_encode([
        "status"  => "success",
        "message" => "City deleted successfully"
    ]);
}
?>
