<?php
// basedir.php - THE ONLY FILE WE MODIFY
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__FILE__));
}

// Get the current script's directory path and standardize slashes
$currentPath = str_replace('\\', '/', dirname(__FILE__));

// Find htdocs position and set base directory
$htdocsPos = strpos($currentPath, 'htdocs');
if ($htdocsPos !== false) {
    // Get everything from htdocs onwards, ensuring proper slashes
    $baseDir = substr($currentPath, 0, $htdocsPos + 6); // Get up to htdocs
    $projectPath = substr($currentPath, $htdocsPos + 6); // Get everything after htdocs
    $baseDir = $baseDir . '/' . trim($projectPath, '/') . '/';
} else {
    $baseDir = $currentPath . '/';
}

// Define constants that other files will use without needing changes
define('BASE_DIR', $baseDir);
define('FULL_BASE_DIR', $baseDir);