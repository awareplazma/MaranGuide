<?php
include 'superadmin_sidenav.php';
ob_start(); 

if (isset($_SESSION['error_message'])) {
    echo "<div style='color:red;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo "<div style='color:green;'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lokasi Baru</title>
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
        <h1 class="form-title">Tambah Pemilik Baru</h1>
        <form id="locationForm" class="form-card" action="superadmin_add_owner_process.php" method="POST" enctype="multipart/form-data">
            <div class="upload-container">
                <label class="upload-icon" for="profilepictureUpload">
                    <i class="material-icons">add_photo_alternate</i>
                </label>
                <input type="file" id="profilepictureUpload" name="profilepictureUpload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <div id="imagePreview" class="image-preview">
                    <img id="uploadedImage" src="" alt="Image Preview" />
                    <button type="button" class="remove-btn" onclick="removeImage()">
                        <i class="material-icons">delete</i>
                        Remove
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="attraction_name">Nama Penuh Pemilik</label>
                <input type="text" id="owner_name" name="owner_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="owner_password">Kata Laluan</label>
                <input type="owner_password" name="owner_password" class="form-input" required></in>
            </div>

            <div class="form-group">
                <label class="form-label" for="owner_email">Emel Pemilik</label>
                <input type="text" id="owner_email" name="owner_email" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="owner_phone_number">Nombor Telefon Pemilik</label>
                <input type="text" id="owner_phone_number" name="owner_phone_number" class="form-input" required>
            </div>
            
            <button type="submit" class="submit-button">
                <i class="material-icons">check_circle</i>
                Submit
            </button>
        </form>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
       function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function () {
        const previewContainer = document.getElementById('imagePreview');
        const uploadedImage = document.getElementById('uploadedImage');
        const uploadIcon = document.querySelector('.upload-icon');

        uploadedImage.src = reader.result;
        previewContainer.style.display = 'flex'; // Show preview box
        uploadIcon.style.display = 'none'; // Hide upload icon
    };

    if (file) {
        reader.readAsDataURL(file);
    }
    }

    function removeImage() {
    document.getElementById('imageUpload').value = ''; // Clear the file input
    document.getElementById('imagePreview').style.display = 'none'; // Hide preview
    document.getElementById('uploadedImage').src = ''; // Clear the image preview
    document.querySelector('.upload-icon').style.display = 'flex'; // Show upload icon
    }
</script>
</body>
</html>