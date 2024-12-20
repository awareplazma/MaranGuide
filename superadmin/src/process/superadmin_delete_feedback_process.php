<?php
include $_SERVER['DOCUMENT_ROOT'] . '/maranguide_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    $_SESSION['error_message'] = "Unauthorized access";
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid admin ID";
    header("Location: superadmin_view_feedback.php");
    exit();
}

$attraction_id = (int)$_GET['id'];

try {
 
    $conn->begin_transaction();

   
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE feedback_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare select statement: " . $conn->error);
    }

    $stmt->bind_param("i", $feedback_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute select statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $attraction = $result->fetch_assoc();
    $stmt->close();

    
    $stmt = $conn->prepare("DELETE FROM feedback WHERE admin_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement: " . $conn->error);
    }

    $stmt->bind_param("i", $admin_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute delete statement: " . $stmt->error);
    }

    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Attraction not found");
    }

    
    $conn->commit();

    $_SESSION['success_message'] = "Attraction deleted successfully";

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error deleting attraction: " . $e->getMessage());
    $_SESSION['error_message'] = "Error deleting attraction: " . $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}


header("Location: superadmin_view_feedback.php");
exit();
?>