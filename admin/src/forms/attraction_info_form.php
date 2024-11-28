<form id="AttractionForm" action="src/process/edit_attraction_info.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="attraction_id" value="<?php echo htmlspecialchars($attraction_data['attraction_id']); ?>">


<div id="event_info" class="col s12">
    <div class="event-form-section">
        <div class="section-title">
            <i class="material-icons left">info</i>
            Maklumat Tempat Tarikan
        </div>
    
        <!-- Preview Gambar Laman Depan -->
        <div class="row">
        <label for="event_name" allign="center">Gambar Laman Depan</label>
            <div class="input-field col s12">
                <div class="media-preview" id="attraction_thumbnails">
                <img src="<?php echo htmlspecialchars($attraction_data['attraction_thumbnails']); ?>" alt="Gambar Laman Depan">
                </div>
            </div>
        </div>
        
        <!-- Maklumat Ringkas-->

        <div class="row">
            <div class="input-field col s12">
                <i class="material-icons prefix">event</i>
                <textarea id="event_name" name="event_name" class="materialize-textarea" required><?php echo htmlspecialchars($attraction_data['attraction_name']); ?></textarea>
                <label for="event_name">Nama Tempat Tarikan</label>
            </div>

            <div class="input-field col s12">
                <i class="material-icons prefix">description</i>
                <textarea id="event_description" name="event_description" class="materialize-textarea" required><?php echo htmlspecialchars($attraction_data['attraction_description']); ?></textarea>
                <label for="event_description">Butiran Ringkas</label>
            </div>
        </div>

          <!-- Hari Operasi -->

        <div class="row">
            <div class="col s12 m6">
                <div class="operating-days">
                    <?php

                    // Values in column is comma-separated 
                    
                    $operating_days = explode(",", $attraction_data['attraction_operating_days']); 
                    ?>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Monday" <?php echo (in_array('Monday', $operating_days)) ? 'checked' : ''; ?> required>
                        <span>Monday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Tuesday" <?php echo (in_array('Tuesday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Tuesday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Wednesday" <?php echo (in_array('Wednesday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Wednesday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Thursday" <?php echo (in_array('Thursday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Thursday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Friday" <?php echo (in_array('Friday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Friday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Saturday" <?php echo (in_array('Saturday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Saturday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Sunday" <?php echo (in_array('Sunday', $operating_days)) ? 'checked' : ''; ?>>
                        <span>Sunday</span>
                    </label>
                </div>
            </div>
        </div>
        
         <!-- Waktu Operasi -->

        <div class="row">
            <div class="col s12 m6">
                <div class="datetime-wrapper">
                    <label>Waktu Buka</label>
                    <input type="time" name="event_start_date" class="validate" value="<?php echo date('H:i', strtotime($attraction_data['attraction_opening_hours'])); ?>" required>
                </div>
            </div>
            
            <div class="col s12 m6">
                <div class="datetime-wrapper">
                    <label>Waktu Tutup</label>
                    <input type="time" name="event_end_date" class="validate" value="<?php echo date('H:i', strtotime($attraction_data['attraction_closing_hours'])); ?>" required>
                </div>
            </div>
        </div>
    

    <!-- Status  -->
    
        <div class="row">
            <div class="col s12">
                <div class="input-field">
                    <select name="event_status" required>
                        <option value="" disabled>Choose event status</option>
                        <option value="draf">Draf</option>
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                    </select>
                    <label>Event Status</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Fields -->
    
    <input type="hidden" name="event_created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
</div>
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