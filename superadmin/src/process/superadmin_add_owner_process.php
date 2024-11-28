<?php
include '../maranguide_connection.php'; 
ob_start(); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        if (empty($_POST['owner_name']) || empty($_POST['owner_password']) || empty($_POST['owner_email'])) {
            throw new Exception("All fields are required");
        }

       
        $owner_name = trim($_POST['owner_name']);
        $admin_password = trim($_POST['owner_password']);
        $admin_email = trim($_POST['owner_email']);
        $admin_phone_number = trim($_POST['owner_phone_number']);
        $admin_profile_picture = NULL;

        
        if (isset($_FILES['profilepictureUpload']) && $_FILES['profilepictureUpload']['error'] == 0) {
            $targetDir = "..\media\owner_pfp";
            $fileName = basename($_FILES["profilepictureUpload"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            
            if (move_uploaded_file($_FILES["profilepictureUpload"]["tmp_name"], $targetFilePath)) {
                $admin_profile_picture = $targetFilePath;
            } else {
                throw new Exception("Failed to upload profile picture");
            }
        }

        $stmt = $conn->prepare("INSERT INTO adminlist 
            (admin_password, admin_email, admin_name, admin_profile_picture, admin_phone_number, admin_role) 
            VALUES (?, ?, ?, ?, ?, 'owner')");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

      
        if (!$stmt->execute([$admin_password, $admin_email, $owner_name, $admin_profile_picture, $admin_phone_number])) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        
        $_SESSION['success_message'] = "Admin added successfully!";
        header("Location: superadmin_manage_owner.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: superadmin_manage_owner.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: superadmin_manage_owner.php");
    exit();
}
?>
