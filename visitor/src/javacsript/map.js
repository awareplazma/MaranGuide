const MapState = {
    map: null,
    userMarker: null,
    attractionMarkers: [],
    currentUserLocation: null
};

// Utility Functions
const MapUtils = {
    showLoading() {
        const loader = document.getElementById('loading');
        if (loader) loader.style.display = 'block';
    },

    hideLoading() {
        const loader = document.getElementById('loading');
        if (loader) loader.style.display = 'none';
    },

    displayNotification(message, type = 'info') {
        if (M && M.toast) {
            M.toast({ 
                html: message, 
                classes: {
                    'info': 'blue',
                    'error': 'red',
                    'success': 'green'
                }[type] 
            });
        } else {
            console.log(message);
        }
    }
};

// Attractions Fetcher
async function fetchAttractions() {
    try {
        MapUtils.showLoading();
        const response = await fetch('/MARANGUIDE/api/fetch_map.php');
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        
        if (data.status !== 'success') {
            throw new Error(data.message || 'Unknown error');
        }

        return data.attractions;
    } catch (error) {
        MapUtils.displayNotification('Failed to load attractions', 'error');
        console.error('Attractions Fetch Error:', error);
        return [];
    } finally {
        MapUtils.hideLoading();
    }
}

// Add Attractions to Map
function renderAttractions(attractions) {
    // Clear existing markers
    MapState.attractionMarkers.forEach(marker => MapState.map.removeLayer(marker));
    MapState.attractionMarkers = [];

    attractions.forEach(attraction => {
        const marker = L.marker(
            [attraction.latitude, attraction.longitude], 
            { 
                title: attraction.name
            }
        ).addTo(MapState.map);

        marker.bindPopup(`
            <div class="attraction-popup">
                <h5>${attraction.name}</h5>
                <p><strong>Coordinates:</strong> ${attraction.latitude}, ${attraction.longitude}</p>
                <button onclick="navigateToAttraction(${attraction.latitude}, ${attraction.longitude})" 
                        class="btn waves-effect waves-light">
                    Navigate
                </button>
            </div>
        `);

        MapState.attractionMarkers.push(marker);
    });

    // If attractions exist, fit map to show all markers
    if (attractions.length > 0) {
        const group = new L.featureGroup(MapState.attractionMarkers);
        MapState.map.fitBounds(group.getBounds().pad(0.1));
    }
}

// UPage Initialization
async function initPage() {
    try {
        // Show loading indicator
        const loadingIndicator = document.getElementById('loading');
        if (loadingIndicator) loadingIndicator.style.display = 'block';

        // Load header
        try {
            const headerResponse = await fetch('header.html');
            if (!headerResponse.ok) throw new Error(`Header load error: ${headerResponse.status}`);

            const headerData = await headerResponse.text();
            const headerContainer = document.getElementById('header-html');
            
            // Add a check to ensure the element exists before setting innerHTML
            if (headerContainer) {
                headerContainer.innerHTML = headerData;
            } else {
                console.warn('Header container not found');
            }
        } catch (headerError) {
            console.error('Header loading error:', headerError);
            MapUtils.displayNotification('Could not load header', 'error');
        }

        // Initialize Materialize components
        const sidenavElems = document.querySelectorAll('.sidenav');
        M.Sidenav.init(sidenavElems);

        const dropdownElems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdownElems, {
            coverTrigger: false,
            constrainWidth: false,
            alignment: 'right'
        });

    } catch (error) {
        console.error('Error during page initialization:', error);
        MapUtils.displayNotification('Page initialization failed', 'error');
    } finally {
        // Hide loading indicator
        const loadingIndicator = document.getElementById('loading');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
    }
}

// Map Initialization
function initializeMap() {
    try {
        MapState.map = L.map('map', {
            center: [3.584946, 102.779218], // Default location
            zoom: 12,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap Contributors',
            maxZoom: 19
        }).addTo(MapState.map);

        // Always try to locate user first
        MapState.map.locate({
            setView: true,  // Automatically center the map on user's location
            maxZoom: 16,
            timeout: 10000,
            watch: false    // Only locate once
        });

        MapState.map.on('locationfound', function(e) {
            MapState.currentUserLocation = e.latlng;
            const radius = e.accuracy / 2;

            // Clear any existing user marker
            if (MapState.userMarker) {
                MapState.map.removeLayer(MapState.userMarker);
            }

            // User location marker
            MapState.userMarker = L.marker(e.latlng, {
                icon: L.divIcon({
                    className: 'user-location-marker',
                    html: '<div class="pulse"></div>',
                    iconSize: [20, 20]
                })
            }).addTo(MapState.map).bindPopup('Your Location');

            // Optional: Add accuracy circle
            L.circle(e.latlng, radius).addTo(MapState.map);

            // Fetch and render attractions
            fetchAttractions().then(renderAttractions);
        });

        MapState.map.on('locationerror', function(e) {
            console.warn('Location Error:', e.message);
            MapUtils.displayNotification('Unable to determine location. Showing default map.', 'error');
            
            // Fetch attractions even if location fails
            fetchAttractions().then(renderAttractions);
        });

    } catch (error) {
        console.error("Map Initialization Error:", error);
        MapUtils.displayNotification('Map initialization failed', 'error');
    }
}


document.addEventListener('DOMContentLoaded', () => {
    initPage();
    initializeMap();
});

    // Navigation Function
    function navigateToAttraction(lat, lng) {
    if (MapState.currentUserLocation) {
        // Construct the Google Maps URL
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${MapState.currentUserLocation.lat},${MapState.currentUserLocation.lng}&destination=${lat},${lng}`;

        // Open Google Maps in a new tab
        window.open(googleMapsUrl, '_blank');
    } else {
        MapUtils.displayNotification('Location unavailable for precise navigation', 'error');
    }
}


