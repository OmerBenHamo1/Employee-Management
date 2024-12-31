<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

$path = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

if (isset($path[0]) && $path[0] === 'employees') {
    switch ($method) {
        case 'GET':
            isset($path[1]) ? getEmployee($connection, $path[1]) : getAllEmployees($connection);
            break;
        case 'POST':
            if (isset($path[1]) && $path[1] === 'check_email') {
                checkEmail($connection);
            } else {
                addEmployee($connection);
            }
            break;
        case 'PUT':
            isset($path[1]) ? updateEmployee($connection, $path[1]) : respond(400, ['error' => 'Employee ID required for PUT']);
            break;
        case 'DELETE':
            deleteEmployee($connection);
            break;
        default:
            respond(405, ['error' => 'Method not allowed']);
    }
} else {
    respond(404, ['error' => 'Invalid endpoint']);
}

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

function checkEmail($connection) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        respond(400, ['error' => 'Invalid email format']);
    }

    $stmt = $connection->prepare("SELECT id FROM employees WHERE email = ?");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        respond(409, ['error' => 'Email already exists']);
    } else {
        respond(200, ['message' => 'Email does not exist']);
    }

    $stmt->close();
}

function getAllEmployees($connection) {
    $sql = "SELECT * FROM employees";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
        respond(200, $employees);
    } else {
        respond(200, []);
    }
}

function getEmployee($connection, $id) {
    $stmt = $connection->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    if ($employee) {
        respond(200, $employee);
    } else {
        respond(404, ['error' => 'Employee not found']);
    }

    $stmt->close();
}

function addEmployee($connection) {
    $data = json_decode(file_get_contents('php://input'), true);

    $requiredFields = ['first_name', 'last_name', 'email', 'position', 'hire_date', 'salary'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            respond(400, ['error' => "$field is required"]);
        }
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $data['first_name']) ||
        !preg_match('/^[a-zA-Z\s]+$/', $data['last_name']) ||
        !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['hire_date']) ||
        !is_numeric($data['salary'])) {
        respond(400, ['error' => 'Invalid data']);
    }

    $stmt = $connection->prepare("SELECT id FROM employees WHERE email = ?");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        respond(409, ['error' => 'Email already exists']);
    }

    $stmt->close();

    $stmt = $connection->prepare("INSERT INTO employees (first_name, last_name, email, position, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssd",
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['position'],
        $data['hire_date'],
        $data['salary']
    );

    if ($stmt->execute()) {
        respond(201, ['message' => 'Employee added successfully', 'id' => $stmt->insert_id]);
    } else {
        respond(500, ['error' => 'Failed to add employee: ' . $stmt->error]);
    }

    $stmt->close();
}

function updateEmployee($connection, $id) {
    $data = json_decode(file_get_contents('php://input'), true);

    $requiredFields = ['first_name', 'last_name', 'email', 'position', 'hire_date', 'salary'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            respond(400, ['error' => "$field is required"]);
        }
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $data['first_name']) ||
        !preg_match('/^[a-zA-Z\s]+$/', $data['last_name']) ||
        !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['hire_date']) ||
        !is_numeric($data['salary'])) {
        respond(400, ['error' => 'Invalid data']);
    }

    $stmt = $connection->prepare("UPDATE employees SET first_name = ?, last_name = ?, email = ?, position = ?, hire_date = ?, salary = ? WHERE id = ?");
    $stmt->bind_param(
        "sssssdi",
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['position'],
        $data['hire_date'],
        $data['salary'],
        $id
    );

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        respond(200, ['message' => 'Employee updated successfully']);
    } else {
        respond(404, ['error' => 'Employee not found']);
    }

    $stmt->close();
}

function deleteEmployee($connection) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !is_numeric($data['id'])) {
        respond(400, ['error' => 'Invalid ID']);
    }

    $id = $data['id'];

    $stmt = $connection->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id); 
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        respond(200, ['message' => 'Employee deleted successfully']);
    } else {
        respond(404, ['error' => 'Employee not found']);
    }

    $stmt->close();
}
?>
