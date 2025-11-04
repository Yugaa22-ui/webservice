<?php
include_once("config.php");

$method = $_SERVER["REQUEST_METHOD"];
requireHeaders($method);

switch ($method) {
    case "GET":
        if (isset($_GET["id"])) getTopic($conn, $_GET["id"]);
        else getAllTopic($conn);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createTopic($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        updateTopic($conn, $_GET["id"], $data);
        break;

    case "DELETE":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        deleteTopic($conn, $_GET["id"]);
        break;

    default:
        jsonResponse(405, "error", "Method not allowed");
        break;
}

// =======================
// CRUD Functions
// =======================
function getAllTopic($conn) {
    $query = $conn->query("SELECT * FROM topic");
    $data = [];
    while ($row = $query->fetch_assoc()) $data[] = $row;
    jsonResponse(200, "success", "", $data);
}

function getTopic($conn, $id) {
    $query = $conn->query("SELECT * FROM topic WHERE topic_id = '$id'");
    $data = $query->fetch_assoc();
    jsonResponse(200, "success", "", $data);
}

function createTopic($conn, $data) {
    if (empty($data["topic_name"])) {
        jsonResponse(400, "error", "Field 'topic_name' is required");
        return;
    }
    $name = $conn->real_escape_string($data["topic_name"]);
    $conn->query("INSERT INTO topic (topic_name) VALUES ('$name')");
    jsonResponse(201, "success", "Topic added successfully");
}

function updateTopic($conn, $id, $data) {
    if (empty($data["topic_name"])) {
        jsonResponse(400, "error", "Field 'topic_name' is required");
        return;
    }
    $name = $conn->real_escape_string($data["topic_name"]);
    $conn->query("UPDATE topic SET topic_name='$name' WHERE topic_id='$id'");
    jsonResponse(200, "success", "Topic updated successfully");
}

function deleteTopic($conn, $id) {
    $conn->query("DELETE FROM topic WHERE topic_id='$id'");
    jsonResponse(200, "success", "Topic deleted successfully");
}
?>
