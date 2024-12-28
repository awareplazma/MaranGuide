<?php
include 'admin_nav.php';

if (isset($_SESSION['attraction_id'])) {
    $attraction_id = $_SESSION['attraction_id'];

    // Correct the SQL query to fetch attraction data
    $sql = "SELECT * FROM attraction WHERE attraction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $attraction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attraction_data = $result->fetch_assoc();

    // Check if attraction data was found
    if (!$attraction_data) {
        header("Location: admin_manage_attraction.php");
        exit();
    }

    if (!empty($attraction_data['attraction_thumbnails'])) {
        $attraction_data['attraction_thumbnails'] = '../' . ltrim($attraction_data['attraction_thumbnails'], '/');
    }

    // Fetch media data from attraction_media table
    $media_sql = "SELECT * FROM attraction_media WHERE attraction_id = ?";
    $media_stmt = $conn->prepare($media_sql);
    $media_stmt->bind_param("i", $attraction_id);
    $media_stmt->execute();
    $media_result = $media_stmt->get_result();
    $media_paths = [];

    while ($media = $media_result->fetch_assoc()) {
        $media_paths[] = $media['media_path']; 
    }

} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($attraction_data['attraction_name']); ?></title>
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<div class="event-form-container">
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
<!-- Main Content -->
    <ul class="tabs">
        <li class="tab col s3"><a class="active" href="#event_info">Event Info</a></li>
        <li class="tab col s3"><a href="#media_section">Add Picture</a></li>
        <li class="tab col s3"><a href="#gallery_section">Gallery</a></li>
    </ul>

    <div id="event_info" class="col s12">
        <?php include 'src/forms/attraction_info_form.php'; ?>
    </div>

    <div id="media_section" class="col s12">
        <?php include 'src/forms/attraction_media_form.php'; ?>
    </div>

    <div id="gallery_section" class="col s12">
        <?php include 'attraction_gallery.php'; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tabs = document.querySelectorAll('.tabs');
        M.Tabs.init(tabs);
        var selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
    });
    const tabs = M.Tabs.getInstance(document.querySelector('.tabs'));
    //Log in ChromeDevTool console in case tabs isn't working
    console.log(tabs); 
</script>
</body>
</html>