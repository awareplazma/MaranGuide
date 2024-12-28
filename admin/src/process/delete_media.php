<!-- Not working: Logic only deletes file but not databse row ID -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'C:/xampp/htdocs/MARANGUIDE/maranguide_connection.php';

// Absolute path of log files for error. 
$logDir = $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/logs/';
$logFile = $logDir . 'delete_media_log.txt';


if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

if (!file_exists($logFile)) {
    file_put_contents($logFile, "Log file created: " . date('Y-m-d H:i:s') . "\n");
}

$mediaId = '';
if (isset($_POST['media_id']) && !empty($_POST['media_id'])) {
    $mediaId = $_POST['media_id'];
} elseif (isset($_GET['delete_media_id']) && !empty($_GET['delete_media_id'])) {
    $mediaId = $_GET['delete_media_id'];
}

if (!empty($mediaId)) {
    error_log("Attempting to delete media with ID: $mediaId\n", 3, $logFile);

    // Sanitize input
    $mediaId = mysqli_real_escape_string($conn, $mediaId);

    $selectStmt = $conn->prepare("SELECT media_path FROM attraction_media WHERE media_id = ?");
    $selectStmt->bind_param("i", $mediaId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $mediaData = $result->fetch_assoc();
    $selectStmt->close();

    if ($mediaData) {
        $mediaPath = $mediaData['media_path'];
        
        // Check if the physical file exists
        if (file_exists($mediaPath)) {
            error_log("Media file found: $mediaPath\n", 3, $logFile);

            // Try deleting the file
            if (unlink($mediaPath)) {
                error_log("File deleted successfully: $mediaPath\n", 3, $logFile);

                $deleteStmt = $conn->prepare("DELETE FROM attraction_media WHERE media_id = ?");
                $deleteStmt->bind_param("i", $mediaId);

                if ($deleteStmt->execute()) {
                    error_log("Database record deleted for media ID: $mediaId\n", 3, $logFile);
                    
                    if (isset($_POST['media_id'])) {
                        echo 'success';
                    } else {
                        $_SESSION['success_message'] = "Media deleted successfully!";
                        header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
                        exit();
                    }
                } else {
                    error_log("Database deletion failed for media ID: $mediaId. Error: " . $deleteStmt->error . "\n", 3, $logFile);
                    if (isset($_POST['media_id'])) {
                        echo 'error';
                    } else {
                        $_SESSION['error_message'] = "Failed to delete database record!";
                        header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
                        exit();
                    }
                }
                $deleteStmt->close();
            } else {
                error_log("Failed to delete file: $mediaPath\n", 3, $logFile);
                if (isset($_POST['media_id'])) {
                    echo 'error';
                } else {
                    $_SESSION['error_message'] = "Failed to delete file!";
                    header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
                    exit();
                }
            }
        } else {
            error_log("Media file not found: $mediaPath\n", 3, $logFile);
            if (isset($_POST['media_id'])) {
                echo 'error';
            } else {
                $_SESSION['error_message'] = "Media file not found!";
                header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
                exit();
            }
        }
    } else {
        error_log("No database record found for media ID: $mediaId\n", 3, $logFile);
        if (isset($_POST['media_id'])) {
            echo 'error';
        } else {
            $_SESSION['error_message'] = "Media record not found in database!";
            header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
            exit();
        }
    }
} else {
    error_log("No media_id provided.\n", 3, $logFile);
    if (isset($_POST['media_id'])) {
        echo 'error';
    } else {
        $_SESSION['error_message'] = "No media ID provided!";
        header("Location: " . $_SERVER['HTTP_SOURCE_PAGE']);
        exit();
    }
}


mysqli_close($conn);
?>