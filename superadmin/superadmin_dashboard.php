<?php 
include 'superadmin_sidenav.php';

$admin_id = $_SESSION['admin_id'];

$admin_id = $_SESSION['admin_id'];

// Owner numbers
$owner_query = "SELECT COUNT(*) AS total_owners FROM adminlist";
$stmt = $conn->prepare($owner_query);
$stmt->execute();
$owner_result = $stmt->get_result()->fetch_assoc();
$total_owners = $owner_result['total_owners'];
$stmt->close();

// Attraction numbers
$attraction_query = "SELECT COUNT(*) AS total_attractions FROM attraction";
$stmt = $conn->prepare($attraction_query);
$stmt->execute();
$attraction_result = $stmt->get_result()->fetch_assoc();
$total_attractions = $attraction_result['total_attractions'];
$stmt->close();

// Feedback numbers
$feedback_query = "SELECT COUNT(*) AS total_unread_feedback FROM feedback WHERE read_status = 'unread'";
$stmt = $conn->prepare($feedback_query);
$stmt->execute();
$feedback_result = $stmt->get_result()->fetch_assoc();
$total_unread_feedback = $feedback_result['total_unread_feedback'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaranGuide Superadmin Dashboard</title>
 
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
</head>
<body>
    <!-- Main Content -->
    <div class="dashboard-container">
    <!-- Header Section -->
    <div class="page-header">
        <h5 class="grey-text text-darken-2">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h5>
    </div>

    <!-- Stats Section -->
    <div class="row">
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">visibility</i>
                <span class="stats-number"><?php echo $total_attractions; ?></span>
                <span>Jumlah Tempat Tarikan</span>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">star</i>
                <span class="stats-number"><?php echo $total_owners; ?></span>
                <span>Jumlah Pemilik</span>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">event</i>
                <span class="stats-number"><?php echo $total_unread_feedback; ?></span>
                <span>Maklum Balas Belum Dibaca</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="content-card">
        <div class="card-header">
            <h6 class="margin-0">Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col s6 m3">
                    <a href="admin_edit_attraction.php" class="quick-action-button waves-effect">
                        <i class="material-icons">edit</i>
                        <span>Tambah Tempat Tarikan</span>
                    </a>
                </div>
                <div class="col s6 m3">
                    <a href="superadmin_add_owner.php" class="quick-action-button waves-effect">
                        <i class="material-icons">event_note</i>
                        <span>Tambah Pemilik</span>
                    </a>
                </div>
                <div class="col s6 m3">
                    <a href="view_feedback.php" class="quick-action-button waves-effect">
                        <i class="material-icons">feedback</i>
                        <span>Lihat Maklum Balas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            var sidenav = document.querySelectorAll('.sidenav');
            M.Sidenav.init(sidenav);
        });

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>