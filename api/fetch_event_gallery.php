<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

// Set headers to ensure JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Disable display of errors on page for production
error_reporting(0);
ini_set('display_errors', 0);

// Set up error log file
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/logs/api_errors.log');


if (!isset($conn) || !$conn) {
    returnJsonError('Database connection failed.');
}

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
$event_id = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

if (!$event_id || $event_id <= 0) {
    returnJsonError('Invalid event ID');
}

try {
    // Pagination setup
    $itemsPerPage = 6;
    $offset = ($page - 1) * $itemsPerPage;

    // Prepared statement for count
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM event_media WHERE event_id = ?");
    mysqli_stmt_bind_param($countStmt, "i", $event_id);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalItems = $countRow['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);


 // Prepared statement for gallery fetch
    $stmt = mysqli_prepare($conn, 
        "SELECT 
            em.media_id, 
            em.media_title, 
            em.media_description, 
            em.media_path, 
            em.media_type,
            e.event_name,
            e.event_id AS actual_attraction_id,
            a.attraction_name
        FROM event_media em
        JOIN eventlist e ON em.event_id = e.event_id
        JOIN attraction a ON e.attraction_id = a.attraction_id
        WHERE em.event_id = ?
        ORDER BY em.created_at DESC 
        LIMIT ? OFFSET ?"
    );

    mysqli_stmt_bind_param($stmt, "iii", $event_id, $itemsPerPage, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    //Check if query was successful
    if (!$result) {
        error_log("Database Query Error: " . mysqli_error($conn));
        returnJsonError("Database query failed");
    }

    $base_url = "http://localhost/MARANGUIDE/";
    // Fetch galleries
    $galleries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Replace underscores with spaces in the attraction name
        $cleanAttractionName = str_replace('_', ' ', $row['attraction_name']);
        $cleanEventName = str_replace('_', ' ', $row['event_name']);
        error_log("Debugging - Sanitized Name: " . $cleanAttractionName);
        error_log("Debugging - Sanitized Name: " . $cleanEventName);

        // Determine folder based on media type
        $folder = 'pictures'; // Default folder
        if (isset($row['media_type']) && strtolower($row['media_type']) === 'video') {
            $folder = 'videos';
        } elseif (preg_match('/\.(mp4|mov|avi|mkv)$/i', $row['media_path'])) {
            $folder = 'videos';
        }

        $relativePath = "..//media/attraction/{$cleanAttractionName}/{$cleanEventName}/{$folder}/" . basename($row['media_path']);

        // Log the generated path for debugging
        error_log("Debugging - Original Name: " . $row['event_name']);
        error_log("Debugging - Sanitized Name: " . $cleanEventName);
        error_log("Debugging - Folder: " . $folder);
        error_log("Debugging - Generated Path: " . $relativePath);

        $row['media_path'] = $relativePath;
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
    echo json_encode($response, JSON_THROW_ON_ERROR);

} catch (Exception $e) {
    returnJsonError($e->getMessage());
} finally {
  
    if (isset($countStmt)) mysqli_stmt_close($countStmt);
    if (isset($stmt)) mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
