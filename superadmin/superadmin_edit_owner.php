<?php 
include 'superadmin_sidenav.php';


$admin_id = $_SESSION['admin_id']; 
$sql = "SELECT * FROM adminlist WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<section class="home-section2">
    <div class="home-content">
        <div class="dashboard-container">
            <h4>Edit Profile</h4>
            <form id="profileForm" action="superadmin_edit_profile_process.php" method="POST" enctype="multipart/form-data">
                <div class="upload-container">
                    <label class="upload-icon" for="imageUpload" <?php if(!empty($admin_data['profile_image'])) echo 'style="display: none;"'; ?>>
                        <i class='bx bx-image-add'></i>
                    </label>
                    <input type="file" id="imageUpload" name="imageUpload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                    <div id="imagePreview" class="image-preview" <?php if(!empty($admin_data['profile_image'])) echo 'style="display: flex;"'; else echo 'style="display: none;"'; ?>>
                        <img id="uploadedImage" src="<?php echo !empty($admin_data['profile_image']) ? 'uploads/' . $admin_data['profile_image'] : ''; ?>" alt="Profile Picture" />
                        <button type="button" class="remove-btn" onclick="removeImage()">Remove</button>
                    </div>
                </div>
                <span class="add-note">
                    *For profile picture display
                </span>   

                <!-- Hidden input for admin_id -->
                <input type="hidden" name="admin_id" value="<?php echo $admin_data['admin_id']; ?>">

                <div class="input-field">
                    <input type="text" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($admin_data['admin_name']); ?>" required>
                    <label for="admin_name">Name</label>
                </div>
                
                <div class="input-field">
                    <input type="password" id="admin_password" name="admin_password" value="<?php echo htmlspecialchars($admin_data['admin_password']); ?>" required>
                    <label for="admin_password">Password</label>
                </div>

                <div class="input-field">
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_data['admin_email']); ?>" required>
                    <label for="admin_email">Email</label>
                </div>

                <div class="input-field">
                    <input type="tel" id="admin_phone_number" name="admin_phone_number" value="<?php echo htmlspecialchars($admin_data['admin_phone_number']); ?>" required>
                    <label for="admin_phone_number">Phone Number</label>
                </div>
                

                <button type="submit" class="icon-submit-button">
                    <i class='bx bxs-check-circle'></i>
                </button>
            </form>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            const previewContainer = document.getElementById('imagePreview');
            const uploadedImage = document.getElementById('uploadedImage');
            const uploadIcon = document.querySelector('.upload-icon');

            uploadedImage.src = reader.result;
            previewContainer.style.display = 'flex';
            uploadIcon.style.display = 'none';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        document.getElementById('imageUpload').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('uploadedImage').src = '';
        document.querySelector('.upload-icon').style.display = 'flex';
        
        const form = document.getElementById('profileForm');
        let removeImageInput = document.getElementById('remove_image');
        if (!removeImageInput) {
            removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.id = 'remove_image';
            removeImageInput.name = 'remove_image';
            form.appendChild(removeImageInput);
        }
        removeImageInput.value = '1';
    }
</script>
</body>
</html>