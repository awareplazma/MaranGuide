<?php
// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once('../maranguide_connection.php');

// Error response function
function sendErrorResponse($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'error' => true,
        'message' => $message
    ]);
    exit;
}

// Sanitize Input Function
function sanitizeInput($input) {
    return is_numeric($input) ? intval($input) : null;
}

try {
    // Validate attraction ID
    $attractionId = isset($_GET['id']) ? sanitizeInput($_GET['id']) : null;
    
    if ($attractionId === null) {
        sendErrorResponse("Invalid attraction ID", 400);
    }

    // Fetch detailed attraction information
    $query = "SELECT 
        a.attraction_id, 
        a.attraction_name, 
        a.attraction_description, 
        a.attraction_address,
        a.attraction_latitude,
        a.attraction_longitude,
        a.attraction_operating_hours,
        GROUP_CONCAT(DISTINCT m.media_path) as images,
        GROUP_CONCAT(DISTINCT c.category_name) as categories
    FROM attraction a
    LEFT JOIN attraction_media m ON a.attraction_id = m.attraction_id
    LEFT JOIN attraction_category ac ON a.attraction_id = ac.attraction_id
    LEFT JOIN category c ON ac.category_id = c.category_id
    WHERE a.attraction_id = ? AND a.attraction_status = 'aktif'
    GROUP BY a.attraction_id";

    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $attractionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch attraction details
    $attraction = mysqli_fetch_assoc($result);

    if (!$attraction) {
        sendErrorResponse("Attraction not found", 404);
    }

    // Process images and categories
    $response = [
        'id' => $attraction['attraction_id'],
        'name' => htmlspecialchars($attraction['attraction_name']),
        'description' => htmlspecialchars($attraction['attraction_description']),
        'address' => htmlspecialchars($attraction['attraction_address']),
        'latitude' => $attraction['attraction_latitude'],
        'longitude' => $attraction['attraction_longitude'],
        'price' => $attraction['attraction_price'],
        'operatingHours' => htmlspecialchars($attraction['attraction_operating_hours']),
        'images' => array_map('htmlspecialchars', explode(',', $attraction['images'])),
        'categories' => array_map('htmlspecialchars', explode(',', $attraction['categories']))
    ];

    echo json_encode($response);

} catch (Exception $e) {
    sendErrorResponse($e->getMessage());
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>