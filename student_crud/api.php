
<?php
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

function respond($status, $message, $data = null) {
    http_response_code($status);
    echo json_encode([
        'success' => $status < 400,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function getInput() {
    $input = json_decode(file_get_contents('php://input'), true);
    return is_array($input) ? $input : [];
}

function validateStudent($data, $COURSES) {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $course = trim($data['course'] ?? '');
    $year = filter_var($data['year'] ?? null, FILTER_VALIDATE_INT);

    if ($name === '') {
        respond(400, 'Name is required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(400, 'Enter a valid email.');
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        respond(400, 'Phone must be 10 digits.');
    }

    if (!in_array($course, $COURSES, true)) {
        respond(400, 'Select a valid course.');
    }

    if ($year === false || $year < 1 || $year > 4) {
        respond(400, 'Year must be 1 to 4.');
    }

    return [$name, $email, $phone, $course, $year];
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // GET: List students or get one student
    if ($method === 'GET') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id !== null && $id !== false) {
            if ($id < 1) {
                respond(400, 'Invalid student ID.');
            }

            $stmt = $conn->prepare(
                "SELECT id, name, email, phone, course, year
                 FROM students WHERE id = ? AND status = 'active'"
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $student = $stmt->get_result()->fetch_assoc();

            if (!$student) {
                respond(404, 'Student not found.');
            }

            respond(200, 'Student found.', $student);
        }

        $result = $conn->query(
            "SELECT id, name, email, phone, course, year
             FROM students WHERE status = 'active'
             ORDER BY id DESC"
        );

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        respond(200, 'Students loaded successfully.', $students);
    }

    // POST: Add a student
    if ($method === 'POST') {
        $data = getInput();
        [$name, $email, $phone, $course, $year] =
            validateStudent($data, $COURSES);

        $stmt = $conn->prepare(
            'SELECT id FROM students WHERE email = ?'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            respond(409, 'This email already exists.');
        }
        $stmt->close();

        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, phone, course, year)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssssi', $name, $email, $phone, $course, $year
        );
        $stmt->execute();

        $newId = $conn->insert_id;
        respond(201, 'Student added successfully.', [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'course' => $course,
            'year' => $year
        ]);
    }

    // PUT: Update a student
    if ($method === 'PUT') {
        $data = getInput();
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id < 1) {
            respond(400, 'A valid student ID is required.');
        }

        [$name, $email, $phone, $course, $year] =
            validateStudent($data, $COURSES);

        $stmt = $conn->prepare(
            "SELECT id FROM students
             WHERE id = ? AND status = 'active'"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            respond(404, 'Student not found.');
        }
        $stmt->close();

        $stmt = $conn->prepare(
            'SELECT id FROM students WHERE email = ? AND id <> ?'
        );
        $stmt->bind_param('si', $email, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            respond(409, 'This email belongs to another student.');
        }
        $stmt->close();

        $stmt = $conn->prepare(
            "UPDATE students
             SET name = ?, email = ?, phone = ?, course = ?, year = ?
             WHERE id = ? AND status = 'active'"
        );
        $stmt->bind_param(
            'ssssii', $name, $email, $phone, $course, $year, $id
        );
        $stmt->execute();

        respond(200, 'Student updated successfully.', [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'course' => $course,
            'year' => $year
        ]);
    }

    // DELETE: Soft-delete a student
    if ($method === 'DELETE') {
        $data = getInput();
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id < 1) {
            respond(400, 'A valid student ID is required.');
        }

        $stmt = $conn->prepare(
            "UPDATE students SET status = 'inactive'
             WHERE id = ? AND status = 'active'"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            respond(404, 'Student not found or already inactive.');
        }

        respond(200, 'Student deleted successfully.', ['id' => $id]);
    }

    header('Allow: GET, POST, PUT, DELETE');
    respond(405, 'HTTP method not allowed.');

} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(500, 'A server error occurred. Please try again.');
}
