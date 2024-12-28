<?php
$attraction_name = $_SESSION['attraction_name'];
// Process deletion if delete button is clicked
if (isset($_GET['delete_media_id'])) {
    $mediaIdToDelete = trim(htmlspecialchars($_GET['delete_media_id'], ENT_QUOTES, 'UTF-8'));

    // Determine the absolute file path based on the media type
    $mediaBasePath = __DIR__ . "/../media/attraction/{$attraction_name}/";
    if (strpos($mediaIdToDelete, '.mp4') !== false) {
        $mediaFilePath = $mediaBasePath . "videos/" . basename($mediaIdToDelete);
    } else {
        $mediaFilePath = $mediaBasePath . "pictures/" . basename($mediaIdToDelete);
    }

    // Debugging: Log the file path
    error_log("Attempting to delete: " . $mediaFilePath);

    // Check if file exists and delete
    if (file_exists($mediaFilePath)) {
        if (unlink($mediaFilePath)) {
            echo "<script>alert('Media deleted successfully!');</script>";
        } else {
            echo "<script>alert('Failed to delete media!');</script>";
        }
    } else {
        error_log("File does not exist: " . $mediaFilePath);
        echo "<script>alert('Media file does not exist!');</script>";
    }
}
?>


<div id="gallery_section">
    <div class="event-form-section">
        <div class="section-title">
            <i class="material-icons left">image</i>
            Galeri
        </div>
        <div class="row">
            <?php if (!empty($media_paths)): ?>
                <?php foreach ($media_paths as $media): ?>
                    <?php
                    
                    $mediaType = 'picture'; // Default type
                    if (strpos($media, '.mp4') !== false) {
                        $mediaType = 'video';
                    }

                    $mediaPath = "http://".$_SERVER['HTTP_HOST']."/MARANGUIDE/media/attraction/{$attraction_name}/".($mediaType === 'video' ? 'videos' : 'pictures')."/".htmlspecialchars(basename(trim($media)), ENT_QUOTES, 'UTF-8');
                    ?>
                    <?php if (!empty(trim($media))): ?>
                        <div class="col s12 m6 l4">
                            <div class="card media-card">
                                <div class="card-image">
                                    <!-- Adjust the media type display -->
                                    <?php if ($mediaType === 'picture'): ?>
                                        <img class="zoomable" src="<?php echo $mediaPath; ?>" alt="Event Image">
                                    <?php elseif ($mediaType === 'video'): ?>
                                        <video class="responsive-video" controls>
                                            <source src="<?php echo $mediaPath; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php endif; ?>

                                    <!-- Delete button -->
                                    <a href="?delete_media_id=<?php echo urlencode(basename(trim($media))); ?>" 
                                        class="delete-media" 
                                        onclick="return confirm('Are you sure you want to delete this image/video?');">
                                        <i class="material-icons">close</i>
                                    </a>
                                </div>
                                <div class="card-content">
                                    <span class="card-title"><?php echo basename(trim($media)); ?></span>
                                    <div class="chip">Event Media</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tiada gambar/video diupload sila upload.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>

    // Zoom functionality for images
    document.addEventListener('DOMContentLoaded', function () {
        const zoomableImages = document.querySelectorAll('.zoomable');
        zoomableImages.forEach(function (img) {
            img.addEventListener('click', function () {
                // Create a modal or popup to display the image in a larger view
                const modal = document.createElement('div');
                modal.classList.add('zoom-modal');
                modal.innerHTML = `<div class="zoom-modal-content"><img src="${this.src}" alt="Zoomed Image"><span class="zoom-close">&times;</span></div>`;
                document.body.appendChild(modal);

                // Close modal
                modal.querySelector('.zoom-close').addEventListener('click', function () {
                    modal.remove();
                });
            });
        });
    });
</script>

<style>
/* Style for zoom modal */
.zoom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.zoom-modal-content img {
    max-width: 90%;
    max-height: 90%;
}

.zoom-close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: white;
    font-size: 2rem;
    cursor: pointer;
}
</style>
