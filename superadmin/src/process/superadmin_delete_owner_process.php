<?php
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    $_SESSION['error_message'] = "Unauthorized access";
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid admin ID";
    header("Location: ../../superadmin_manage_owner.php");
    exit();
}

$owner_id = (int)$_GET['id'];

try {
    $conn->begin_transaction();

    // Get admin profile picture path but the feature is not yet implemented
    $stmt = $conn->prepare("SELECT admin_profile_picture FROM adminlist WHERE admin_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare select statement: " . $conn->error);
    }

    $stmt->bind_param("i", $owner_id); 
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute select statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $owner = $result->fetch_assoc();
    $stmt->close();

    // Delete the owner from the adminlist table
    $stmt = $conn->prepare("DELETE FROM adminlist WHERE admin_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement: " . $conn->error);
    }

    $stmt->bind_param("i", $owner_id);  // Use $owner_id here too
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute delete statement: " . $stmt->error);
    }

    // Check if the deletion was successful
    if ($stmt->affected_rows === 0) {
        throw new Exception("Owner not found");
    }

    // Delete the profile picture if it exists
    if ($owner && !empty($owner['admin_profile_picture'])) {
        $image_path = $_SERVER['DOCUMENT_ROOT'] . $owner['admin_profile_picture'];  // Make sure to use the full server path
        if (file_exists($image_path)) {
            if (!unlink($image_path)) {
                error_log("Failed to delete image file: " . $image_path);
            }
        }
    }

    // Commit the transaction
    $conn->commit();

    $_SESSION['success_message'] = "Owner deleted successfully";

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error deleting Owner: " . $e->getMessage());
    $_SESSION['error_message'] = "Error deleting Owner: " . $e->getMessage();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

// Redirect to the owner management page
header("Location: ../../superadmin_manage_owner.php");
exit();
?>
