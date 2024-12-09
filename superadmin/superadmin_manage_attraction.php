<?php 
include 'superadmin_sidenav.php';

$sql = "SELECT attraction.*, adminlist.admin_name 
        FROM attraction
        LEFT JOIN adminlist 
        ON attraction.admin_id = adminlist.admin_id 
        ORDER BY attraction_created_at DESC";

$result = $conn->query($sql);

function getDayRanges($days) {
    $day_order = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    $indexes = array_map(function($day) use ($day_order) {
        return array_search($day, $day_order);
    }, $days);

    sort($indexes);
    $ranges = [];
    $start = $indexes[0];

    for ($i = 1; $i < count($indexes); $i++) {
        if ($indexes[$i] != $indexes[$i - 1] + 1) {
            $ranges[] = [$start, $indexes[$i - 1]];
            $start = $indexes[$i];
        }
    }

    $ranges[] = [$start, end($indexes)];

    $result = [];
    foreach ($ranges as [$startIdx, $endIdx]) {
        $result[] = $day_order[$startIdx] . ($startIdx != $endIdx ? " - " . $day_order[$endIdx] : "");
    }

    return implode(', ', $result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin Dashboard</title>
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
        <!-- Message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error-message">
                <i class='bx bxs-error'></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success-message">
                <i class='bx bxs-check-square'></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Add button -->
        <div class="form-container">
            <div class="form-title">Senarai Tempat Tarikan</div>
            <div class="search-wrapper-mobile">
                <div class="search-input-container">
                    <input type="text" id="searchInput" placeholder="Cari Tempat Tarikan...">
                </div>
                <a href="superadmin_add_attraction.php" class="add-button waves-effect waves-light">
                    <span>Tambah Tempat Tarikan</span>
                    <i class="material-icons">add</i>
                </a>
            </div>

            <!-- Attraction List -->
            <div class="attraction-list">
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status_class = ($row['attraction_status'] === 'aktif') ? 'active-badge' : 'inactive-badge';
                        
                        // Handle operating days
                        $operating_days = !empty($row['attraction_operating_days']) ? 
                            array_filter(array_map('trim', explode(',', $row['attraction_operating_days']))) : [];
                        $operating_days_display = !empty($operating_days) ? 
                            getDayRanges($operating_days) : "No operating days set";

                        // Handle thumbnail path
                        $thumbnail = '../placeholder.jpg';
                        if (!empty($row['attraction_thumbnails'])) {
                            $thumbnail_path = '../' . $row['attraction_thumbnails'];
                            if (file_exists($thumbnail_path)) {
                                $thumbnail = $thumbnail_path;
                            }
                        }

                        // Format times
                        $opening_time = !empty($row['attraction_opening_hours']) ? 
                            date("H:i", strtotime($row['attraction_opening_hours'])) : "N/A";
                        $closing_time = !empty($row['attraction_closing_hours']) ? 
                            date("H:i", strtotime($row['attraction_closing_hours'])) : "N/A";
                        ?>
                        
                        <div class="attraction-card">
                            <div class="attraction-image-container">
                                <img src="<?php echo htmlspecialchars($thumbnail); ?>" 
                                     alt="<?php echo htmlspecialchars($row['attraction_name']); ?>" 
                                     class="attraction-image"
                                     onerror="this.src='../placeholder.jpg'">
                            </div>
                            
                            <div class="attraction-info">
                                <div class="attraction-header">
                                    <div class="attraction-title">
                                        <?php echo htmlspecialchars($row['attraction_name']); ?>
                                    </div>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['attraction_status']); ?>
                                    </span>
                                </div>
                                <p class="attraction-description">
                                <?php 
                                    if (!isset($row['admin_name']) || empty($row['admin_name'])) {
                                        // If admin_name is not set or is empty, show a default message
                                        echo 'Pemilik belum ditetapkan';
                                    } else {
                                        // Otherwise, get the admin_name and sanitize it
                                        $description = htmlspecialchars($row['admin_name']);
                                        // Check if the description exceeds 150 characters
                                        echo (strlen($description) > 150) ? substr($description, 0, 147) . '...' : $description;
                                    }
                                ?>
                                </p>
                                <p class="attraction-description">
                                    <?php 
                                    $description = htmlspecialchars($row['attraction_description']);
                                    echo (strlen($description) > 150) ? 
                                        substr($description, 0, 147) . '...' : $description;
                                    ?>
                                </p>
                                
                                <div class="attraction-details">
                                    <p class="attraction-meta">
                                        <i class='bx bx-map'></i>
                                        <span><?php echo htmlspecialchars($row['attraction_address']); ?></span>
                                    </p>
                                    
                                    <div class="date-time-info">
                                        <?php if ($opening_time !== "N/A" && $closing_time !== "N/A"): ?>
                                        <p class="date-time-field">
                                            <i class='bx bx-time'></i>
                                            <span><?php echo $opening_time; ?> - <?php echo $closing_time; ?></span>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <p class="date-time-field">
                                            <i class='bx bx-calendar'></i>
                                            <span><?php echo htmlspecialchars($operating_days_display); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="superadmin_edit_attraction.php?attraction_id=<?php echo htmlspecialchars($row['attraction_id']); ?>" 
                                   class="edit-btn waves-effect waves-light">
                                    <i class="material-icons">edit</i>
                                </a>
                                <button onclick="deleteAttraction(<?php echo htmlspecialchars($row['attraction_id']); ?>)" 
                                        class="delete-btn waves-effect waves-light">
                                    <i class="material-icons">delete</i>
                                </button>
                            </div>
                        </div>
                    <?php 
                    }
                } else {
                    echo '<div class="no-attractions">
                            <p>No attractions found</p>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        // Attraction Search
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const attractionCards = document.querySelectorAll('.attraction-card');
            
            attractionCards.forEach(card => {
                const title = card.querySelector('.attraction-title').textContent.toLowerCase();
                const description = card.querySelector('.attraction-description').textContent.toLowerCase();
                
                if (title.includes(searchValue) || description.includes(searchValue)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Delete confirmation
        function deleteAttraction(id) {
            if (confirm('Are you sure you want to delete this attraction?')) {
                window.location.href = `src/process/superadmin_delete_attraction_process.php?id=${id}`;
            }
        }
    </script>
</body>
</html>