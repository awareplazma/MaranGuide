<?php 
include 'admin_nav.php';

$admin_id = $_SESSION['admin_id'];

// Owner details
$owner_query = "SELECT * FROM adminlist WHERE admin_id = ?";
$stmt = $conn->prepare($owner_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$owner = $stmt->get_result()->fetch_assoc();

// Attraction details
$attraction_query = "SELECT * FROM attraction WHERE admin_id = ?";
$stmt = $conn->prepare($attraction_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$attraction = $stmt->get_result()->fetch_assoc();

// New Comment
$reviews_query = "SELECT * FROM comments WHERE attraction_id = ? AND approval_status='Belum Dibaca' ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($reviews_query);
$stmt->bind_param("i", $attraction['attraction_id']);
$stmt->execute();
$reviews = $stmt->get_result();

// Ratings
$rating_query = "SELECT AVG(rating) AS average_rating FROM comments WHERE attraction_id = ?";
$stmt = $conn->prepare($rating_query);
$stmt->bind_param("i", $attraction['attraction_id']);
$stmt->execute();
$result = $stmt->get_result();
$average_rating = $result->fetch_assoc()['average_rating']; // Fetch the average rating
$stmt->close();

// Optional: Scale the average rating to a percentage (e.g., 4/5 → 80%)
$scaled_rating = $average_rating !== null ? ($average_rating / 5) : 0;

// Events
$event_query = "SELECT * FROM eventlist WHERE attraction_id = ? AND event_start_date >= CURDATE() ORDER BY event_start_date LIMIT 3";
$stmt = $conn->prepare($event_query);
$stmt->bind_param("i", $attraction['attraction_id']);
$stmt->execute();
$events = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - <?php echo htmlspecialchars($attraction['attraction_name']); ?></title>
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
</head>
<body>

<div class="dashboard-container">
    <!-- Header Section -->
    <div class="page-header">
        <h5 class="grey-text text-darken-2">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h5>
        <h6 class="orange-text"><?php echo htmlspecialchars($attraction['attraction_name']); ?></h6>
    </div>

    <!-- Stats Section -->
    <div class="row">
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">visibility</i>
                <span class="stats-number"><?php echo $reviews->num_rows; ?></span>
                <span>New Comments</span>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">star</i>
                <span class="stats-number"> <?php 
            echo $average_rating !== 0 
                ? number_format($average_rating, 1) . " / 5" 
                : "No ratings yet"; 
            ?></span>
                <span>Average Rating</span>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="stats-card">
                <i class="material-icons medium">event</i>
                <span class="stats-number"><?php echo $events->num_rows; ?></span>
                <span>Acara</span>
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
                    <a href="admin_manage_attraction.php" class="quick-action-button waves-effect">
                        <i class="material-icons">edit</i>
                        <span>Edit Details</span>
                    </a>
                </div>
                <div class="col s6 m3">
                    <a href="admin_manage_events.php" class="quick-action-button waves-effect">
                        <i class="material-icons">event_note</i>
                        <span>Add Event</span>
                    </a>
                </div>
                <div class="col s6 m3">
                    <a href="view_feedback.php?id=<?php echo $attraction['attraction_id']; ?>" class="quick-action-button waves-effect">
                        <i class="material-icons">feedback</i>
                        <span>View Reviews</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>