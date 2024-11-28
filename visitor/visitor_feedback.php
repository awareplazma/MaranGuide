<?php
// Database connection
include 'maranguide_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $created_at = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO feedback (feedback_content, feedback_created_at, read_status) 
            VALUES ('$content', '$created_at', 0)";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>M.toast({html: 'Feedback submitted successfully!'});</script>";
    } else {
        echo "<script>M.toast({html: 'Error: " . $conn->error . "'});</script>";
    }
}
?>