<form id="AttractionMediaForm" action="../manage_attraction_media_process.php" method="POST" enctype="multipart/form-data">
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
                        <input type="file" name="event_media" accept="image/*,video/*">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Upload event image or video">
                    </div>
                </div>
                
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