<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include('../maranguide_connection.php');

// Check if an ID is passed via GET
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$attractions = [];

if ($id)
// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // 6 attractions per page
$offset = ($page - 1) * $limit;

$response = [
    'attractions' => [],
    'totalPages' => 0,
    'currentPage' => $page
];

// First, get total number of attractions
$countQuery = "SELECT COUNT(*) as total FROM attractions";
$countResult = mysqli_query($conn, $countQuery);
$totalAttractions = mysqli_fetch_assoc($countResult)['total'];
$response['totalPages'] = ceil($totalAttractions / $limit);

// Fetch attractions for current page
$query = "SELECT a.id, a.name, a.description, 
                 COALESCE(m.media_path, '../picture/rabbitland.jpg') as media_path 
          FROM attractions a 
          LEFT JOIN attraction_media m ON a.id = m.attraction_id
          LIMIT ? OFFSET ?";

// Prepare statement to prevent SQL injection
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $response['attractions'][] = $row;
}

// If no attractions, return a default attraction
if (empty($response['attractions'])) {
    $response['attractions'][] = [
        'id' => 0,
        'name' => 'No Attractions Available',
        'description' => 'Check back later for new attractions.',
        'media_path' => '../picture/rabbitland.jpg'
    ];
}

echo json_encode($response);

mysqli_close($conn);
?>