<?php 
// Database connection
require_once('../maranguide_connection.php');
if (!file_exists('../maranguide_connection.php')) {
    die('Connection file not found');
}


// Strict error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Security and CORS headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Input validation
if(empty($_POST['title']) || empty($_POST['content'])){
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing or empty required fields'
    ]);
    var_dump($_POST);
    exit;
}

//Sanitize
$feedback_title = htmlspecialchars(trim($_POST['title']));
$feedback_content = htmlspecialchars(trim($_POST['content']));
$read_status = 'unread';


try {

    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }


    $query = "INSERT INTO feedback (
        title,
        feedback_content,
        feedback_created_at,
        read_status
        ) VALUES (?, ? , NOW(), ?)";


    $stmt = $conn -> prepare($query);

    // Check if prepare was successful
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt ->bind_param('sss', $feedback_title, $feedback_content, $read_status);

    $stmt ->execute();
    
    // Response
    if($stmt->affected_rows >0)
    {
    echo json_encode([
        'status' => 'success',
        'message'=> 'Comment added successfully'
    ], JSON_PRETTY_PRINT);
    } else{
        throw new Exception('Insert Operation Failed');
    }

 
    $stmt->close();

} catch (Exception $e) {
    // Detailed error logging without exposing sensitive info
    error_log('Database Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error'
    ]);

//Ensure closing connection
if($conn)
{
$conn->close();
}
}