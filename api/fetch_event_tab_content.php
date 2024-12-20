<!-- EVENT VIEW!!!!! -->
<?php
// Enable comprehensive error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

// Log all incoming GET parameters for debugging
file_put_contents('debug_log.txt', 
    date('Y-m-d H:i:s') . " - Raw GET Parameters:\n" . 
    print_r($_GET, true) . "\n", 
    FILE_APPEND
);

$attractionId = $_GET['attractionId'] ?? null;
$eventId = $_GET['eventId'] ?? null;
$tabId = $_GET['tab'] ?? null;

// Detailed debugging 
file_put_contents('debug_log.txt', 
    date('Y-m-d H:i:s') . 
    " - Parsed Parameters:\n" .
    "AttractionID: " . ($attractionId ?? 'NULL') . "\n" .
    "EventID: " . ($eventId ?? 'NULL') . "\n" .
    "TabID: " . ($tabId ?? 'NULL') . "\n", 
    FILE_APPEND
);

// Absolute Paths 
$tabFiles = [
    'butiran-acara' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\event-details-complete.html',
    'galeri-acara' => 'C:\\xampp\\htdocs\\MARANGUIDE\\visitor\\event-gallery.html',
];

// More verbose validation
if ($attractionId === null) {
    http_response_code(400);
    file_put_contents('debug_log.txt', 
        date('Y-m-d H:i:s') . " - ERROR: attractionId is null\n", 
        FILE_APPEND
    );
    echo "Error: Missing attraction ID";
    exit;
}

if ($tabId === null) {
    http_response_code(400);
    file_put_contents('debug_log.txt', 
        date('Y-m-d H:i:s') . " - ERROR: tabId is null\n", 
        FILE_APPEND
    );
    echo "Error: Missing tab";
    exit;
}

// Validate tab
if (!isset($tabFiles[$tabId])) {
    http_response_code(404);
    file_put_contents('debug_log.txt', 
        date('Y-m-d H:i:s') . " - ERROR: Invalid tab $tabId\n", 
        FILE_APPEND
    );
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