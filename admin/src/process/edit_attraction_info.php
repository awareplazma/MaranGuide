<?php
ini_set('display_errors', 0);    
ini_set('log_errors', 1);         // Logging of errors in apache.conf
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/php_error.log');
error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE/maranguide_connection.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        
        $attraction_id = $_POST['attraction_id'] ?? null;
        if (!$attraction_id) {
            throw new Exception("Attraction ID is required");
        }

      
        $updateFields = [];
        $params = [];

        // Only add fields that were submitted and not empty
        if (isset($_POST['attraction_name']) && trim($_POST['attraction_name']) !== '') {
            $updateFields[] = "attraction_name = ?";
            $params[] = trim($_POST['attraction_name']);
        }

        if (isset($_POST['attraction_description']) && trim($_POST['attraction_description']) !== '') {
            $updateFields[] = "attraction_description = ?";
            $params[] = trim($_POST['attraction_description']);
        }

        if (isset($_POST['attraction_start_hours']) && $_POST['attraction_start_hours'] !== '') {
            $updateFields[] = "attraction_opening_hours = ?";
            $params[] = $_POST['attraction_start_hours'];
        }

        if (isset($_POST['attraction_closing_hours']) && $_POST['attraction_closing_hours'] !== '') {
            $updateFields[] = "attraction_closing_hours = ?";
            $params[] = $_POST['attraction_closing_hours'];
        }

        if (isset($_POST['attraction_status']) && $_POST['attraction_status'] !== '') {
            $updateFields[] = "attraction_status = ?";
            $params[] = $_POST['attraction_status'];
        }

        if (isset($_POST['operating_days'])) {
            $updateFields[] = "attraction_operating_days = ?";
            $params[] = implode(',', $_POST['operating_days']);
        }

        // Only proceed if there are fields to update
        if (!empty($updateFields)) {
            // Build the SQL query dynamically
            $sql = "UPDATE attraction SET " . implode(", ", $updateFields) . " WHERE attraction_id = ?";
            
            // Add attraction_id to params array
            $params[] = $attraction_id;

            // Prepare and execute the statement
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt === false) {
                throw new Exception("Failed to prepare the SQL statement");
            }

            // Bind parameters dynamically
            $param_types = str_repeat("s", count($params));
            mysqli_stmt_bind_param($stmt, $param_types, ...$params);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update attraction: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

            $_SESSION['success_message'] = "Attraction updated successfully";
        } else {
            $_SESSION['success_message'] = "No changes were made to the attraction";
        }

        header("Location: ../../admin_manage_attraction.php");
        exit();

    } catch (Exception $e) {
        error_log("Error in attraction update: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../../admin_manage_attraction.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: ../../admin_manage_attraction.php");
    exit();
}
?>