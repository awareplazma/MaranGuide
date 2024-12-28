<?php
error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';
ob_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();
        
        // Check for required fields
        if (empty($_POST['event_name']) || empty($_POST['event_description']) || 
            empty($_POST['event_start_date']) || 
            empty($_POST['event_end_date'])) {
            throw new Exception("All fields are required.");
        }

        // Retrieve attraction ID from session
        if (isset($_SESSION['attraction_id'])) {
            $attraction_id = $_SESSION['attraction_id'];
        } else {
            throw new Exception("Attraction ID is missing from the session.");
        }

        // Sanitize input
        $attraction_id = htmlspecialchars(trim($attraction_id));
        $event_name = htmlspecialchars(trim($_POST['event_name']));
        $event_description = htmlspecialchars(trim($_POST['event_description']));
        $event_start_date = htmlspecialchars(trim($_POST['event_start_date']));
        $event_end_date = htmlspecialchars(trim($_POST['event_end_date']));
        $event_thumbnails = null;

        // Fetch attraction name from database for folder creation
        $query = "SELECT attraction_name FROM attraction WHERE attraction_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $attraction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            throw new Exception("Attraction not found.");
        }

        $attraction_name = $row['attraction_name'];

        // Insert event into database first to get event_id
        $stmt = $conn->prepare("INSERT INTO eventlist 
            (attraction_id, event_created_at, event_name, event_description, 
             event_start_date, event_end_date, event_thumbnails, event_status) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?, 'Perlu Dilihat')");

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        
        $initial_thumbnails = '/media/default/placeholder.jpg';

        // Bind parameters and execute statement
        if (!$stmt->bind_param("isssss", 
            $attraction_id,  
            $event_name,    
            $event_description, 
            $event_start_date,  
            $event_end_date,
            $initial_thumbnails        
        )) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        $event_id = $conn->insert_id;

        // Set up directory paths
        $baseDir = "C:/xampp/htdocs/MARANGUIDE/media/attraction/{$attraction_name}/{$event_name}";
        
        $directories = [
            'videos' => $baseDir . '/videos/',
            'pictures' => $baseDir . '/pictures/',
            'thumbnail' => $baseDir . '/thumbnail/'
        ];

        // Create directories if they don't exist
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new Exception("Failed to create directory: " . $dir);
                }
            }
        }

        // Handle thumbnail upload with validation
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
            $allowed_picture_types = ['image/jpeg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['imageUpload']['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mime_type, $allowed_picture_types)) {
                $fileExtension = strtolower(pathinfo($_FILES['imageUpload']['name'], PATHINFO_EXTENSION));
                $fileName = "{$event_id}_thumbnail." . $fileExtension;
                $targetFilePath = $directories['thumbnail'] . $fileName;
                
                // Create the directory structure if it doesn't exist
                if (!file_exists(dirname($targetFilePath))) {
                    mkdir(dirname($targetFilePath), 0777, true);
                }
                
                if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $targetFilePath)) {
                    // Store the relative path in the database
                    $event_thumbnails = "/media/attraction/{$attraction_name}/{$event_name}/thumbnail/" . $fileName;
                    
                    // Update database with thumbnail path
                    $updateStmt = $conn->prepare("UPDATE eventlist SET event_thumbnails = ? WHERE event_id = ?");
                    
                    if (!$updateStmt) {
                        throw new Exception("Failed to prepare update statement: " . $conn->error);
                    }
                    
                    if (!$updateStmt->bind_param("si", $event_thumbnails, $event_id)) {
                        throw new Exception("Failed to bind update parameters: " . $updateStmt->error);
                    }
                    
                    if (!$updateStmt->execute()) {
                        throw new Exception("Failed to update thumbnail: " . $updateStmt->error);
                    }
                    
                    $updateStmt->close();
                } else {
                    throw new Exception("Failed to move uploaded thumbnail file.");
                }
            } else {
                throw new Exception("Invalid thumbnail file type. Only JPEG and PNG are allowed.");
            }
        }

        $stmt->close();
        $conn->commit();

        $_SESSION['success_message'] = "Event added successfully with media uploads!";
        header("Location: ../../admin_manage_events.php");
        exit();

    } catch (Exception $e) {
        // Rollback if error
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error in event processing: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../../admin_manage_events.php");
        exit();
    } 
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: ../../admin_manage_events.php");
    exit();
}