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
            getDetailDistrict($conn, $_GET['id']);
        } else {
            getAllDistrict($conn);
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createDistrict($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"  => "error",
                "message" => "ID district harus diisi"
            ]);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);
        updateDistrict($conn, $_GET['id'], $data);
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            echo json_encode([
                "status"  => "error",
                "message" => "ID district harus diisi"
            ]);
            exit();
        }

        deleteDistrict($conn, $_GET['id']);
        break;

    default:
        echo json_encode([
            "status"  => "error",
            "message" => "Method not allowed"
        ]);
        break;
}

function getAllDistrict($conn) {
    $query = $conn->query("SELECT district.*, city.city_name FROM district INNER JOIN city ON district.city_id = city.city_id");
    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status"  => "success",
        "results" => $data
    ]);
}

function getDetailDistrict($conn, $id) {
    $query = $conn->query("SELECT district.*, city.city_name FROM district INNER JOIN city ON district.city_id = city.city_id WHERE district_id = $id");
    $data = $query->fetch_assoc();

    echo json_encode([
        "status"  => "success",
        "results" => $data
    ]);
}

function createDistrict($conn, $data) {
    if (!isset($data['name']) || empty($data['name']) || !isset($data['city_id'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama district dan ID kota harus diisi"
        ]);
        return;
    }

    $name = $data['name'];
    $city_id = $data['city_id'];
    $conn->query("INSERT INTO district (district_name, city_id) VALUES ('$name', $city_id)");

    echo json_encode([
        "status"  => "success",
        "message" => "District added successfully"
    ]);
}

function updateDistrict($conn, $id, $data) {
    if (!isset($data['name']) || empty($data['name']) || !isset($data['city_id'])) {
        echo json_encode([
            "status"  => "error",
            "message" => "Nama district dan ID kota harus diisi"
        ]);
        return;
    }

    $name = $data['name'];
    $city_id = $data['city_id'];
    $conn->query("UPDATE district SET district_name = '$name', city_id = $city_id WHERE district_id = $id");

    echo json_encode([
        "status"  => "success",
        "message" => "District updated successfully"
    ]);
}

function deleteDistrict($conn, $id) {
    $conn->query("DELETE FROM district WHERE district_id = $id");

    echo json_encode([
        "status"  => "success",
        "message" => "District deleted successfully"
    ]);
}
?>
