<?php
include 'superadmin_sidenav.php';
ob_start(); 

if (isset($_SESSION['error_message'])) {
    echo "<div style='color:red;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo "<div style='color:green;'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);

    
}

$admin_sql = "SELECT admin_id, admin_name FROM adminlist WHERE admin_role = 'owner'";
$admin_result = $conn->query($admin_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lokasi Baru</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="src/css/superadmin_section.css">
    <link rel="stylesheet" href="src/css/superadmin_sidenav.css">
    <link rel="stylesheet" href="../project.css">
    
    
</head>
<body>
    <div class="dashboard-container">
        <h1 class="form-title">Tambah Lokasi Baru</h1>
        <form id="locationForm" class="form-card" action="src/process/superadmin_add_attraction_process.php" method="POST" enctype="multipart/form-data">
            <div class="upload-container">
                <label class="upload-icon" for="ThumbnailUpload">
                    <i class="material-icons">add_photo_alternate</i>
                </label>
                <input type="file" id="ThumbnailUpload" name="ThumbnailUpload" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <div id="imagePreview" class="image-preview">
                    <img id="uploadedImage" src="" alt="Image Preview" />
                    <button type="button" class="remove-btn" onclick="removeImage()">
                        <i class="material-icons">delete</i>
                        Remove
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="attraction_name">Nama Lokasi</label>
                <input type="text" id="attraction_name" name="attraction_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="attraction_description">Deskripsi Lokasi</label>
                <textarea id="attraction_description" name="attraction_description" class="form-input" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="attraction_address">Alamat Lokasi</label>
                <input type="text" id="attraction_address" name="attraction_address" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Hari Operasi</label>
                <div class="operating-days">
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Monday">
                        <span>Monday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Tuesday">
                        <span>Tuesday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Wednesday">
                        <span>Wednesday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Thursday">
                        <span>Thursday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Friday">
                        <span>Friday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Saturday">
                        <span>Saturday</span>
                    </label>
                    <label class="day-checkbox">
                        <input type="checkbox" name="operating_days[]" value="Sunday">
                        <span>Sunday</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Waktu Operasi</label>
                <div class="time-inputs">
                    <div class="form-group">
                        <label class="form-label" for="opening_time">Waktu Buka</label>
                        <input type="time" id="opening_time" name="opening_time" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="closing_time">Waktu Tutup</label>
                        <input type="time" id="closing_time" name="closing_time" class="form-input" required>
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
                    <input type="text" id="attraction_latitude" name="attraction_latitude" class="form-input" readonly required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="attraction_longitude">Longitude</label>
                    <input type="text" id="attraction_longitude" name="attraction_longitude" class="form-input" readonly required>
                </div>
            </div>

            <div id="map"></div>

            <button type="submit" class="submit-button">
                <i class="material-icons">check_circle</i>
                Submit
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
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

        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function() {
                const previewContainer = document.getElementById('imagePreview');
                const uploadedImage = document.getElementById('uploadedImage');
                const uploadIcon = document.querySelector('.upload-icon');

                uploadedImage.src = reader.result;
                previewContainer.style.display = 'block';
                uploadIcon.style.display = 'none';
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('ThumbnailUpload').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('uploadedImage').src = '';
            document.querySelector('.upload-icon').style.display = 'flex';
        }

        document.getElementById('locationForm').addEventListener('submit', function(event) {
            const lat = document.getElementById('attraction_latitude').value;
            const lng = document.getElementById('attraction_longitude').value;
            
            if (!lat || !lng) {
                event.preventDefault();
                alert('Please set the location coordinates');
                return;
            }
            
            const operatingDays = document.querySelectorAll('input[name="operating_days[]"]:checked');
            if (operatingDays.length === 0) {
                event.preventDefault();
                alert('Please select at least one operating day');
                return;
            }
        });
    </script>
</body>
</html>