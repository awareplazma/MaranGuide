<?php 
// Database connection
require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

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

/* Prevent direct script access
if (php_sapi_name() !== 'cli' && (!defined('SITE_ROOT') || !SITE_ROOT)) {
    http_response_code(403);
    exit('Forbidden');
} */


try {
    // Prepared statement for attractions
    $query = "SELECT 
        attraction_id,
        attraction_name,
        attraction_latitude AS latitude,
        attraction_longitude AS longitude
    FROM attraction
    WHERE attraction_status = 'aktif'
    ORDER BY attraction_name";
    
    // Prepare statement
    $stmt = $conn->prepare($query);
    
    // Execute query
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $conn->error);
    }
    
    // Bind result variables
    $stmt->bind_result(
        $attraction_id, 
        $attraction_name, 
        $latitude, 
        $longitude
    );
    
    // Fetch results
    $attractions = [];
    while ($stmt->fetch()) {
        $attractions[] = [
            'id' => intval($attraction_id),
            'name' => htmlspecialchars($attraction_name, ENT_QUOTES, 'UTF-8'),
            'latitude' => floatval($latitude),
            'longitude' => floatval($longitude)
        ];
    }
    
    // Close statement
    $stmt->close();
    
    // Response
    echo json_encode([
        'status' => 'success',
        'attractions' => $attractions
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    
    error_log('Database Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error'
    ]);
}