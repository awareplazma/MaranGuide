<?php
include 'admin_nav.php';

$sql = "SELECT * FROM eventlist ORDER BY event_created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin Dashboard</title>
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="home-content">
        <div class="dashboard-container">
            <!-- Status Messages -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error-message">
                    <i class="material-icons">error</i>
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message success-message">
                    <i class="material-icons">check_circle</i>
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <div class="form-container">
                <div class="search-wrapper-mobile">
                    <div class="search-input-container">
                        <input type="text" id="searchInput" placeholder="Cari Acara...">
                    </div>
                    <a href="admin_add_events.php" class="add-button waves-effect waves-light">
                        Tambah Acara
                        <i class="material-icons">add</i>
                    </a>
                </div>

            <!-- Event List -->
                <div class="event-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_class = ($row['event_status'] === 'Active') ? 'active-badge' : 'inactive-badge';
                            ?>
                            <div class="event-card">
                                <div class="event-image-container">
                                <img src="<?php echo !empty($row['event_media']) ? $row['event_media'] : 'placeholder.jpg'; ?>" 
                                    alt="<?php echo htmlspecialchars($row['event_name']); ?>">
                                </div>
                                
                                <div class="event-info">
                                    <h2 class="event-title" ><?php echo htmlspecialchars($row['event_name']); ?></h2>
                                    
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['event_status']); ?>
                                    </span>
                                    
                                    <p class="event-description">
                                        <?php echo htmlspecialchars($row['event_description']); ?>
                                    </p>
                                                                
                                    <p class="event-meta">
                                        <i class="material-icons" style="vertical-align: -7px;">access_time</i></i> <?php echo date('d/m/Y H:i', strtotime($row['event_start_date'])); ?> 
                                        <i class='bx bx-time'></i>-<?php echo date('d/m/Y H:i', strtotime($row['event_end_date'])); ?> 
                                    </p>
                                </div>

                                <div class="action-buttons">
                                    <a href="admin_edit_event.php?event_id=<?php echo $row['event_id']; ?>" class="edit-btn">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <button onclick="deleteEvent(<?php echo $row['event_id']; ?>)" class="delete-btn">
                                        <i class="material-icons">delete</i>  
                                    </button>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p style='text-align: center; padding: 20px;'>Tidak ada acara yang ditemukan</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>

// Delete confirmation
function deleteEvent(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        window.location.href = `admin_delete_event_process.php?id=${id}`;
    }
}
</script>

</body>
</html>
