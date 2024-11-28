<form id="AttractionMediaForm" action="src/process/edit_attraction_media.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($attraction_data['attraction_id']); ?>">
    <div id="media_section" class="col s12">
        <div class="event-form-section">
            <div class="section-title">
                <i class="material-icons left">file_upload</i>
                Muat Naik Media
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="attraction_media" accept="image/*,video/*">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Upload event image or video">
                        </div>
                    </div>
            <!--Media Preview-->

            <div class="media-preview center-align">
                <div class="image-preview" style="display: none;">
                    <i class="material-icons large">image</i>
                    <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; display: none;">
                </div>
                <div class="video-preview" style="display: none;">
                    <video id="videoPreview" controls style="max-width: 100%; display: none;">
                    Your browser doesn't support video playback.
                    </video>
                </div>
                <p class="grey-text">Media preview will appear here</p>
                </div>

            <!--  End  -->

                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">event</i>
                    <textarea id="media_title" name="media_title" class="materialize-textarea" required></textarea>
                    <label for="media_title">Tajuk Media</label>
                </div>
                <div class="input-field col s12">
                    <i class="material-icons prefix">event</i>
                    <textarea id="media_description" name="media_title" class="materialize-textarea" required></textarea>
                    <label for="media_description">Butiran Media</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row center-align">
        <button class="btn-large waves-effect waves-light" type="submit" name="action">
            Muat Naik
            <i class="material-icons right">save</i>
        </button>
        
        <a href="admin_manage_attraction.php" class="btn-large waves-effect waves-light red">
            Batal
            <i class="material-icons right">cancel</i>
        </a>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all file input elements on the page
    const fileInputs = document.querySelectorAll('input[type="file"][accept="image/*,video/*"]');
    
    fileInputs.forEach(function(fileInput) {
        fileInput.addEventListener('change', function(event) {
            // Find the closest form and related preview elements
            const form = event.target.closest('form');
            const imagePreviewContainer = form.querySelector('.image-preview');
            const videoPreviewContainer = form.querySelector('.video-preview');
            const imagePreview = form.querySelector('#imagePreview');
            const videoPreview = form.querySelector('#videoPreview');
            const previewText = form.querySelector('.grey-text');

            // Reset previous previews
            imagePreviewContainer.style.display = 'none';
            videoPreviewContainer.style.display = 'none';
            imagePreview.style.display = 'none';
            videoPreview.style.display = 'none';
            imagePreview.src = '';
            videoPreview.src = '';

            // Get the selected file
            const file = event.target.files[0];

            if (file) {
                // Validate file type
                const fileType = file.type;

                // Preview for images
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                        imagePreview.style.display = 'block';
                        previewText.textContent = file.name;
                    };

                    reader.onerror = function(e) {
                        console.error('File reading error', e);
                        previewText.textContent = 'Error reading file';
                    };

                    reader.readAsDataURL(file);
                } 
                // Preview for videos
                else if (fileType.startsWith('video/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        videoPreview.src = e.target.result;
                        videoPreviewContainer.style.display = 'block';
                        videoPreview.style.display = 'block';
                        previewText.textContent = file.name;
                    };

                    reader.onerror = function(e) {
                        console.error('File reading error', e);
                        previewText.textContent = 'Error reading file';
                    };

                    reader.readAsDataURL(file);
                } 
                // Invalid file type
                else {
                    previewText.textContent = 'Please upload a valid image or video file';
                }
            }
        });
    });
});
</script>


