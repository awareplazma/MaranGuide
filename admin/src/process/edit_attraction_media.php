<?php
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

function isValidFileType($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
    return in_array($file['type'], $allowed_types);
}

// Function to generate unique filename. Change if needed
function generateUniqueFilename($original_name, $file_extension) {
    return uniqid() . '_' . time() . '.' . $file_extension;
}

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate attraction ID
        if (!isset($_POST['attraction_id']) || empty($_POST['attraction_id'])) {
            throw new Exception('Invalid attraction ID');
        }
        
        $attraction_id = intval($_POST['attraction_id']);

        // Fetch attraction name from database
        $stmt = mysqli_prepare($conn, "SELECT attraction_name FROM attraction WHERE attraction_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $attraction_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
         if (!($row = mysqli_fetch_assoc($result))) {
            throw new Exception('Attraction not found');
        }

        $attraction_name = $row['attraction_name'];


        // Use the media title and description from the form
        $media_title = trim($_POST['media_title']);
        $media_description = trim($_POST['media_description']); 
        
        // Validate media title
        if (empty($media_title)) {
            throw new Exception('Media title is required');
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['attraction_media']) || $_FILES['attraction_media']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('No file was uploaded');
        }
        
        $file = $_FILES['attraction_media'];
        
        // Validate file upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed: ' . $file['error']);
        }
        
        // Validate file type
        if (!isValidFileType($file)) {
            throw new Exception('Invalid file type. Only images (JPEG, PNG, GIF) and videos (MP4, WebM) are allowed');
        }
        
        // Validate file size (max 10MB)
        $max_size = 10 * 1024 * 1024; // 10MB in bytes
        if ($file['size'] > $max_size) {
            throw new Exception('File size exceeds maximum limit of 10MB');
        }
        
        // Defining separate upload directories for images and videos
        $image_upload_dir = $_SERVER['DOCUMENT_ROOT']. '/MARANGUIDE/media/attraction/' .$attraction_name . '/pictures';
        $video_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/media/attraction/'. $attraction_name . '/videos';
        
        // Determining media type and upload directory
        $is_video = strpos($file['type'], 'video/') === 0;
        $media_type = $is_video ? 'video' : 'image';
        $upload_dir = $is_video ? $video_upload_dir : $image_upload_dir;
        
        // Check upload directory exists
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
        
        // Generate unique filename. UChange name if necessary
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = generateUniqueFilename($file['name'], $file_extension);
        $file_path = $upload_dir . '/' . $new_filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // Set the media path
        $media_path = $upload_dir . '/' . $new_filename;
        
        // Insert media record
        $sql = "INSERT INTO attraction_media (
                    attraction_id, 
                    media_path, 
                    media_type, 
                    media_title, 
                    media_description, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = mysqli_prepare($conn, $sql);
        
     
        if ($stmt === false) {
            die('MySQLi prepare error: ' . mysqli_error($conn));
        }

      
        mysqli_stmt_bind_param($stmt, 'issss', $attraction_id, $media_path, $media_type, $media_title, $media_description);

        
        if (!mysqli_stmt_execute($stmt)) {
            die('MySQLi execute error: ' . mysqli_error($conn));
        }
        
        // Success message and redirection
        $_SESSION['success_message'] = 'Media uploaded successfully';
        
        
        header("Location: ../../admin_manage_attraction.php?id=" . $attraction_id);
        exit();
        
    } else {
        throw new Exception('Invalid request method');
    }
    
} catch (Exception $e) {
    // Error log just in case
    error_log('Media Upload Error: ' . $e->getMessage());
    
    // Error message and redirection
    $_SESSION['error_message'] = $e->getMessage();
    
    
    if (isset($attraction_id)) {
        header("Location: ../../admin_manage_attraction.php?id=" . $attraction_id);
    } else {
        header("Location: ../../admin/admin_manage_attraction.php");
    }
    exit();
}
?>
