<!-- ATTRACTION VIEW -->
<?php
// Enable comprehensive error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

$AttractionId = $_GET['id'] ?? null;
$tabId = $_GET['tab'] ?? null;

// Debugging 
file_put_contents('debug_log.txt', 
    date('Y-m-d H:i:s') . 
    " - Request: ID=$AttractionId, Tab=$tabId\n", 
    FILE_APPEND
);

// Absolute Paths c
$tabFiles = [
    'butiran-am' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\attraction-details-complete.html',
    'tarikan-acara' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\event-list.html',
    'galeri' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\gallery.html',
    'ulasan' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\comment-section.html',
    
    
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

// Check file existence with absolute path in case of errors
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