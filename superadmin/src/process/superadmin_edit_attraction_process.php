<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['attraction_name']) || empty($_POST['attraction_description']) || 
            empty($_POST['attraction_address']) || empty($_POST['opening_time']) || 
            empty($_POST['closing_time']) || empty($_POST['attraction_status'])) {
            throw new Exception("All fields are required");
        }

        // Get and sanitize form data
        $attraction_id = $_POST['attraction_id'];
        $admin_id = $_POST['admin_id'];
        $attraction_name = htmlspecialchars(trim($_POST['attraction_name']));
        // This is used for file path formatting
        $attraction_thumbnail_insert = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $attraction_name);
        $attraction_description = htmlspecialchars(trim($_POST['attraction_description']));
        $attraction_address = htmlspecialchars(trim($_POST['attraction_address']));
        $attraction_opening_hours = htmlspecialchars(trim($_POST['opening_time']));
        $attraction_closing_hours = htmlspecialchars(trim($_POST['closing_time']));
        $attraction_status = htmlspecialchars(trim($_POST['attraction_status']));
        
        // Handle operating days array
        $operating_days = isset($_POST['attraction_operating_days']) ? 
            implode(',', $_POST['attraction_operating_days']) : '';
        
        $attraction_thumbnails = null;
        
        //Edit Gambar
        if($remove_thumbnail)
        {
            print "Perlu Muka Depan!";
        }

        // Handle image upload
        if (isset($_FILES['ThumbnailUpload']) && $_FILES['ThumbnailUpload']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png'];
            if (!in_array($_FILES['ThumbnailUpload']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Hanya JPG dan PNG dibenarkan.");
            }

            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/MARANGUIDE/media/attraction/{$attraction_thumbnail_insert}/thumbnail/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES["ThumbnailUpload"]["name"], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["ThumbnailUpload"]["tmp_name"], $targetFilePath)) {
                $attraction_thumbnails = $targetFilePath;
                $attraction_thumbnails = "/media/attraction/{$attraction_thumbnail_insert}/thumbnail/" . $fileName;
            } else {
                throw new Exception("Failed to upload image");
            }
        }

        // Handle coordinates
        $latitude = !empty($_POST['attraction_latitude']) ? $_POST['attraction_latitude'] : null;
        $longitude = !empty($_POST['attraction_longitude']) ? $_POST['attraction_longitude'] : null;

        // Prepare SQL query
        $sql = "UPDATE attraction SET 
                admin_id = ?,
                attraction_name = ?, 
                attraction_description = ?, 
                attraction_address = ?, 
                attraction_opening_hours = ?,
                attraction_closing_hours = ?,
                attraction_status = ?,
                attraction_latitude = ?,
                attraction_longitude = ?,
                attraction_operating_days = ?";

        // Add thumbnail column if new thumbnail exists
        if ($attraction_thumbnails) {
            $sql .= ", attraction_thumbnails = ?";
        }

        $sql .= " WHERE attraction_id = ?";

        $stmt = $conn->prepare($sql);
        
        // Bind parameters based on whether thumbnail exists
        if ($attraction_thumbnails) {
            $stmt->bind_param("issssssssssi", 
                $admin_id,
                $attraction_name,
                $attraction_description,
                $attraction_address,
                $attraction_opening_hours,
                $attraction_closing_hours,
                $attraction_status,
                $latitude,
                $longitude,
                $operating_days,
                $attraction_thumbnails,
                $attraction_id
            );
        } else {
            $stmt->bind_param("isssssssssi", 
                $admin_id,
                $attraction_name,
                $attraction_description,
                $attraction_address,
                $attraction_opening_hours,
                $attraction_closing_hours,
                $attraction_status,
                $latitude,
                $longitude,
                $operating_days,
                $attraction_id
            );
        }

        // Execute the update query
        if (!$stmt->execute()) {
            throw new Exception("Failed to update attraction: " . $stmt->error);
        }

        $_SESSION['success_message'] = "Attraction updated successfully!";
        header("Location: ../../superadmin_manage_attraction.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../../superadmin_manage_attraction.php");
        exit();
    }
} else {
    header("Location: ../../superadmin_manage_attraction.php");
    exit();
}
?>