function attachButiranAmScripts(attractionId) {

        const container = document.getElementById('butiran-am');
        async function fetchAttractionDetails() {
            try {
                const response = await fetch(`/MARANGUIDE/api/fetch_attraction_details.php?id=${attractionId}`);
                
                if (!response.ok) {
                    throw new Error(`Attractions fetch error: ${response.status}`);
                }

                const data = await response.json();
                
                // Check for API-level errors
                if (data.error) {
                    throw new Error(data.message);
                }

                // Update DOM elements
                container.querySelector('#attraction_name').textContent = data.name;
                container.querySelector('#description').textContent = data.description;

                // Operating Days
                const operatingDaysList = container.querySelector('#operatingDays');
                operatingDaysList.innerHTML = data.operatingDays.split(',').map(day => 
                    `<li>${day.trim()}</li>`
                ).join('');
                
                // Operating Hours
                container.querySelector('#operatingHours').textContent = data.operatingHours;
            
            } catch (error) {
                console.error('Failed to fetch attractions:', error);
                container.innerHTML = `
                    <div class="col s12">
                        <div class="card red lighten-2">
                            <div class="card-content white-text">
                                <span class="card-title">Ralat</span>
                                <p>Tidak dapat memuatkan maklumat. ${error.message}</p>
                                <div class="card-action">
                                    <a href="#" onclick="attachButiranAmScripts('${attractionId}')">Cuba Semula</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

             // Find on map button
    document.getElementById('find-on-map').addEventListener('click', async function() {
        try {
            // Fetch attraction details
            const response = await fetch(`/MARANGUIDE/api/fetch_attraction_details.php?id=${attractionId}`);

            // Check for successful response
            if (!response.ok) {
            throw new Error(`Attractions fetch error: ${response.status}`);
            }

            // Parse JSON data
            const data = await response.json();

            // Check for error in response data
            if (data.error) {
            throw new Error(data.message);
            }

            // Extract latitude and longitude
            const { latitude, longitude } = data;

            // Build Google Maps URL
            const googleMapsUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;

            // Redirect to Google Maps
            window.location.href = googleMapsUrl;
        } catch (error) {
            // Handle errors (optional)
            console.error("Error fetching attraction details:", error);
        }
    });

    // Call the fetch function
    fetchAttractionDetails();
    }