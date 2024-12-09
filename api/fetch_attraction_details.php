<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/MARANGUIDE/api/error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once('../maranguide_connection.php');

// Log any connection errors
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die(json_encode([
        'error' => true, 
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]));
}

try {
    // Log received parameters
    error_log("Received GET parameters: " . print_r($_GET, true));

    $attractionId = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($attractionId === null) {
        http_response_code(400);
        die(json_encode([
            'error' => true, 
            'message' => 'Invalid attraction ID'
        ]));
    }

    $query = "SELECT 
        a.attraction_id, 
        a.attraction_name, 
        a.attraction_description, 
        a.attraction_address,
        a.attraction_opening_hours,
        a.attraction_closing_hours,
        a.attraction_operating_days,
        a.attraction_status
    FROM attraction a
    WHERE a.attraction_id = ? AND a.attraction_status = 'aktif'";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $attractionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $attraction = mysqli_fetch_assoc($result);

    // Log query result
    error_log("Query result: " . print_r($attraction, true));

    if (!$attraction) {
        http_response_code(404);
        die(json_encode([
            'error' => true, 
            'message' => 'Attraction not found'
        ]));
    }

    $response = [
        'id' => $attraction['attraction_id'],
        'name' => htmlspecialchars($attraction['attraction_name']),
        'description' => htmlspecialchars($attraction['attraction_description']),
        'address' => htmlspecialchars($attraction['attraction_address']),
        'operatingDays' => htmlspecialchars($attraction['attraction_operating_days']),
        'operatingHours' => htmlspecialchars($attraction['attraction_opening_hours'] . ' - ' . $attraction['attraction_closing_hours'])
    ];

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true, 
        'message' => $e->getMessage()
    ]);
} finally {
    mysqli_close($conn);
}