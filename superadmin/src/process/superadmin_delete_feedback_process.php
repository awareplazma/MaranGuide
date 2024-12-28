<?php
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid feedback ID";
    header("Location: superadmin_view_feedback.php");
    exit();
}

$feedback_id = (int)$_GET['id'];  

try {
    $conn->begin_transaction();

    // Get feedback details
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE feedback_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare select statement: " . $conn->error);
    }

    $stmt->bind_param("i", $feedback_id);  
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute select statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();  
    $stmt->close();

    // Delete feedback
    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement: " . $conn->error);
    }

    $stmt->bind_param("i", $feedback_id);  
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute delete statement: " . $stmt->error);
    }

    // Check if the deletion was successful
    if ($stmt->affected_rows === 0) {
        throw new Exception("Feedback not found");
    }

   
    $conn->commit();

    $_SESSION['success_message'] = "Feedback deleted successfully";  

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error deleting feedback: " . $e->getMessage());
    $_SESSION['error_message'] = "Error deleting feedback: " . $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

// Redirect to the feedback management page
header("Location: ../../superadmin_view_feedback.php");
exit();
?>
