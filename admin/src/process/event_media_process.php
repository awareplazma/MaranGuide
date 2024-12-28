<?php
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

function isValidFileType($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
    return in_array($file['type'], $allowed_types);
}

// Function to generate unique filename. Change if necessary
function generateUniqueFilename($original_name, $file_extension) {
    return uniqid() . '_' . time() . '.' . $file_extension;
}

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
            throw new Exception('Invalid event ID');
        }
        
        $event_id = intval($_POST['event_id']);

        // Fetch event name from database
        $stmt = mysqli_prepare($conn, "SELECT event_name FROM eventlist WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
         if (!($row = mysqli_fetch_assoc($result))) {
            throw new Exception('Attraction not found');
        }

        $event_name = $row['event_name'];

        // Fetch attraction name from database

        $stmt = mysqli_prepare($conn, "SELECT a.attraction_name 
        FROM attraction a 
        JOIN eventlist e ON a.attraction_id = e.attraction_id 
        WHERE e.event_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $event_id);
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
        if (!isset($_FILES['event_media']) || $_FILES['event_media']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('No file was uploaded');
        }
        
        $file = $_FILES['event_media'];
        
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
        
        // Define separate upload directories for images and videos
        $image_upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/MARANGUIDE/media/attraction/{$attraction_name}/{$event_name}/pictures";
        $video_upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/MARANGUIDE/media/attraction/{$attraction_name}/{$event_name}/videos";
        
        // Determine media type and upload directory
        $is_video = strpos($file['type'], 'video/') === 0;
        $media_type = $is_video ? 'video' : 'image';
        $upload_dir = $is_video ? $video_upload_dir : $image_upload_dir;
        
        // Ensure upload directory exists
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
        
        // Generate unique filename Ubah nanti ikut nama
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = generateUniqueFilename($file['name'], $file_extension);
        $file_path = $upload_dir . '/' . $new_filename;
        $dbFilePath = "/media/attraction/{$attraction_name}/{$event_name}/thumbnail/" . $fileName;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        // Set the media path
        $media_path = $upload_dir . '/' . $new_filename;
        
        // Insert media record
        $sql = "INSERT INTO event_media (
                    event_id, 
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

     
        mysqli_stmt_bind_param($stmt, 'issss', $event_id, $media_path, $media_type, $media_title, $media_description);

   
        if (!mysqli_stmt_execute($stmt)) {
            die('MySQLi execute error: ' . mysqli_error($conn));
        }
        
        // Success message and redirection
        $_SESSION['success_message'] = 'Media uploaded successfully';
        
        // Redirect back to event page
        header("Location: /MARANGUIDE/admin/admin_manage_events.php?id=" . $event_id);
        exit();
        
    } else {
        throw new Exception('Invalid request method');
    }
    
} catch (Exception $e) {
    // Log the error just in case
    error_log('Media Upload Error: ' . $e->getMessage());
    
    // Error message and redirection
    $_SESSION['error_message'] = $e->getMessage();
    
   
    if (isset($event_id)) {
        header("Location: /MARANGUIDE/admin/admin_manage_events.php?id=" . $event_id);
    } else {
        header("Location: /MARANGUIDE/admin/admin_manage_events.php");
    }
    exit();
}
?>
