<?php
include 'admin_nav.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch the event data
    $sql = "SELECT * FROM eventlist WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event_data = $result->fetch_assoc();

    // Check if event data was found
    if (!$event_data) {
        header("Location: admin_manage_events.php");
        exit();
    }

} else {
    header("Location: admin_manage_events.php");
    exit();
}

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Information - <?php echo htmlspecialchars($attraction['attraction_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="src/css/admin_section.css">
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

    <!-- Tabs -->
   <div class="row">
    <div class="col s12">
        <ul class="tabs">
            <li class="tab col s3"><a class="active" href="#event_info">Event Info</a></li>
            <li class="tab col s3"><a href="media_section">Add Picture</a></li>
            <li class="tab col s3"><a href="#gallery_section">Gallery</a></li>
        </ul>
    </div>
</div>

    <!-- Tab Content Containers -->
    <div id="event_info" class="col s12">
        <?php include 'src/forms/event_info_form.php'; ?>
    </div>

    <div id="media_section" class="col s12">
        <?php include 'src/forms/event_media_form.php'; ?>
    </div>

    <div id="gallery_section" class="col s12">
        <?php include 'event_gallery.php'; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Initialize tabs
    var tabsElem = document.querySelector('.tabs');
    console.log('Tabs element:', tabsElem);
    
    if (tabsElem) {
        var instance = M.Tabs.init(tabsElem);
        console.log('Tabs instance:', instance);
    } else {
        console.error('Tabs element not found');
    }

    // Initialize textareas
    var textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        M.textareaAutoResize(textarea);
    });
});
</script>
</body>
</html>