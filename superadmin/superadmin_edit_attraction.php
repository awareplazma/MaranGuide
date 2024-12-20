<?php
include 'superadmin_sidenav.php';
ob_start(); 

if(isset($_GET['attraction_id']))
{
    $attraction_id = $_GET['attraction_id'];

    $sql = "SELECT * FROM attraction WHERE attraction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $attraction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $attraction_name = $attraction_description = $attraction_address = $attraction_opening_time = $attraction_closing_time = '';
    $attraction_latitude = $attraction_longitude = '';
    $selected_admin_id = ''; // New variable to track selected admin

    if ($row = $result->fetch_assoc()) {
        $attraction_name = $row['attraction_name'];
        $attraction_description = $row['attraction_description'];
        $attraction_address = $row['attraction_address'];
        $attraction_opening_hours = date("H:i", strtotime($row['attraction_opening_hours']));
        $attraction_closing_hours = date("H:i", strtotime($row['attraction_closing_hours']));
        $attraction_latitude = $row['attraction_latitude'];
        $attraction_longitude = $row['attraction_longitude'];
        $selected_admin_id = $row['admin_id']; // Retrieve the current admin ID
        $current_attraction_status = $row['attraction_status']; // Retrieve current status

        $attraction_operating_days = array_map('trim', explode(',', $row['attraction_operating_days'] ?? ''));
    }

    $admin_sql = "SELECT admin_id, admin_name FROM adminlist WHERE admin_role = 'owner'";
    $admin_result = $conn->query($admin_sql);
}
else {
    echo "No attraction_id sent from form.";
    exit();
}

//Thumbnail
// HIGHLIGHTED CHANGES START
$thumbnail = '/media/default_image.png'; 
if (!empty($row['attraction_thumbnails'])) {
    // Strip any leading slashes and use a consistent path construction
    $relative_path = ltrim($row['attraction_thumbnails'], '/');
    $thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative_path;
    
    // Normalize path separators
    $thumbnail_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $thumbnail_path);
    
    // Debug information
    error_log("Thumbnail Path: " . $thumbnail_path);
    
    // Check file existence with realpath for additional security
    $real_path = realpath($thumbnail_path);
    if ($real_path && file_exists($real_path)) {
        // Use web-accessible path
        $thumbnail = '/' . $relative_path;
    }
}
// HIGHLIGHTED CHANGES END
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lokasi</title>
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
    <link rel="stylesheet" href="../project.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <h1 class="form-title">Edit Lokasi</h1>
        <form id="locationForm" class="form-card" action="src/process/superadmin_edit_attraction_process.php" method="POST" enctype="multipart/form-data">
            <!-- Photo -->
            <div class="upload-container">
                <label class="upload-icon" for="ThumbnailUpload">
                    <i class="material-icons">add_photo_alternate</i>
                </label>
                <input type="file" id="ThumbnailUpload" name="ThumbnailUpload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <div id="imagePreview" class="image-preview">
                    <img id="uploadedImage" src="<?php echo htmlspecialchars($thumbnail); ?>" alt="Image Preview" />
                    <button type="button" class="remove-btn" onclick="removeImage()">
                        <i class="material-icons">delete</i>
                        Remove
                    </button>
                </div>
            </div>
            <!-- -->
        <input type="hidden" name="attraction_id" value="<?= htmlspecialchars($attraction_id ?? '') ?>">
            <div class="form-group">
                <label class="form-label" for="owner">Pemilik Lokasi</label>
                <select id="owner" name="admin_id" class="form-input" required>
                    <?php if ($admin_result && $admin_result->num_rows > 0): ?>
                        <option value="" disabled selected>Pilih Pemilik Lokasi</option>
                        <?php while ($row = $admin_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['admin_id']); ?>" 
                                    <?= isset($selected_admin_id) && $row['admin_id'] == $selected_admin_id ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($row['admin_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>No owners found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="attraction_status">Status</label>
                <select id="attraction_status" name="attraction_status" class="form-input" required>
                   <option value="aktif" <?= isset($current_attraction_status) && $current_attraction_status == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                   <option value="tidak aktif" <?= isset($current_attraction_status) && $current_attraction_status == 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="attraction_name">Nama Lokasi</label>
                <input type="text" id="attraction_name" name="attraction_name" class="form-input" 
                value="<?= htmlspecialchars($attraction_name ?? '') ?>"  required>
            </div>

            <div class="form-group">
                <label class="form-label" for="attraction_description">Deskripsi Lokasi</label>
                <input type="text" id="attraction_description" name="attraction_description" class="form-input" rows="4" 
                value="<?= htmlspecialchars($attraction_description ?? '') ?>" required></input>
            </div>

            <div class="form-group">
                <label class="form-label" for="attraction_address">Alamat Lokasi</label>
                <input type="text" id="attraction_address" name="attraction_address" class="form-input" 
                value="<?= htmlspecialchars($attraction_address ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hari Operasi</label>
                <div class="operating-days">
                    <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Monday" <?= in_array('Monday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Monday</span>
                </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Tuesday" <?= in_array('Tuesday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Tuesday</span>
            </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Wednesday" <?= in_array('Wednesday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Wednesday</span>
            </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Thursday" <?= in_array('Thursday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Thursday</span>
            </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Friday" <?= in_array('Friday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Friday</span>
            </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Saturday" <?= in_array('Saturday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Saturday</span>
            </label>
            <label class="day-checkbox">
                <input type="checkbox" name="attraction_operating_days[]" value="Sunday" <?= in_array('Sunday', $attraction_operating_days) ? 'checked' : '' ?>>
                <span>Sunday</span>
            </label>
        </div>
    </div>

            <div class="form-group">
                <label class="form-label">Waktu Operasi</label>
                <div class="time-inputs">
                    <div class="form-group">
                        <label class="form-label" for="opening_time">Waktu Buka</label>
                        <input type="time" id="opening_time" name="opening_time" class="form-input" 
                        value="<?= htmlspecialchars($attraction_opening_hours ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="closing_time">Waktu Tutup</label>
                        <input type="time" id="closing_time" name="closing_time" class="form-input" 
                        value="<?= htmlspecialchars($attraction_closing_hours ?? '') ?>"required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="day-checkbox">
                    <input type="checkbox" id="manualCoordinates" onchange="toggleCoordinateInput()">
                    <span>Input koordinat manual</span>
                </label>
            </div>

            <div class="coordinate-inputs">
                <div class="form-group">
                    <label class="form-label" for="attraction_latitude">Latitude</label>
                    <input type="text" id="attraction_latitude" name="attraction_latitude" class="form-input" 
                    value="<?= htmlspecialchars($attraction_latitude ?? '') ?>"readonly required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="attraction_longitude">Longitude</label>
                    <input type="text" id="attraction_longitude" name="attraction_longitude" class="form-input" 
                    value="<?= htmlspecialchars($attraction_longitude ?? '') ?>" readonly required>
                </div>
            </div>

            <div id="map"></div>

            <button type="submit" class="submit-button">
                <i class="material-icons">check_circle</i>
                Edit
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        let map, marker;

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeMap, 100);
        });

        function initializeMap() {
    try {
        map = L.map('map', {
            center: [-2.548926, 118.014863],
            zoom: 5,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Pre-existing coordinate
        const initialLat = parseFloat(document.getElementById('attraction_latitude').value);
        const initialLng = parseFloat(document.getElementById('attraction_longitude').value);
        if (!isNaN(initialLat) && !isNaN(initialLng)) {
            setMarkerAndCoordinates(initialLat, initialLng);
            map.setView([initialLat, initialLng], 15); 
        }

        map.on('click', function(e) {
            if (!document.getElementById('manualCoordinates').checked) {
                setMarkerAndCoordinates(e.latlng.lat, e.latlng.lng);
            }
        });

        setTimeout(() => {
            map.invalidateSize();
        }, 100);

    } catch (error) {
        console.error("Error initializing map:", error);
    }
}

        function setMarkerAndCoordinates(lat, lng) {
            if (!map) return;

            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker([lat, lng]).addTo(map);
            
            document.getElementById('attraction_latitude').value = lat.toFixed(6);
            document.getElementById('attraction_longitude').value = lng.toFixed(6);
        }

        function toggleCoordinateInput() {
            const isManual = document.getElementById('manualCoordinates').checked;
            const latInput = document.getElementById('attraction_latitude');
            const lngInput = document.getElementById('attraction_longitude');
            
            latInput.readOnly = !isManual;
            lngInput.readOnly = !isManual;
            
            if (isManual && marker && map) {
                map.removeLayer(marker);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
        const elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
        });

        // Add console logging for debugging
        document.getElementById('locationForm').addEventListener('submit', function(event) {
            const lat = document.getElementById('attraction_latitude').value;
            const lng = document.getElementById('attraction_longitude').value;
    
            
            const operatingDays = document.querySelectorAll('input[name="attraction_operating_days[]"]:checked');
            if (operatingDays.length === 0) {
                event.preventDefault();
                alert('Please select at least one operating day');
                return;
            }

            // Add debugging console logs
            console.log('Form submitted');
            console.log('Latitude:', lat);
            console.log('Longitude:', lng);
        });

        //Preview Image
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        // ADD MORE ROBUST ERROR HANDLING
        reader.onerror = function(e) {
            console.error("Error reading file:", e);
            alert("Error reading image file");
        };

        reader.onload = function() {
            const uploadedImage = document.getElementById('uploadedImage');
            
            // ADD EXTENSIVE LOGGING
            console.log('File read successfully');
            console.log('New image source:', reader.result);
            
            // Ensure the image source is set
            if (reader.result) {
                uploadedImage.src = reader.result;
                
                // Verify image load
                uploadedImage.onload = function() {
                    console.log('Image loaded successfully');
                };
                
                uploadedImage.onerror = function() {
                    console.error('Failed to load image');
                    alert('Failed to load image');
                };
            }
            
            // Hidden input for tracking thumbnail removal
            const removeThumbnailInput = document.getElementById('remove_thumbnail');
            if (removeThumbnailInput) {
                removeThumbnailInput.value = '0';
            } else {
                console.warn('remove_thumbnail input not found');
            }
        };

        if (file) {
            // Log file details
            console.log('Selected file:', {
                name: file.name,
                type: file.type,
                size: file.size
            });
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        // MORE COMPREHENSIVE IMAGE REMOVAL
        try {
            // Reset file input
            const fileInput = document.getElementById('ThumbnailUpload');
            if (fileInput) {
                fileInput.value = '';
            }
            
            // Set default image
            const uploadedImage = document.getElementById('uploadedImage');
            if (uploadedImage) {
                uploadedImage.src = '/media/default_image.png';
                
                // Verify default image load
                uploadedImage.onload = function() {
                    console.log('Default image loaded successfully');
                };
                
                uploadedImage.onerror = function() {
                    console.error('Failed to load default image');
                    alert('Failed to load default image');
                };
            }
            
            // Set remove thumbnail flag
            const removeThumbnailInput = document.getElementById('remove_thumbnail');
            if (removeThumbnailInput) {
                removeThumbnailInput.value = '1';
            } else {
                console.warn('remove_thumbnail input not found');
            }
        } catch (error) {
            console.error('Error in removeImage:', error);
        }
    }

    // ADD INITIAL PAGE LOAD VERIFICATION
    document.addEventListener('DOMContentLoaded', function() {
        const uploadedImage = document.getElementById('uploadedImage');
        
        // Verify initial image
        if (uploadedImage) {
            uploadedImage.onerror = function() {
                console.error('Initial image failed to load');
                console.log('Attempted source:', uploadedImage.src);
                
                // Fallback to default image
                uploadedImage.src = '/media/default_image.png';
            };
        }
    });
    </script>
</body>
</html>