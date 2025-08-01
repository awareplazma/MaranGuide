<?php
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
} else {
    throw new Exception("Admin ID is missing from the session.");
}

include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        
        if (empty($_GET['comment_id'])) {
            throw new Exception("Comment ID not provided.");
        }

        
        $comment_id = intval($_GET['comment_id']);

    
        $conn->begin_transaction();

        
        $stmt = $conn->prepare("UPDATE comments SET approval_status = 'Tidak Lulus' WHERE comment_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        
        if (!$stmt->bind_param("i", $comment_id)) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        $stmt->close();
        $conn->commit();

        // Success message and redirect
        $_SESSION['error_message'] = "Comment disapproved successfully!";
        header("Location: ../../view_feedback.php");
        exit();

    } catch (Exception $e) {
        // Rollback if error
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error updating comment approval status: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../../view_feedback.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: ../../view_feedback.php");
    exit();
}
