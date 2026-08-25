<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

function sendResponse(bool $success, string $message = '', array $data = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // GET: return all records.
    if ($method === 'GET') {
        $result = $conn->query(
            "SELECT id, name, age, status FROM people ORDER BY id ASC"
        );

        if (!$result) {
            throw new Exception($conn->error);
        }

        $people = [];

        while ($row = $result->fetch_assoc()) {
            $row['id'] = (int) $row['id'];
            $row['age'] = (int) $row['age'];
            $row['status'] = (int) $row['status'];
            $people[] = $row;
        }

        sendResponse(true, '', $people);
    }

    // POST: add a new record or toggle an existing record.
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            sendResponse(false, 'Invalid request.', [], 400);
        }

        $action = $input['action'] ?? '';

        // Add a person.
        if ($action === 'add') {
            $name = trim((string) ($input['name'] ?? ''));
            $age = filter_var($input['age'] ?? null, FILTER_VALIDATE_INT);

            if ($name === '') {
                sendResponse(false, 'Please enter a name.', [], 422);
            }

            if ($age === false || $age < 1 || $age > 120) {
                sendResponse(false, 'Please enter a valid age between 1 and 120.', [], 422);
            }

            $stmt = $conn->prepare(
                "INSERT INTO people (name, age, status) VALUES (?, ?, 0)"
            );

            if (!$stmt) {
                throw new Exception($conn->error);
            }

            $stmt->bind_param('si', $name, $age);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $newId = $stmt->insert_id;
            $stmt->close();

            sendResponse(true, 'Person added successfully.', [
                'id' => (int) $newId
            ]);
        }

        // Toggle a person's status: 0 -> 1 or 1 -> 0.
        if ($action === 'toggle') {
            $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

            if ($id === false || $id < 1) {
                sendResponse(false, 'Invalid person ID.', [], 422);
            }

            $stmt = $conn->prepare(
                "UPDATE people SET status = CASE WHEN status = 0 THEN 1 ELSE 0 END WHERE id = ?"
            );

            if (!$stmt) {
                throw new Exception($conn->error);
            }

            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                $stmt->close();
                sendResponse(false, 'Person not found.', [], 404);
            }

            $stmt->close();

            $stmt = $conn->prepare("SELECT id, name, age, status FROM people WHERE id = ?");

            if (!$stmt) {
                throw new Exception($conn->error);
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $person = $result->fetch_assoc();
            $stmt->close();

            if (!$person) {
                sendResponse(false, 'Person not found.', [], 404);
            }

            $person['id'] = (int) $person['id'];
            $person['age'] = (int) $person['age'];
            $person['status'] = (int) $person['status'];

            sendResponse(true, 'Status updated successfully.', $person);
        }

        sendResponse(false, 'Unknown action.', [], 400);
    }

    sendResponse(false, 'Method not allowed.', [], 405);

} catch (Throwable $e) {
    // Do not expose database credentials or internal SQL details to visitors.
    sendResponse(false, 'Server/database error. Check your database configuration.', [], 500);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
