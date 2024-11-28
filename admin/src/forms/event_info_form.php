<form id="eventForm" action="admin_edit_event_process.php" method="POST" enctype="multipart/form-data">
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
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Tarikh Mula</label>
                            <<input type="datetime-local" name="event_start_date" class="validate" value="<?php echo date('Y-m-d\TH:i', strtotime($event_data['event_start_date'])); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col s12 m6">
                        <div class="datetime-wrapper">
                            <label>Tarikh Habis</label>
                            <input type="datetime-local" name="event_end_date" class="validate" value="<?php echo date('Y-m-d\TH:i', strtotime($event_data['event_end_date'])); ?>" required>
                        </div>
                    </div>
                </div>
           

            <!-- Status Section -->
            
                
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