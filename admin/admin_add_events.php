<?php
include 'admin_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Acara Baru</title>
    <link rel="stylesheet" href="src/css/project.css">
    <link rel="stylesheet" href="src/css/admin_section.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
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
                <h4 class="form-title">Tambah Acara Baru</h4>
                <input type="hidden" name="attraction_id" value="<?php echo isset($_SESSION['attraction_id']) ? htmlspecialchars($_SESSION['attraction_id']) : ''; ?>">
                <form id="eventForm" action="src/process/admin_add_event_process.php" method="POST" enctype="multipart/form-data">
                    <!-- Image Upload Section -->
                    <div class="upload-section">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Pilih Gambar</span>
                                <input type="file" id="imageUpload" name="imageUpload" accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Muat naik gambar acara">
                            </div>
                        </div>
                        <div id="imagePreview" class="media-preview" style="display: none;">
                            <img id="uploadedImage" src="" alt="Image Preview" />
                            <button type="button" class="btn remove-btn" onclick="removeImage()">
                                <i class="material-icons">close</i>
                            </button>
                        </div>
                    </div>
                    <!-- Hidden Input -->
                    <input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($_SESSION['attraction_id'] ?? ''); ?>">

                    <!-- Event Details -->
                    <div class="input-field">
                        <input type="text" id="event_name" name="event_name" required>
                        <label for="event_name">Nama Acara</label>
                    </div>

                    <div class="input-field">
                        <textarea id="event_description" name="event_description" class="materialize-textarea" required></textarea>
                        <label for="event_description">Deskripsi Acara</label>
                    </div>

                    <!-- Start Date and Time -->
                    <div class="datetime-group">
                        <div class="datetime-field">
                            <label>Tarikh Mula</label>
                            <input type="datetime-local" id="event_start_date" name="event_start_date" required>
                        </div>                 
                        <div class="datetime-field">
                            <label>Tarikh Tamat</label>
                            <input type="datetime-local" id="event_end_date" name="event_end_date">
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="submit" class="submit-button">
                        <i class="material-icons">check_circle</i>
                        <span>Simpan Acara</span>
                    </button>
                </form>
            </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Materialize components
    M.updateTextFields();
    M.textareaAutoResize(document.querySelector('#event_description'));
    
    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const startDate = new Date(document.getElementById('event_start_date').value + ' ' + document.getElementById('event_start_time').value);
        const endDate = new Date(document.getElementById('event_end_date').value + ' ' + document.getElementById('event_end_time').value);
        
        if (endDate < startDate) {
            e.preventDefault();
            M.toast({html: 'Tarikh tamat tidak boleh lebih awal dari tarikh mula!', classes: 'red'});
        }
    });
});

function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        const image = document.getElementById('uploadedImage');
        preview.style.display = 'block';
        image.src = reader.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('imageUpload').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.querySelector('.file-path').value = '';
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>