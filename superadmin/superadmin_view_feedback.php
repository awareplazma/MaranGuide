<?php 

include_once 'superadmin_sidenav.php';

$sql = "SELECT 
            feedback_id, 
            title, 
            feedback_content, 
            feedback_created_at, 
            read_status 
        FROM feedback 
        ORDER BY feedback_created_at DESC";


$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin - Feedback Management</title>
    
  
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="dashboard-container">
    <!-- Error and Success Message Handling -->
    <?php 
    
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
                    
                    $status_class = ($row['read_status'] === 'unread') ? 'active-badge' : 'inactive-badge';
                    
                    // Format and sanitize output
                    $title = htmlspecialchars($row['title']);
                    $description = htmlspecialchars($row['feedback_content']);
                    $description = (strlen($description) > 150) ? substr($description, 0, 147) . '...' : $description;
                    $time = !empty($row['feedback_created_at']) 
                        ? date("H:i", strtotime($row['feedback_created_at'])) 
                        : "N/A";
                    ?>
                    
                    <div class="attraction-card">
                        <div class="attraction-info">
                            <div class="attraction-header">
                                <div class="attraction-title">
                                    <?php echo $title; ?>
                                </div>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($row['read_status']); ?>
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
                            <button onclick="deleteFeedback(<?php echo htmlspecialchars($row['feedback_id']); ?>)" 
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
            window.location.href = `/MARANGUIDE/superadmin/src/process/superadmin_delete_feedback_process.php?id=${id}`;
        }
    }
</script>
</body>
</html>