<?php
ob_start();
// Include database connection
require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up error log file
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/logs/api_errors.log');

// Set headers to ensure JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');



// Check database connection
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
$attractionId = isset($_GET['attractionId']) ? intval($_GET['attractionId']) : 0;

// Validate attraction ID
if ($attractionId <= 0) {
    returnJsonError('Invalid attraction ID');
}

try {
    // Pagination setup
    $itemsPerPage = 6;
    $offset = ($page - 1) * $itemsPerPage;

    // DEBUG POINT 1: Log attraction ID before database queries
    error_log("Debugging - Attraction ID: " . $attractionId);

    // Prepared statement for count
    $countStmt = mysqli_prepare($conn, 
        "SELECT COUNT(*) AS total FROM attraction_media WHERE attraction_id = ?"
    );
    mysqli_stmt_bind_param($countStmt, "i", $attractionId);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalItems = $countRow['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    // DEBUG POINT 2: Verify total items and pages
    error_log("Debugging - Total Items: " . $totalItems . ", Total Pages: " . $totalPages);

    // Prepared statement for gallery fetch
    $stmt = mysqli_prepare($conn, 
        "SELECT 
            am.media_id, 
            am.media_title, 
            am.media_description, 
            am.media_path, 
            am.media_type,
            a.attraction_name,
            a.attraction_id AS actual_attraction_id
        FROM attraction_media am
        JOIN attraction a ON am.attraction_id = a.attraction_id
        WHERE am.attraction_id = ?
        ORDER BY am.created_at DESC 
        LIMIT ? OFFSET ?"
    );
    mysqli_stmt_bind_param($stmt, "iii", $attractionId, $itemsPerPage, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // DEBUG POINT 3: Check if query was successful
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
        error_log("Debugging - Sanitized Name: " . $cleanAttractionName);

        // Determine folder based on media type
        $folder = 'pictures'; // Default folder
        if (isset($row['media_type']) && strtolower($row['media_type']) === 'video') {
            $folder = 'videos';
        } elseif (preg_match('/\.(mp4|mov|avi|mkv)$/i', $row['media_path'])) {
            $folder = 'videos';
        }

        $relativePath = "../media/attraction/{$cleanAttractionName}/{$folder}/" . basename($row['media_path']);

        // Log the generated path for debugging
        error_log("Debugging - Original Name: " . $row['attraction_name']);
        error_log("Debugging - Sanitized Name: " . $cleanAttractionName);
        error_log("Debugging - Folder: " . $folder);
        error_log("Debugging - Generated Path: " . $relativePath);

        $row['media_path'] = $relativePath;
        $galleries[] = $row;
    }

    // DEBUG POINT 6: Verify galleries array
    error_log("Debugging - Galleries Count: " . count($galleries));

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
    // Catch and log any exceptions
    error_log("Exception occurred: " . $e->getMessage());
    returnJsonError($e->getMessage());
} finally {
 
    if (isset($countStmt)) mysqli_stmt_close($countStmt);
    if (isset($stmt)) mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>