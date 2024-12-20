<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/MARANGUIDE/api/error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

// Check database connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    die(json_encode([
        'error' => true,
        'message' => 'Database connection failed.'
    ]));
}

try {
    // Log received parameters
    error_log("Received GET parameters: " . print_r($_GET, true));

    // Validate inputs
    $attraction_id = isset($_GET['attractionId']) ? intval($_GET['attractionId']) : null;
    $event_id = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

    error_log("Attraction ID from Request: " . $attraction_id);
    error_log("Event ID from Request: " . $event_id);

    if (!$attraction_id || $attraction_id <= 0) {
        http_response_code(400);
        die(json_encode([
            'error' => true,
            'message' => 'Invalid attraction ID.'
        ]));
    }

    if (!$event_id || $event_id <= 0) {
        http_response_code(400);
        die(json_encode([
            'error' => true,
            'message' => 'Invalid event ID.'
        ]));
    }

    // Query for the event details
    $query = "SELECT * FROM eventlist WHERE attraction_id = ? AND event_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    error_log("Prepared query: SELECT * FROM eventlist WHERE attraction_id = $attraction_id AND event_id = $event_id");

    if (!$stmt) {
        error_log("Failed to prepare query: " . mysqli_error($conn));
        http_response_code(500);
        die(json_encode([
            'error' => true,
            'message' => 'Internal server error.'
        ]));
    }

    mysqli_stmt_bind_param($stmt, "ii", $attraction_id, $event_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        die(json_encode([
            'error' => true,
            'message' => 'Event not found.'
        ]));
    }

    $event = mysqli_fetch_assoc($result);
    error_log("Event Data: " . print_r($event, true));

    // Prepare response
    $response = [
        'id' => $event['event_id'] ?? null,
        'name' => $event['event_name'] ?? '',
        'description' => $event['event_description'] ?? '',
        'duration' => ($event['event_start_date'] ?? '') . ' - ' . ($event['event_end_date'] ?? ''),
    ];

    // Send JSON response
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'An unexpected error occurred.'
    ]);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
