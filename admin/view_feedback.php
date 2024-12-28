<?php 
include_once 'admin_nav.php';

$attraction_id = $_SESSION['attraction_id'];

// Validate and retrieve attraction_id
if (!isset($_SESSION['attraction_id']) || !filter_var($_SESSION['attraction_id'], FILTER_VALIDATE_INT)) {
    die('Invalid or missing attraction ID');
}

$sql = "SELECT 
            comment_id,
            user,
            content,
            rating,
            created_at, 
            approval_status 
        FROM comments
        WHERE attraction_id = ? AND (approval_status = 'Lulus' OR approval_status = 'Belum Dibaca' OR approval_status = '0')
        ORDER BY created_at DESC";

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
    
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <h1> Urus Komen </h1>
                <!--Filter and search functionality will add when have time -->

                <!-- <div class="search-wrapper-mobile">
                    <div class="search-input-container">
                        <input type="text" id="searchInput" placeholder="Cari Acara...">
                    </div>
                    <a href="admin_add_events.php" class="add-button waves-effect waves-light">
                        Tambah Acara
                        <i class="material-icons">add</i>
                    </a>
                </div> -->

            <!-- Comment List -->
                <!-- Change div later -->
                <div class="event-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_class = ($row['approval_status'] === '0') ? 'active-badge' : 'inactive-badge';
                            ?>
                            <div class="event-card">
                                
                                
                                <div class="event-info">
                                    <h2 class="event-title" ><?php echo htmlspecialchars($row['user']); ?></h2>
                                    
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['approval_status']); ?>
                                    </span>
                                    
                                    <p class="event-description">
                                        <?php echo htmlspecialchars($row['content']); ?>
                                    </p>
                                                                
                                    <p class="event-meta">
                                        <i class='bx bx-time'></i>-<?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?> 
                                    </p>
                                </div>

                                <div class="action-buttons">
                                    <a href="src/process/admin_approve_comment.php?comment_id=<?php echo $row['comment_id']; ?>" 
                                    class="edit-btn tooltipped"
                                    data-position="top"
                                    data-tooltip="Luluskan komen untuk paparan depan">
                                        <i class="material-icons">check_box</i>
                                    </a>
                                    <a href="src/process/admin_disapprove_comment.php?comment_id=<?php echo $row['comment_id']; ?>"
                                    class="delete-btn tooltipped"
                                    data-position="top"
                                    data-tooltip="Buang komen">
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
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    // Feedback Search Function
    // const searchInput = document.getElementById('searchInput');
    // if (searchInput) {
    //     searchInput.addEventListener('keyup', function() {
    //         const searchValue = this.value.toLowerCase();
    //         const attractionCards = document.querySelectorAll('.attraction-card');
            
    //         attractionCards.forEach(card => {
    //             const title = card.querySelector('.attraction-title').textContent.toLowerCase();
    //             const description = card.querySelector('.attraction-description').textContent.toLowerCase();
                
    //             card.style.display = (title.includes(searchValue) || description.includes(searchValue)) ? '' : 'none';
    //         });
    //     });
    // }

    //Hoover tool
    document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.tooltipped');
    var instances = M.Tooltip.init(elems, {
        enterDelay: 200,  // Delay before tooltip appears (in ms)
        exitDelay: 0,    // Delay before tooltip disappears (in ms)
        transitionMovement: 10   
    });
});

</script>
</body>
</html>