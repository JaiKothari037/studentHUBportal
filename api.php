<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT student_id, name, email, course, status FROM students WHERE student_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                http_response_code(200);
                echo json_encode(["status" => "success", "data" => $row]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Student not found"]);
            }
            $stmt->close();
        } else {
            $sql = "SELECT student_id, name, email, course, status FROM students ORDER BY student_id DESC";
            $result = $conn->query($sql);
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $students]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $course = trim($data['course'] ?? '');

        if (empty($name) || empty($email) || empty($course)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Name, Email, and Course are required fields."]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students (name, email, course, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("sss", $name, $email, $course);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Student created successfully!", "student_id" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['student_id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $course = trim($data['course'] ?? '');
        $status = trim($data['status'] ?? 'active');

        if ($id <= 0 || empty($name) || empty($email) || empty($course)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid input for update."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, status = ? WHERE student_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $course, $status, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Student updated successfully!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update student: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid Student ID for deletion."]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Student deleted successfully!"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete student: " . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>