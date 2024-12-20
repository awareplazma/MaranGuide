<?php 
// Include side navigation - ensure this file exists and is properly secured
include_once 'admin_nav.php';

$attraction_id = $_SESSION['attraction_id'];

// Validate and retrieve attraction_id
if (!isset($_SESSION['attraction_id']) || !filter_var($_SESSION['attraction_id'], FILTER_VALIDATE_INT)) {
    die('Invalid or missing attraction ID');
}



// Improve security: Use prepared statement for database query
$sql = "SELECT 
            user,
            content,
            rating,
            created_at, 
            approval_status 
        FROM comments
        WHERE attraction_id = ? 
        ORDER BY created_at DESC";

// COMMENT: Switched to prepared statement for better security
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Failed to prepare statement: ' . $conn->error);
}

$stmt->bind_param("i", $attraction_id);
if (!$stmt->execute()) {
    die('Query execution failed: ' . $stmt->error);
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin - Feedback Management</title>
    
    <!-- COMMENT: Consolidated and updated CSS links -->
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="home-content">
    <!-- Error and Success Message Handling -->
    <?php 
    // COMMENT: Consolidated message display with improved security
    $message_types = [
        'error' => ['class' => 'error-message', 'icon' => 'bxs-error'],
        'success' => ['class' => 'success-message', 'icon' => 'bxs-check-square']
    ];

    foreach ($message_types as $type => $config) {
        $session_key = "_{$type}_message";
        if (isset($_SESSION[$session_key])) {
            echo "<div class='message {$config['class']}'>
                    <i class='bx {$config['icon']}'></i>
                    " . htmlspecialchars($_SESSION[$session_key]) . "
                  </div>";
            unset($_SESSION[$session_key]);
        }
    }
    ?>

    <!-- Feedback Management Container -->
    <div class="form-container">
        <div class="form-title">Maklum Balas (Feedback)</div>

        <!-- Feedback List -->
        <div class="attraction-list">
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    //Baiki == 0 automate dr visitor
                    $status_class = ($row['approval_status'] === '0') ? 'active-badge' : 'inactive-badge';
                    
                    // Format and sanitize output
                    $title = htmlspecialchars($row['user']);
                    $description = htmlspecialchars($row['content']);
                    $description = (strlen($description) > 150) ? substr($description, 0, 147) . '...' : $description;
                    $time = !empty($row['created_at']) 
                        ? date("H:i", strtotime($row['created_at'])) 
                        : "N/A";
                    ?>
                    
                    <div class="attraction-card">
                        <div class="attraction-info">
                            <div class="attraction-header">
                                <div class="attraction-title">
                                    <?php echo $title; ?>
                                </div>
                                <!-- Tukar status class CSS variable name -->
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($row['approval_status']); ?>
                                </span>
                            </div>
                            <p class="attraction-description">
                                <?php echo $description; ?>
                            </p>
                            
                            <div class="date-time-info">
                                <p class="date-time-field">
                                    <i class='bx bx-time'></i>
                                    <span><?php echo $time; ?></span>
                                </p>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button onclick="deleteFeedback(<?php echo htmlspecialchars($row['comment_id']); ?>)" 
                                    class="delete-btn waves-effect waves-light">
                                <i class="material-icons">delete</i>
                            </button>
                        </div>
                    </div>
                <?php 
                }
            } else {
                echo '<div class="no-attractions">
                        <p>No feedback found</p>
                      </div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- COMMENT: Updated script links and added error handling -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    // Feedback Search Function
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const attractionCards = document.querySelectorAll('.attraction-card');
            
            attractionCards.forEach(card => {
                const title = card.querySelector('.attraction-title').textContent.toLowerCase();
                const description = card.querySelector('.attraction-description').textContent.toLowerCase();
                
                card.style.display = (title.includes(searchValue) || description.includes(searchValue)) ? '' : 'none';
            });
        });
    }

    // Delete Confirmation Function
    function deleteFeedback(id) {
        if (confirm('Are you sure you want to delete this feedback?')) {
            // COMMENT: Updated to use a more secure deletion path
            window.location.href = `/superadmin/src/process/superadmin_delete_feedback_process.php?id=${id}`;
        }
    }
</script>
</body>
</html>