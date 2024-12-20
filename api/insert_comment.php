<?php 
// Database connection
require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

// Strict error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1); //Check at error_log.php if error
ini_set('error_log', __DIR__ . '/error.log');

// Security and CORS headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');  // Changed to POST
header('Access-Control-Allow-Headers: Content-Type');

// Input validation
if(empty($_POST['username']) || empty($_POST['rating']) || empty($_POST['content'] || empty($_POST['id']))){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing or empty required fields'
    ]);
    exit;
}

// Sanitize inputs
$comment_user = htmlspecialchars(trim($_POST['username']));
$comment_content = htmlspecialchars(trim($_POST['content']));
$attraction_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$comment_rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT, [
    'options' => [
        'min_range' => 1, 
        'max_range' => 5
    ]
]);

// Additional validation
if ($comment_rating === false) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid rating value'
    ]);
    exit;
}

$approval_status = 'no';

try {
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Prepared statement for attractions
    $query = "INSERT INTO comments (
        user,
        content,
        rating,
        attraction_id,
        created_at,
        approval_status
    ) VALUES (?, ?, ?, ?, NOW(), ?)";

    // Prepare statement
    $stmt = $conn->prepare($query);
    
    // Check if prepare was successful
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param('ssiii', $comment_user, $comment_content, $comment_rating, $attraction_id, $approval_status);
    
    // Execute statement
    $execute_result = $stmt->execute();
    
    // Check execution
    if ($execute_result === false) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Response
    if($stmt->affected_rows > 0)
    {
        echo json_encode([
            'status' => 'success',
            'message' => 'Comment added successfully'
        ], JSON_PRETTY_PRINT);
    } else {
        throw new Exception('No rows affected');
    }

    // Close statement
    $stmt->close();

} catch (Exception $e) {
    // Detailed error logging
    error_log('Attraction Comment Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error'
    ]);
}

// Close database connection
if ($conn) {
    $conn->close();
}