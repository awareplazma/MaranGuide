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