<?php
error_reporting(E_ALL);

//Might have problem as using relative
include '../../../maranguide_connection.php';
ob_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['attraction_name']) || empty($_POST['attraction_description']) || 
            empty($_POST['attraction_address']) || empty($_POST['opening_time']) || 
            empty($_POST['closing_time']) || empty($_POST['operating_days'])){
            throw new Exception("All fields are required");
        }

        // Handle operating days array
        $operating_days = is_array($_POST['operating_days']) 
            ? implode(', ', $_POST['operating_days']) 
            : $_POST['operating_days'];

        // Sanitize input
        $attraction_name = htmlspecialchars(trim($_POST['attraction_name']));
        $attraction_description = htmlspecialchars(trim($_POST['attraction_description']));
        $attraction_address = htmlspecialchars(trim($_POST['attraction_address']));
        $attraction_operating_days = htmlspecialchars($operating_days); 
        $attraction_opening_hours = htmlspecialchars(trim($_POST['opening_time']));
        $attraction_closing_hours = htmlspecialchars(trim($_POST['closing_time']));

        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Start transaction
        $conn->begin_transaction();

        // Default thumbnail path
        $attraction_thumbnails = realpath($targetFilePath);


        // Modified SQL to use proper parameter count
        $stmt = $conn->prepare("INSERT INTO attraction 
        (attraction_name, attraction_description, attraction_created_at, 
        attraction_address, attraction_operating_days, attraction_opening_hours, 
        attraction_closing_hours, attraction_status, attraction_thumbnails, attraction_latitude, 
        attraction_longitude, admin_id) 
        VALUES (?, ?, NOW(), ?, ?, ?, ?, 'Perlu Dilihat', ?, ?, ?, NULL)");

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Sanitize and validate coordinates
        $latitude = !empty($_POST['attraction_latitude']) ? floatval($_POST['attraction_latitude']) : null;
        $longitude = !empty($_POST['attraction_longitude']) ? floatval($_POST['attraction_longitude']) : null;

        if (!$stmt->bind_param("sssssssdd", 
        $attraction_name, 
        $attraction_description, 
        $attraction_address, 
        $attraction_operating_days,
        $attraction_opening_hours,
        $attraction_closing_hours,
        $attraction_thumbnails, // Always set to default
        $latitude, 
        $longitude
        )) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        // Get the new attraction_id
        $attraction_id = $conn->insert_id;

        // Base directory paths with improved security
        $baseDir = realpath("C:/xampp/htdocs/MARANGUIDE/media/attraction/") . DIRECTORY_SEPARATOR . preg_replace('/[^a-zA-Z0-9_\s-]/', '', $attraction_name) . DIRECTORY_SEPARATOR;
        $directories = [
            'videos' => $baseDir . 'videos' . DIRECTORY_SEPARATOR,
            'pictures' => $baseDir . 'pictures' . DIRECTORY_SEPARATOR,
            'thumbnail' => $baseDir . 'thumbnail' . DIRECTORY_SEPARATOR
        ];

        // Create directories with proper permissions
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new Exception("Failed to create directory: " . $dir);
                }
            }
        }

        // Handle thumbnail upload with improved validation
        if (isset($_FILES['ThumbnailUpload']) && $_FILES['ThumbnailUpload']['error'] == 0) {
            $allowed_picture_types = ['image/jpeg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['ThumbnailUpload']['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mime_type, $allowed_picture_types)) {
                $fileExtension = strtolower(pathinfo($_FILES['ThumbnailUpload']['name'], PATHINFO_EXTENSION));
                $fileName = "{$attraction_id}_thumbnail." . $fileExtension;
                $targetFilePath = $directories['thumbnail'] . $fileName;
                $dbFilePath = "/media/attraction/{$attraction_name}/thumbnail/" . $fileName;

                if (move_uploaded_file($_FILES['ThumbnailUpload']['tmp_name'], $targetFilePath)) {
                    $attraction_thumbnails = $dbFilePath;
                }
            }
        }

        // Update database with file path if changed
        $updateStmt = $conn->prepare("UPDATE attraction SET attraction_thumbnails = ? WHERE attraction_id = ?");

        if (!$updateStmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        if (!$updateStmt->bind_param("si", $attraction_thumbnails, $attraction_id)) {
            throw new Exception("Failed to bind update parameters: " . $updateStmt->error);
        }
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update thumbnail: " . $updateStmt->error);
        }

        $updateStmt->close();
        $conn->commit();
        $stmt->close();

        $_SESSION['success_message'] = "Attraction added successfully with media uploads!";
        header("Location: ../../superadmin_manage_attraction.php");
        exit();

    } catch (Exception $e) {

        // Rollback on error
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error in attraction processing: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: /superadmin/superadmin_manage_attraction.php");
        exit();
    } 
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: /superadmin/superadmin_add_attraction.php");
    exit();
}
?>