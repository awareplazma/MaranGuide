<?php 

include 'superadmin_sidenav.php';
$sql = "SELECT * FROM adminlist WHERE admin_role='owner' ORDER BY admin_id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin Dashboard</title>
    <link rel="stylesheet" href="../css/superadmin_section.css">
    <link rel="stylesheet" href="superadmin_sidenav.css">
    <link rel="stylesheet" href="../css/project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<style>
.attraction-list {
    display: grid;
    gap: 1.5rem;
    padding: 1.5rem;
}

.attraction-card {
    display: grid;
    grid-template-columns: 200px 1fr auto;
    gap: 1.5rem;
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
}

.attraction-image-container {
    width: 200px;
    height: 200px;
    overflow: hidden;
    border-radius: 8px;
}

.attraction-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.attraction-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.attraction-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.attraction-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.active-badge {
    background-color: #dcfce7;
    color: #166534;
}

.inactive-badge {
    background-color: #fee2e2;
    color: #991b1b;
}

.attraction-description {
    color: #64748b;
    line-height: 1.5;
}

.attraction-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.attraction-meta,
.date-time-field {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
}

.date-time-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

/* Updated Button Styles */
.search-wrapper-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 0 1.5rem;
}

.search-input-container {
    flex: 1;
}

.search-input-container input {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    width: 100%;
}

.add-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #2196F3;
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.add-button:hover {
    background-color: #1976D2;
    color: white;
}

.add-button i {
    font-size: 20px;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    align-self: flex-start;
    margin-top: 1rem;
}

.edit-btn,
.delete-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.edit-btn {
    background-color: #2196F3;
    color: white;
}

.edit-btn:hover {
    background-color: #1976D2;
}

.delete-btn {
    background-color: #F44336;
    color: white;
}

.delete-btn:hover {
    background-color: #D32F2F;
}

.edit-btn i,
.delete-btn i {
    font-size: 20px;
}

.no-attractions {
    text-align: center;
    padding: 2rem;
    color: #64748b;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .attraction-card {
        grid-template-columns: 1fr;
    }
    
    .attraction-image-container {
        width: 100%;
        height: 200px;
    }
    
    .search-wrapper-mobile {
        flex-direction: column;
    }
    
    .add-button {
        width: 100%;
        justify-content: center;
    }
    
    .action-buttons {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        margin-top: 0;
        background: rgba(255, 255, 255, 0.95);
        padding: 0.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .edit-btn,
    .delete-btn {
        padding: 0.5rem;
    }
    
    .edit-btn i,
    .delete-btn i {
        font-size: 18px;
    }
}
</style>
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
                <div class="search-wrapper-mobile">
                    <div class="search-input-container">
                        <input type="text" id="searchInput" placeholder="Cari Pemilika...">
                    </div>
                    <a href="superadmin_add_owner.php" class="add-button waves-effect waves-light">
                        Tambah Pemilik
                        <i class="material-icons">add</i>
                    </a>
            </div>

            <!-- Admin List -->
            <div class="admin-list">
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="admin-card">
                        <div class="attraction-image-container">
                            <img src="<?php echo !empty($row['admin_profile_picture']) ? $row['admin_profile_picture'] : 'placeholder.jpg'; ?>" 
                            alt="<?php echo htmlspecialchars($row['admin_name']); ?>" 
                            class="attraction-image">
                        </div>
                            <div class="admin-info">
                                <h2 class="admin-name"><?php echo htmlspecialchars($row['admin_name']); ?></h2>
                                
                                <span class="role-badge">
                                    <?php echo htmlspecialchars($row['admin_role']); ?>
                                </span>
                                
                                <p class="owner-meta">
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
                                <a href="superadmin_edit_owner.php?id=<?php echo $row['admin_id']; ?>" class="edit-btn">
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
</section>
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
        window.location.href = `/src/process/superadmin_delete_owner_process.php?id=${id}`;
    }
}
</script>

</body>
</html>
