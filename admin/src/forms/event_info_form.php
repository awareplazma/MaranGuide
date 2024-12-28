<form id="eventForm" action="/MARANGUIDE/admin/src/process/admin_edit_event_process.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_data['event_id']); ?>">

        <!-- Event Info Tab -->
        <div id="event_info" class="col s12">
            <!-- Basic Information Section -->
            <div class="event-form-section">
                <div class="section-title">
                    <i class="material-icons left">info</i>
                    Basic Information
                </div>
                
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">event</i>
                        <textarea id="event_name" name="event_name" class="materialize-textarea" required><?php echo htmlspecialchars($event_data['event_name']); ?></textarea>
                        <label for="event_name">Nama Acara</label>
                    </div>

                    <div class="input-field col s12">
                        <i class="material-icons prefix">description</i>
                        <textarea id="event_description" name="event_description" class="materialize-textarea" required><?php echo htmlspecialchars($event_data['event_description']); ?></textarea>
                        <label for="event_description">Butiran Ringkas Acara</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">description</i>
                        <select id="event_status" name="event_status" required>
                            <option value="" disabled <?php echo isset($event_status) && $event_status == '' ? 'selected' : ''; ?>>Status</option>
                            <option value="draf" <?php echo isset($event_status) && $event_status == 'draf' ? 'selected' : ''; ?>>Draf</option>
                            <option value="active" <?php echo isset($event_status) && $event_status == 'active' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="complete" <?php echo isset($event_status) && $event_status == 'complete' ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                        <label for="event_status">Status Acara</label>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Tarikh Mula</label>
                            <input type="datetime-local" name="event_start_date" class="validate" value="<?php echo date('Y-m-d\TH:i', strtotime($event_data['event_start_date'])); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Tarikh Habis</label>
                            <input type="datetime-local" name="event_end_date" class="validate" value="<?php echo date('Y-m-d\TH:i', strtotime($event_data['event_end_date'])); ?>" required>
                        </div>
                    </div>
                </div>
           

            <!-- Hidden Fields -->
            <input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($attraction['attraction_id']); ?>">
            <input type="hidden" name="event_created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">

                <!-- Submit Buttons -->
            <div class="row center-align">
                <button class="btn-large waves-effect waves-light" type="submit" name="action">
                    Simpan
                    <i class="material-icons right">save</i>
                </button>
                
                <a href="events_list.php" class="btn-large waves-effect waves-light red">
                    Cancel
                    <i class="material-icons right">cancel</i>
                </a>
            </div>
        </div> 
</form>
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