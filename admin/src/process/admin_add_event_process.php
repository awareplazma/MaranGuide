<?php
error_reporting(E_ALL);

include 'admin_nav.php';
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
        $attraction_id = htmlspecialchars(trim($_POST['attraction_id']));
        $event_name = htmlspecialchars(trim($_POST['event_name']));
        $event_description = htmlspecialchars(trim($_POST['event_description']));
        $event_start_date = htmlspecialchars(trim($_POST['event_start_date']));
        $event_end_date = htmlspecialchars(trim($_POST['event_end_date']));
        $event_thumbnails = null;

        // Handle file upload
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['imageUpload']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed.");
            }

            $targetDir = "../media/($attraction_id)/picture"; 
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Generate unique filename and move file
            $fileExtension = pathinfo($_FILES["imageUpload"]["name"], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $targetFilePath)) {
                $event_thumbnails = $targetFilePath;
            } else {
                throw new Exception("Failed to upload image.");
            }
        }

        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Prepare SQL statement for insertion
        $stmt = $conn->prepare("INSERT INTO eventlist 
            (attraction_id, event_created_at, event_name, event_description, 
             event_start_date, event_end_date, event_thumbnails, event_status) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?, 'Perlu Dilihat')");

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Bind parameters and execute statement
        if (!$stmt->bind_param("isssss", 
            $attraction_id,  
            $event_name,    
            $event_description, 
            $event_start_date,  
            $event_end_date,
            $event_thumbnails        
        )) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        $event_id = $conn->insert_id;

        $baseDir = "C:/xampp/htdocs/MARANGUIDE/media/attraction/{$attraction_id}/{$event_id}";
        $directories = [
            'pictures' => $baseDir . 'pictures/',
            'videos' => $baseDir . 'videos/',
            'thumbnail' => $baseDir . 'thumbnail/'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new Exception("Failed to create directory: " . $dir);
                }
            }
        }

        $uploadedFiles = [
            'pictures' => [],
            'videos' => [],
            'thumbnail' => []
        ];

        if (isset($_FILES['thumbnailUpload']) && $_FILES['thumbnailUpload']['error'] == 0) {
            $allowed_picture_types = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($_FILES['thumbnailUpload']['type'], $allowed_picture_types)) {
                throw new Exception("Invalid file type for thumbnail. Only JPG, PNG, and GIF are allowed.");
            }

            $fileExtension = pathinfo($_FILES['thumbnailUpload']['name'], PATHINFO_EXTENSION);
            $fileName = "attraction_{$attraction_id}_thumbnail." . $fileExtension;
            $targetFilePath = $directories['thumbnail'] . $fileName;
            $dbFilePath = "media/attraction/{$attraction_id}/thumbnail/" . $fileName;

            if (move_uploaded_file($_FILES['thumbnailUpload']['tmp_name'], $targetFilePath)) {
                $uploadedFiles['thumbnail'] = $dbFilePath;
            } else {
                throw new Exception("Failed to upload thumbnail: " . error_get_last()['message']);
            }
        }
        if (!empty($uploadedFiles['thumbnail'])) {
            $thumbnail = $uploadedFiles['thumbnail'];
            $updateStmt = $conn->prepare("UPDATE eventlist SET 
                attraction_thumbnail = ?
                WHERE event_id = ?");

            if (!$updateStmt) {
                throw new Exception("Failed to prepare update statement: " . $conn->error);
            }

            if (!$updateStmt->execute([$picturesJson, $videosJson, $thumbnail, $attraction_id])) {
                throw new Exception("Failed to update media paths: " . $updateStmt->error);
            }

            $updateStmt->close();
        }

        // Commit transaction
        $conn->commit();
        $stmt->close();

        $_SESSION['success_message'] = "Event added successfully!";
        header("Location: admin_manage_events.php");
        exit();

    } catch (Exception $e) {
         if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error in attraction processing: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: admin_manage_events.php");
        exit();
    } 
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: admin_manage_events.php");
    exit();
}
?>
