<?php
header('Content-Type: application/json');
header('Content-Language: sk');
require_once '../includes/config.php';
require_once 'lecture.php'; // Create an instance of the lecture lecture 
$lectureOBJ = new Lecture($pdo); // Get the request method 
$method = $_SERVER['REQUEST_METHOD']; // Get the requested endpoint 
$requestUri = explode('/', $_SERVER['REQUEST_URI']);
$endpoint = end($requestUri);

// Handle endpoint /courses

// Set the response content type 
//header('Content-Type: application/json'); // Process the request 
switch ($method) {
    case 'GET':
        if ($endpoint === 'lectures') { // Get all lectures 
            $lectures = $lectureOBJ->getAllLectures();
            if ($lectures === null || empty($lectures)) {
                http_response_code(404);
                echo json_encode(['error' => 'No data found']);
            } else {
                http_response_code(200);
                echo json_encode($lectures, JSON_PRETTY_PRINT);
            }
        } else if (isset($_GET['id']) && $endpoint === ("lectures?id=" . $_GET['id'])) { // Get lecture by ID
            $lectureId = $_GET['id'];
            $lecture = $lectureOBJ->getLectureById($lectureId);
            if ($lecture === "Lecture not found") {
                http_response_code(404);
                echo json_encode($lecture, JSON_PRETTY_PRINT);
            } else {
                http_response_code(200);
                echo json_encode($lecture, JSON_PRETTY_PRINT);
            }
        } else if (($endpoint === "temy?pracovisko=") . $_GET['pracovisko']) {
            require_once '../includes/curlTemy.php';
            if ($data === null || empty($data)) {
                http_response_code(404);
                echo json_encode(['error' => 'No data found']);
            } else {
                http_response_code(200);
                echo json_encode($data, JSON_PRETTY_PRINT);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'oops something went wrong']);
        }
        break;
    case 'POST':
        if ($endpoint === 'lectures') { // Add new lecture 
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $lectureOBJ->addLecture($data);
            if ($result === false) {
                http_response_code(404);
                echo json_encode(['error' => 'Invalid time slot overlap']);
            } else {
                http_response_code(201);
                echo json_encode(['success' => $result]);
            }
        }
        break;
    case 'PUT':
        if (isset($_GET['id']) && $endpoint === ("lectures?id=" . $_GET['id'])) { // Update lecture by ID 
            $lectureId = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $lectureOBJ->updateLecture($lectureId, $data);
            if ($result === false) {
                http_response_code(404);
                echo json_encode(['error' => 'Invalid time slot overlap or id not found']);
            } else {
                http_response_code(200);
                echo json_encode(['success' => $result]);
            }
        }
        break;
    case 'DELETE':
        if (isset($_GET['id']) && $endpoint === ("lectures?id=" . $_GET['id'])) { // Delete lecture by ID 
            $lectureId = $_GET['id'];
            $result = $lectureOBJ->deleteLecture($lectureId);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Lecture not found']);
                
            }
        }
        break;
}
