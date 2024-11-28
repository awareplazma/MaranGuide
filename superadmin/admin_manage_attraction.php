<?php
include 'admin_nav.php';

if (isset($_SESSION['attraction_id'])) {
    $attraction_id = $_SESSION['attraction_id']; // Retrieve attraction_id from the session

    // Fetch the attraction data
    $sql = "SELECT * FROM attraction WHERE attraction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $attraction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attraction_data = $result->fetch_assoc();

    // Check if attraction data was found
    if (!$attraction_data) {
        echo "No data found for attraction_id: " . htmlspecialchars($attraction_id);
        exit();
    }
} else {
    echo "No attraction_id in session.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat Tempat <?php echo htmlspecialchars($attraction['attraction_name']); ?></title>
    <link rel="stylesheet" href="../css/project.css">
    <link rel="stylesheet" href="../css/admin_section.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
</head>
<body>

<div class="attraction-form-container">
    <!-- Tabs -->
    <ul class="tabs">
        <li class="tab col s3"><a class="active" href="#attraction_info">Info</a></li>
        <li class="tab col s3"><a href="#media_section">Media</a></li>
        <li class="tab col s3"><a href="#gallery_section">Galeri</a></li>
    </ul>

    <form id="eventForm" action="admin_manage_attraction_process.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($_data['attraction_id']); ?>">

        <!-- Event Info Tab -->
        <div id="attraction_info" class="col s12">
            <!-- Basic Information Section -->
            <div class="event-form-section">
                <div class="section-title">
                    <i class="material-icons left">info</i>
                    Basic Information
                </div>
                
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">business</i>
                        <textarea id="event_name" name="event_name" class="materialize-textarea" required><?php echo htmlspecialchars($attraction_data['attraction_name']); ?></textarea>
                        <label for="event_name">Nama Tempat</label>
                    </div>

                    <div class="input-field col s12">
                        <i class="material-icons prefix">description</i>
                        <textarea id="attraction_description" name="attraction_description" class="materialize-textarea" required><?php echo htmlspecialchars($attraction_data['attraction_description']); ?></textarea>
                        <label for="attraction_description">Butiran Ringkas Tempat</label>
                    </div>
                </div>
              
                
                <div class="row">
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Waktu Buka</label>
                            <input type="time" name="attraction_closing_hours" class="validate" value="<?php echo date('H:i', strtotime($attraction_data['attraction_opening_hours'])); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Waktu Tutup</label>
                            <input type="time" name="attraction_closing_hours" class="validate" value="<?php echo date('H:i', strtotime($attraction_data['attraction_closing_hours'])); ?>" required>
                        </div>
                    </div>
                </div>
            <!-- Status Section -->
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <select name="event_status" required>
                                <option value="" disabled>Choose event status</option>
                                <option value="active">Aktif</option>
                                <option value="completed">Selesai</option>
                            </select>
                            <label>Event Status</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($attraction['attraction_id']); ?>">
            <input type="hidden" name="attraction_created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
        </div>

        <!-- Media Tab -->
        <div id="media_section" class="col s12">
            <div class="event-form-section">
                <div class="section-title">
                    <i class="material-icons left">image</i>
                    Event Media
                </div>
                
                <div class="row">
                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>File</span>
                                <input type="file" id="mediaInput" name="attraction_media[]" accept="image/*,video/*" multiple>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Upload event images or videos">
                            </div>
                        </div>
                        
                        <div class="media-preview center-align">
                            <div id="preview-container">
                                <p class="grey-text preview-text">Media preview will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gallery -->

        <div id="gallery_section" class="col s12">
            <div class="event-form-section">
                <div class="section-title">
                    <i class="material-icons left">photo_library</i>
                    Media Gallery
                </div>
                <div class="gallery-container">
                    <div class="row">
                        <?php
                        $media_paths = preg_split('/[,;]/', $attraction_data['attraction_media']);
                        foreach($media_paths as $media_path):
                            if(!empty(trim($media_path))):
                                $file_extension = strtolower(pathinfo(trim($media_path), PATHINFO_EXTENSION));
                                $is_video = in_array($file_extension, ['mp4', 'webm', 'ogg']);
                        ?>
                            <div class="col s12 m6 l4">
                                <div class="card media-card">
                                    <div class="card-image">
                                        <?php if($is_video): ?>
                                            <video class="responsive-video" controls>
                                                <source src="<?php echo htmlspecialchars(trim($media_path)); ?>" type="video/<?php echo $file_extension; ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <img src="<?php echo htmlspecialchars(trim($media_path)); ?>" alt="Attraction Media">
                                        <?php endif; ?>
                                        <span class="delete-media btn-floating btn-small waves-effect waves-light red" data-media="<?php echo htmlspecialchars(trim($media_path)); ?>">
                                            <i class="material-icons">delete</i>
                                        </span>
                                    </div>
                                    <div class="card-content">
                                        <span class="card-title truncate"><?php echo basename(trim($media_path)); ?></span>
                                        <div class="chip"><?php echo $is_video ? 'Video' : 'Image'; ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Submit Buttons -->
        <div class="row center-align">
            <button class="btn-large waves-effect waves-light" type="submit" name="action">
                Save Event
                <i class="material-icons right">save</i>
            </button>
            
            <a href="events_list.php" class="btn-large waves-effect waves-light red">
                Cancel
                <i class="material-icons right">cancel</i>
            </a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize Materialize components
    M.Tabs.init(document.querySelectorAll('.tabs'));
    M.FormSelect.init(document.querySelectorAll('select'));
    M.TextareaAutoResize(document.querySelector('.materialize-textarea'));
    
    // Media preview functionality
    const mediaInput = document.getElementById('mediaInput');
    const previewContainer = document.getElementById('preview-container');
    const previewText = document.querySelector('.preview-text');

    mediaInput.addEventListener('change', function(e) {
        previewContainer.innerHTML = ''; // Clear previous previews
        previewText.style.display = 'none';

        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewElement = document.createElement('div');
                previewElement.className = 'preview-item';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'responsive-img';
                    img.style.maxHeight = '200px';
                    img.style.margin = '10px';
                    previewElement.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.controls = true;
                    video.className = 'responsive-video';
                    video.style.maxHeight = '200px';
                    video.style.margin = '10px';
                    previewElement.appendChild(video);
                }
                
                previewContainer.appendChild(previewElement);
            };
            
            reader.readAsDataURL(file);
        });
    });

    // Delete media functionality
    document.querySelectorAll('.delete-media').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const mediaCard = this.closest('.col');
            const mediaPath = this.dataset.media;
            
            if (confirm('Are you sure you want to delete this media?')) {
                fetch('delete_media.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        attraction_id: <?php echo $attraction_id; ?>,
                        media_path: mediaPath
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mediaCard.remove();
                        M.toast({html: 'Media deleted successfully!'});
                    } else {
                        M.toast({html: 'Error deleting media'});
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    M.toast({html: 'Error deleting media'});
                });
            }
        });
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