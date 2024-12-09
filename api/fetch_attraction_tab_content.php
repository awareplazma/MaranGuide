<?php
// Enable comprehensive error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

// Get attraction ID and tab from request
$AttractionId = $_GET['id'] ?? null;
$tabId = $_GET['tab'] ?? null;

// Log debugging information
file_put_contents('debug_log.txt', 
    date('Y-m-d H:i:s') . 
    " - Request: ID=$AttractionId, Tab=$tabId\n", 
    FILE_APPEND
);

// Define paths to included files - use absolute or relative paths carefully
$tabFiles = [
    'butiran-am' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\attraction-details-complete.html',
    'tarikan-acara' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\event-list.html',
    'ulasan' => '/attraction-comment.html'
];

// Validate input
if (!$AttractionId || !$tabId) {
    http_response_code(400);
    echo "Error: Missing attraction ID or tab";
    exit;
}

// Validate tab
if (!isset($tabFiles[$tabId])) {
    http_response_code(404);
    echo "Error: Invalid tab $tabId";
    exit;
}

// Get file path
$filePath = $tabFiles[$tabId];

// Check file existence with absolute path
$fullPath = realpath($filePath);

if (!$fullPath || !file_exists($fullPath)) {
    http_response_code(500);
    
    // Log detailed error
    file_put_contents('debug_log.txt', 
        "File not found: Attempted $filePath\n" .
        "Current directory: " . getcwd() . "\n" .
        "Full attempted path: $fullPath\n", 
        FILE_APPEND
    );
    
    echo "Error: Content file not found: $filePath";
    exit;
}

// Output file contents
readfile($fullPath);
?>