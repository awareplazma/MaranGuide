<?php
include $_SERVER['DOCUMENT_ROOT'] . '/maranguide_connection.php';

$event_id = (int)$_GET['id'];

try {

    $conn->begin_transaction();
    $stmt = $conn->prepare("SELECT media_id FROM event_media WHERE event_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare select statement: " . $conn->error);
    }

    $stmt->bind_param("i", $event_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute select statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM eventlist WHERE event_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement: " . $conn->error);
    }

    $stmt->bind_param("i", $event_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute delete statement: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Event not found");
    }


    if ($event && !empty($event['event_media'])) {
        $image_path = $event['event_media'];
        if (file_exists($image_path)) {
            if (!unlink($image_path)) { 
                error_log("Failed to delete image file: " . $image_path);
            }
        }
    }


    $conn->commit();

    $_SESSION['success_message'] = "Event deleted successfully";

} catch (Exception $e) {
     
    $conn->rollback();
    error_log("Error deleting event: " . $e->getMessage());
    $_SESSION['error_message'] = "Error deleting event: " . $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

header("Location: ../../admin_manage_events.php");
exit();
?>