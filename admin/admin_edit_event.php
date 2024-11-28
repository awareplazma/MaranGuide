<?php
include '../../../maranguide_connection.php';

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
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
</head>
<body>

<div class="event-form-container">
    <!-- Tabs -->
    <ul class="tabs">
        <li class="tab col s3"><a class="active" href="#event_info">Event Info</a></li>
        <li class="tab col s3"><a href="#media_section">Add Picture</a></li>
        <li class="tab col s3"><a href="#gallery_section">Gallery</a></li>
    </ul>

    <!-- Tab Content Containers -->
    <div id="event_info" class="col s12">
        <?php include 'src/forms/event_info_form.php'; ?>
    </div>

    <div id="media_section" class="col s12">
        <?php include 'src/forms/event_media_form.php'; ?>
    </div>

    <div id="gallery_section" class="col s12">
        <?php include 'src/forms/event_gallery.php'; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Materialize components
    M.Tabs.init(document.querySelectorAll('.tabs'));
    M.FormSelect.init(document.querySelectorAll('select'));
    M.TextareaAutoResize(document.querySelector('.materialize-textarea'));
    
    // Media preview functionality
    const mediaInput = document.getElementById('mediaInput');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const previewText = document.querySelector('.preview-text');

    mediaInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    videoPreview.style.display = 'none';
                    previewText.style.display = 'none';
                } else if (file.type.startsWith('video/')) {
                    videoPreview.src = e.target.result;
                    videoPreview.style.display = 'block';
                    imagePreview.style.display = 'none';
                    previewText.style.display = 'none';
                }
            };
            
            reader.readAsDataURL(file);
        }
    });

    // Delete media functionality
    document.querySelectorAll('.delete-media').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const mediaPath = this.dataset.media;
            if (confirm('Are you sure you want to delete this media?')) {
                // Add AJAX call to delete media
                fetch('delete_media.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        event_id: <?php echo $event_id; ?>,
                        media_path: mediaPath
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.col').remove();
                        M.toast({html: 'Media deleted successfully!'});
                    } else {
                        M.toast({html: 'Error deleting media'});
                    }
                });
            }
        });
    });

    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const startDate = new Date(document.querySelector('input[name="event_start_date"]').value);
        const endDate = new Date(document.querySelector('input[name="event_end_date"]').value);
        
        if (endDate < startDate) {
            e.preventDefault();
            M.toast({html: 'End date cannot be before start date!'});
        }
    });
});
</script>
</body>
</html>