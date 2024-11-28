<?php
include 'admin_nav.php'; 

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Fetch the values from the form
        $attraction_id = $_POST['attraction_id'] ?? null;
        if (!$attraction_id) {
            throw new Exception("Attraction ID is required");
        }

        // Sanitize and trim the form data
        $attraction_name = trim($_POST['attraction_name'] ?? '');
        $attraction_desc = trim($_POST['attraction_description'] ?? '');
        $attraction_start_hours = $_POST['attraction_start_hours'] ?? '';
        $attraction_closing_hours = $_POST['attraction_closing_hours'] ?? '';
        $attraction_status = $_POST['attraction_status'] ?? '';

        // Check for empty fields
        if (empty($attraction_name) || empty($attraction_desc) || empty($attraction_start_hours) || empty($attraction_closing_hours)) {
            throw new Exception("Please fill in all required fields");
        }

        // Prepare the SQL query
        $sql = "UPDATE attraction SET 
                attraction_name = ?, 
                attraction_description = ?, 
                attraction_opening_hours = ?,
                attraction_closing_hours = ?,  
                attraction_status = ? 
                WHERE attraction_id = ?";

        // Prepare the values for binding
        $params = [
            $attraction_name,
            $attraction_desc,
            $attraction_start_hours,
            $attraction_closing_hours,
            $attraction_status,
            $attraction_id
        ];

        // If there's media, update the media as well
        if (isset($event_media)) {
            $sql .= ", event_media = ?";
            $params[] = $event_media;
        }

        // Prepare the statement and bind parameters
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare the SQL statement");
        }

        // Bind parameters dynamically based on the number of parameters
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
        mysqli_stmt_execute($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);

        // Set a success message and redirect
        $_SESSION['success'] = "Attraction updated successfully";
        header("Location: admin_manage_attraction.php");
        exit();

    } catch (Exception $e) {
        // In case of an error, set the error message in the session and redirect
        $_SESSION['error'] = $e->getMessage();
        header("Location: admin_manage_attraction.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: admin_manage_attraction.php");
    exit();
}
?>
