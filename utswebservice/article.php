<?php
include_once("config.php");

$method = $_SERVER["REQUEST_METHOD"];
requireHeaders($method);

switch ($method) {
    case "GET":
        if (isset($_GET["topic_id"])) getArticleByTopic($conn, $_GET["topic_id"]);
        elseif (isset($_GET["id"])) getArticle($conn, $_GET["id"]);
        else getAllArticle($conn);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createArticle($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        updateArticle($conn, $_GET["id"], $data);
        break;

    case "DELETE":
        if (!isset($_GET["id"])) {
            jsonResponse(400, "error", "Parameter 'id' is required");
            exit;
        }
        deleteArticle($conn, $_GET["id"]);
        break;

    default:
        jsonResponse(405, "error", "Method not allowed");
        break;
}

// =======================
// CRUD Functions
// =======================
function getAllArticle($conn) {
    $query = $conn->query("
        SELECT a.*, t.topic_name 
        FROM article a 
        JOIN topic t ON a.topic_id = t.topic_id
    ");
    $data = [];
    while ($row = $query->fetch_assoc()) $data[] = $row;
    jsonResponse(200, "success", "", $data);
}

function getArticleByTopic($conn, $topic_id) {
    $query = $conn->query("
        SELECT a.*, t.topic_name 
        FROM article a 
        JOIN topic t ON a.topic_id = t.topic_id
        WHERE a.topic_id = '$topic_id'
    ");
    $data = [];
    while ($row = $query->fetch_assoc()) $data[] = $row;
    jsonResponse(200, "success", "", $data);
}

function getArticle($conn, $id) {
    $query = $conn->query("
        SELECT a.*, t.topic_name 
        FROM article a 
        JOIN topic t ON a.topic_id = t.topic_id
        WHERE a.article_id = '$id'
    ");
    $data = $query->fetch_assoc();
    jsonResponse(200, "success", "", $data);
}

function createArticle($conn, $data) {
    $required = ["topic_id", "article_title", "article_author"];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            jsonResponse(400, "error", "Field '$field' is required");
            return;
        }
    }

    $topic = $data["topic_id"];
    $title = $conn->real_escape_string($data["article_title"]);
    $author = $conn->real_escape_string($data["article_author"]);
    $publisher = $conn->real_escape_string($data["article_publisher"] ?? "");
    $year = $conn->real_escape_string($data["article_year"] ?? "");

    $conn->query("
        INSERT INTO article (topic_id, article_title, article_author, article_publisher, article_year)
        VALUES ('$topic', '$title', '$author', '$publisher', '$year')
    ");
    jsonResponse(201, "success", "Article added successfully");
}

function updateArticle($conn, $id, $data) {
    $topic = $data["topic_id"];
    $title = $conn->real_escape_string($data["article_title"]);
    $author = $conn->real_escape_string($data["article_author"]);
    $publisher = $conn->real_escape_string($data["article_publisher"]);
    $year = $conn->real_escape_string($data["article_year"]);

    $conn->query("
        UPDATE article SET
        topic_id='$topic', article_title='$title', article_author='$author',
        article_publisher='$publisher', article_year='$year'
        WHERE article_id='$id'
    ");
    jsonResponse(200, "success", "Article updated successfully");
}

function deleteArticle($conn, $id) {
    $conn->query("DELETE FROM article WHERE article_id='$id'");
    jsonResponse(200, "success", "Article deleted successfully");
}
?>
