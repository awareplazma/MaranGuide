<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once($_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php');

function sendErrorResponse($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'error' => true,
        'message' => $message
    ]);
    exit;
}

function sanitizeInput($input) {
    return is_numeric($input) ? intval($input) : null;
}

try {
    $page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 1;
    $page = max(1, $page);
    $limit = 6;
    $offset = ($page - 1) * $limit;

    $response = [
        'attractions' => [],
        'currentPage' => $page,
        'totalPages' => 0,
        'totalItems' => 0
    ];

    // Count total attractions
    $countQuery = "SELECT COUNT(*) as total FROM attraction WHERE attraction_status = 'aktif'";
    $countResult = mysqli_query($conn, $countQuery);
    
    if (!$countResult) {
        throw new Exception("Failed to count attraction: " . mysqli_error($conn));
    }

    $totalEvents = mysqli_fetch_assoc($countResult)['total'];
    $response['totalPages'] = ceil($totalEvents / $limit);
    $response['totalItems'] = $totalEvents;

    // Fetch eattraction with media
    $query = "SELECT 
        attraction_id, 
        attraction_name, 
        attraction_description, 
        attraction_thumbnails
    FROM attraction
    WHERE attraction_status = 'aktif'
    ORDER BY attraction_created_at DESC
    LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch eattraction
    while ($row = mysqli_fetch_assoc($result)) {
        $response['attractions'][] = [
            'id' => $row['attraction_id'],
            'name' => htmlspecialchars($row['attraction_name']),
            'media_path' => '../' . ltrim($row['attraction_thumbnails'], '/')
        ];
    }

    // Handle empty results
    if (empty($response['attractions'])) {
        $response['attractions'][] = [
            'id' => 0,
            'name' => 'Tiada Tarikan/Acara',
            'description' => 'Check back later for new attractions.',
            'media_path' => '../media/default_attraction.png'
        ];
    }

    // Send successful response
    echo json_encode($response);

} catch (Exception $e) {
    sendErrorResponse($e->getMessage());
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>