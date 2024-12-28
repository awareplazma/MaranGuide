<?php
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';


if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $event_id = $_POST['event_id'] ?? null;
        if (!$event_id) {
            throw new Exception("Event ID is required");
        }

        $event_name = trim($_POST['event_name'] ?? '');
        $event_desc = trim($_POST['event_description'] ?? '');
        $event_start_date = $_POST['event_start_date'] ?? '';
        $event_end_date = $_POST['event_end_date'] ?? '';
        $event_status = $_POST['event_status'] ?? '';

        if (empty($event_name) || empty($event_desc) || empty($event_start_date) || empty($event_end_date)) {
            throw new Exception("Please fill in all required fields");
        }

        $event_media = null;
        if (isset($_FILES['event_media']) && $_FILES['event_media']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['event_media'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];

            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception("Invalid file type.");
            }

            $upload_dir = '../media/attraction/events/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'event_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $event_media = 'media/events/' . $new_filename;

                $query = "SELECT event_media FROM eventlist WHERE event_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $event_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $old_media);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                if ($old_media && file_exists('../' . $old_media)) {
                    unlink('../' . $old_media);
                }
            } else {
                throw new Exception("Failed to upload media file");
            }
        }

        $sql = "UPDATE eventlist SET 
                event_name = ?, 
                event_description = ?, 
                event_start_date = ?, 
                event_end_date = ?, 
                event_status = ?";
        
        $params = [$event_name, $event_desc, $event_start_date, $event_end_date, $event_status];
        
        if ($event_media) {
            $sql .= ", event_media = ?";
            $params[] = $event_media;
        }

        $sql .= " WHERE event_id = ?";
        $params[] = $event_id;

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['success'] = "Event updated successfully";
        header("Location: ../../admin_manage_events.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../admin_edit_event.php?id=" . $event_id);
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: ../../admin_manage_events.php");
    exit();
}
