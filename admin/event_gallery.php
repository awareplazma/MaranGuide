 <?php
        $media_paths = explode(',', $event_data['event_media']); // Split if comma-separated
        ?>

        <div id="gallery_section">
            <div class="event-form-section">
                <div class="row">
                    <?php foreach($media_paths as $media_path): ?>
                        <?php if(!empty(trim($media_path))): ?>
                            <div class="col s12 m6 l4">
                                <div class="card media-card">
                                    <div class="card-image">
                                        <img src="<?php echo htmlspecialchars(trim($media_path)); ?>" alt="Event Image">
                                        <span class="delete-media">
                                            <i class="material-icons">close</i>
                                        </span>
                                    </div>
                                    <div class="card-content">
                                        <span class="card-title"><?php echo basename(trim($media_path)); ?></span>
                                        <div class="chip">Event Media</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>