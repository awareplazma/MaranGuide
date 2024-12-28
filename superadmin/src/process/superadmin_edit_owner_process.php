<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['admin_name']) || empty($_POST['admin_password']) || 
            empty($_POST['admin_email']) || empty($_POST['admin_phone_number'])) {
            throw new Exception("All fields are required");
        }

        $admin_id = $_POST['admin_id'];
        $admin_name = htmlspecialchars(trim($_POST['admin_name']));
        $admin_password = htmlspecialchars(trim($_POST['admin_password']));
        $admin_email = htmlspecialchars(trim($_POST['admin_email']));
        $admin_phone_number = htmlspecialchars($_POST['admin_phone_number']);
        //Admin Profile Picture
        $admin_profile_pictrue = null;
        
        // Handle image upload
        if (isset($_FILES['ProfilePictureUpload']) && $_FILES['ProfilePictureUpload']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png'];
            if (!in_array($_FILES['ProfilePictureUpload']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed.");
            }

            $targetDir = $_SERVER['DOCUMENT_ROOT']."../Media/admin_pfp/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES["ProfilePictureUpload"]["name"], PATHINFO_EXTENSION);
            $fileName = uniqid() . $admin_name . $fileExtension;
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["ProfilePictureUpload"]["tmp_name"], $targetFilePath)) {
                $admin_profile_picture = $targetFilePath;
            } else {
                throw new Exception("Failed to upload image");
            }
        }

        $sql = "UPDATE adminlist 
        SET admin_name = ?, 
            admin_password = ?, 
            admin_email = ?, 
            admin_phone_number = ?
            WHERE admin_id = ?";
            

        $stmt = $conn->prepare($sql);

        if ($admin_profile_picture) {
            $stmt->bind_param("sssssi", 
                $admin_name,
                $admin_password,
                $admin_email,
                $admin_phone_number,
                $admin_profile_picture,
                $admin_id
            );
        } else {
            $stmt->bind_param("ssssi", 
                $admin_name,
                $admin_password,
                $admin_email,
                $admin_phone_number,
                $admin_id
            );
        }

        if (!$stmt->execute()) {
            throw new Exception("Gagal: " . $stmt->error);
        }

        $_SESSION['success_message'] = "Pemilik updated successfully!";
        header("Location: ../../superadmin_manage_owner.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../../superadmin_manage_owner.php");
        exit();
    }
} else {
    header("Location: ../../superadmin_manage_owner.php");
    exit();
}
?>