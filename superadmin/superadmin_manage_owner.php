<?php 

include 'superadmin_sidenav.php';
$sql = "SELECT a.*, b.attraction_name
FROM adminlist a
LEFT JOIN attraction b ON a.admin_id = b.admin_id
WHERE a.admin_role = 'owner'
ORDER BY admin_id DESC";

$result = $conn->query($sql);

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
<style>

</style>
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
            <div class="form-title">Senarai Pemilik</div>
            <div class="search-wrapper-mobile">
                <div class="search-input-container">
                    <input type="text" id="searchInput" placeholder="Cari Pemilik...">
                </div>
                <a href="superadmin_add_owner.php" class="add-button waves-effect waves-light">
                    <span>Tambah Pemilik</span>
                    <i class="material-icons">add</i>
                </a>
            </div>

            <!-- Attraction List -->
            <div class="attraction-list">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {

                    // Handle thumbnail path
                       $profile_picture = '/MARANGUIDE/media/default_image.png'; // Default image path
                        if (!empty($row['admin_profile_picture'])) {
                            $profile_picture_path = $_SERVER['DOCUMENT_ROOT'] . '/MARANGUIDE' . $row['admin_profile_picture'];
                            if (file_exists($profile_picture_path)) {
                                $profile_picture = '/MARANGUIDE' . $row['admin_profile_picture']; // Use relative path
                            }
                        }
                        
                        ?>
                        <div class="attraction-card">
                        <div class="attraction-card">
                            <div class="attraction-image-container">
                                <img src="<?php echo htmlspecialchars($profile_picture); ?>" 
                                alt="<?php echo htmlspecialchars($row['admin_name']); ?>" 
                                class="attraction-image"
                                onerror="this.src='../placeholder.jpg'">
                            </div>
                        </div>

                        <div class="attraction-info">
                            <h2 class="attraction-title">
                                <span class="admin-id"><?php echo htmlspecialchars($row['admin_id']); ?></span>
                                <span class="admin-name"><?php echo htmlspecialchars($row['admin_name']); ?></span>
                            </h2>
                            
                            <span class="status-badge">
                                <?php echo htmlspecialchars($row['admin_role']); ?>
                            </span>
                            
                            <p class="attraction-description">
                                <i class='bx bx-envelope'></i>
                                <?php echo htmlspecialchars($row['admin_email']); ?>
                            </p>
                            
                            <p class="owner-meta">
                                <i class='bx bx-phone'></i>
                                <?php echo htmlspecialchars($row['admin_phone_number']); ?>
                            </p>

                            <?php if (!empty($row['attraction_id'])): ?>
                            <p class="admin-meta">
                                <i class='bx bx-map-pin'></i>
                                Attraction ID: <?php echo htmlspecialchars($row['attraction_id']); ?>
                            </p>
                            <?php endif; ?>
                        </div>

                            <div class="action-buttons">
                                <a href="superadmin_edit_owner.php?owner_id=<?php echo $row['admin_id']; ?>" class="edit-btn">
                                    <i class='bx bx-edit-alt'></i>
                                </a>
                                <button onclick="deleteAdmin(<?php echo $row['admin_id']; ?>)" class="delete-btn">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='text-align: center; padding: 20px;'>Tidak ada pengurus yang ditemukan</p>";
                }
                ?>
            </div>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
// Search
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const adminCards = document.querySelectorAll('.admin-card');
    
    adminCards.forEach(card => {
        const name = card.querySelector('.admin-name').textContent.toLowerCase();
        const email = card.querySelector('.admin-meta').textContent.toLowerCase();
        
        if (name.includes(searchValue) || email.includes(searchValue)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Delete confirmation
function deleteAdmin(id) {
    if (confirm('Are you sure you want to delete this admin?')) {
        window.location.href = `/MARANGUIDE/superadmin/src/process/superadmin_delete_owner_process.php?id=${id}`;
    }
}
</script>

</body>
</html>
