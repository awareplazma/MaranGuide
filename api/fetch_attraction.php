<?php

include '../database_config.php';

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

// Fetch Attraction for Index.html

try {
    // Pagination parameters with validation
    $page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 1;
    $page = max(1, $page); // Ensure page is at least 1
    $limit = 6; // Attractions per page
    $offset = ($page - 1) * $limit;

    // Prepare response structure
    $response = [
        'attractions' => [],
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => 0,
            'totalItems' => 0
        ]
    ];

    // Count total attractions
    $countQuery = "SELECT COUNT(*) as total FROM attraction WHERE attraction_status = 'aktif'";
    $countResult = mysqli_query($conn, $countQuery);
    
    if (!$countResult) {
        throw new Exception("Failed to count attractions: " . mysqli_error($conn));
    }

    $totalAttractions = mysqli_fetch_assoc($countResult)['total'];
    $response['pagination']['totalPages'] = ceil($totalAttractions / $limit);
    $response['pagination']['totalItems'] = $totalAttractions;

    // Fetch attractions with media
    $query = "SELECT 
        a.attraction_id, 
        a.attraction_name, 
        a.attraction_description, 
        COALESCE(m.media_path, '../picture/default-attraction.jpg') as imageUrl
    FROM attraction a
    LEFT JOIN (
        SELECT attraction_id, MIN(media_path) AS media_path
        FROM attraction_media
        GROUP BY attraction_id
    ) m ON a.attraction_id = m.attraction_id
    WHERE a.attraction_status = 'aktif'
    ORDER BY a.attraction_created_at DESC
    LIMIT ? OFFSET ?;
    ";

    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch attractions
    while ($row = mysqli_fetch_assoc($result)) {

        $media_path = $row['imageUrl'];

        $media_path = BASE_PATH_VISITOR . basename($row['imageUrl']);
        $response['attractions'][] = [
            'id' => $row['attraction_id'],
            'name' => htmlspecialchars($row['attraction_name']),
            'media_path' => htmlspecialchars($row['imageUrl'])
        ];
    }

    // Handle empty results
    if (empty($response['attractions'])) {
        $response['attractions'][] = [
            'id' => 0,
            'name' => 'No Attractions Available',
            'description' => 'Check back later for new attractions.',
            'media_path' => '../media/default_attraction.png'
        ];
    }

    // Send successful response
    echo json_encode($response);

} catch (Exception $e) {
    // Centralized error handling
    sendErrorResponse($e->getMessage());
} finally {
    // Ensure database connection is closed
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>