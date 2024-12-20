<?php
// Disable all error output to prevent HTML errors
error_reporting(0);
ini_set('display_errors', 0);

// Set headers to ensure JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database connection
require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

// Function to return JSON error
function returnJsonError($message) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $message
    ]);
    exit();
}


// Sanitize inputs
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$attraction_id = isset($_GET['attractionId']) ? intval($_GET['attractionId']) : null;
$event_id = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

error_log("Attraction ID from Request: " . $attraction_id);
error_log("Event ID from Request: " . $event_id);

// Validate attraction ID
if ($attraction_id <= 0) {
    returnJsonError('Invalid attraction ID');
}

try {
    // Pagination setup
    $itemsPerPage = 6;
    $offset = ($page - 1) * $itemsPerPage;

    // Prepared statement for count
    $countStmt = mysqli_prepare($conn, 
        "SELECT COUNT(*) AS total FROM event_media WHERE attraction_id = ? AND event_id = ?"
    );
    mysqli_stmt_bind_param($countStmt, "ii", $attraction_id, $event_id);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalItems = $countRow['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Prepared statement for gallery fetch
    $stmt = mysqli_prepare($conn, 
        "SELECT 
            * 
        FROM event_media
        WHERE attraction_id = ? AND event_id = ?
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?"
    );
    mysqli_stmt_bind_param($stmt, "iii", $attraction_id, $itemsPerPage, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch galleries
    $galleries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $galleries[] = $row;
    }

    // Prepare response
    $response = [
        'galleries' => $galleries,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems
    ];

    // Output JSON
    echo json_encode($response);

} catch (Exception $e) {
    returnJsonError($e->getMessage());
} finally {
    // Close connections
    if (isset($countStmt)) mysqli_stmt_close($countStmt);
    if (isset($stmt)) mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>