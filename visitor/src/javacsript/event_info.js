function attachButiranAcaraScripts(attractionId, eventId) {
    const container = document.getElementById('butiran-acara');
    async function fetchEventDetails() {
        try {
            const response = await fetch(`/MARANGUIDE/api/fetch_event_details.php?attractionId=${attractionId}&eventId=${eventId}`);
            
            if (!response.ok) {
                throw new Error(`Attractions fetch error: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.message);
            }

            // Correct DOM updates
            container.querySelector('#event_name').textContent = data.name;
            container.querySelector('#description').textContent = data.description;

            // Use innerHTML to potentially support formatted content
            const eventDuration = container.querySelector('#eventduration');
            eventDuration.innerHTML = `<li>${data.duration}</li>`;
        
        } catch (error) {
            console.error('Failed to fetch event details:', error);
            container.innerHTML = `
                <div class="col s12">
                    <div class="card red lighten-2">
                        <div class="card-content white-text">
                            <span class="card-title">Ralat</span>
                            <p>Tidak dapat memuatkan maklumat. ${error.message}</p>
                            <div class="card-action">
                                <a href="#" onclick="attachButiranAcaraScripts('${attractionId}', '${eventId}')">Cuba Semula</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }


    fetchEventDetails();
}