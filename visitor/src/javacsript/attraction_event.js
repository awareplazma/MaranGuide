function attachTarikanAcaraScripts(attractionId) {

    window.attachTarikanAcaraScripts = {
        fetchEvent: fetchEvent
    };

    async function fetchEvent(page = 1) {
  
        if (!attractionId) {
            displayErrorMessage('Invalid attraction selection');
            return;
        }

        const container = document.getElementById('event-container');
        const paginationContainer = document.getElementById('pagination');
        const loadingIndicator = document.getElementById('loading');
        const eventCardTemplate = document.getElementById('event-card-template');

        // Validate all required elements
        if (!container || !paginationContainer || !loadingIndicator || !eventCardTemplate) {
            console.error('Missing required DOM elements');
            displayErrorMessage('System configuration error');
            return;
        }

        // Show loading, clear previous content
        loadingIndicator.style.display = 'block';
        container.innerHTML = '';
        paginationContainer.innerHTML = '';

        try {
            // [FIX 4] Improved URL construction and fetch configuration
            const url = `/MARANGUIDE/api/fetch_event_list.php?page=${page}&attractionId=${attractionId}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            // [FIX 5] Comprehensive error handling
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Parse JSON safely
            const data = await response.json();

            // [FIX 6] API-level error checking
            if (data.error) {
                displayErrorMessage(data.message || 'Unknown error occurred');
                return;
            }

            const events = data.events || [];

    
            if (events.length === 0) {
                displayNoMediaMessage();
                return;
            }

             // Create modal container if it doesn't exist
            let modalContainer = document.getElementById('gallery-modal');
            if (!modalContainer) {
                modalContainer = document.createElement('div');
                modalContainer.id = 'gallery-modal';
                modalContainer.className = 'gallery-modal';
                modalContainer.style.display = 'none';
                document.body.appendChild(modalContainer);
            }

            events.forEach(event => {
                try {
                    const cardClone = eventCardTemplate.content.cloneNode(true);
                    const cardElement = cardClone.querySelector('.event-card');
                    
                    // Image handling
                   const imgElement = cardClone.querySelector('.card-image img');
                    if (imgElement) {
                        imgElement.src = event.event_thumbnails || '../media/default_image.png';
                        imgElement.alt = event.event_name || 'Gallery Image';
                        imgElement.onerror = () => {
                            imgElement.src = '../media/default_image.png';
                        };
                    }

                    // Title and description
                    const titleElement = cardClone.querySelector('.card-title');
                    const descriptionElement = cardClone.querySelector('.card-description');

                    titleElement.textContent = event.event_name || 'Untitled';
                    descriptionElement.textContent = event.event_description || 'No description';

                    // [FIX 9] Add interaction
                   cardElement.addEventListener('click', () => {
                        const url = `event-details.html?eventId=${event.event_id}&attractionId=${attractionId}`;
                        window.location.href = url;

                    });
                

                    container.appendChild(cardClone);
                } catch (renderError) {
                    console.error('Error rendering event item:', renderError);
                }
            });

            // [FIX 10] Improved pagination rendering
            renderPagination(data.currentPage, data.totalPages);

        } catch (error) {
            // [FIX 11] Comprehensive error handling
            console.error('Gallery fetch error:', error);
            
            if (error.name === 'AbortError') {
                displayErrorMessage('Request timed out. Please check your connection.');
            } else if (error instanceof TypeError) {
                displayErrorMessage('Network error. Please check your internet connection.');
            } else {
                displayErrorMessage('An unexpected error occurred. Please try again later.');
            }
        } finally {
            // Always hide loading indicator
            loadingIndicator.style.display = 'none';
        }
    }

    // Helper functions remain mostly the same
    function displayErrorMessage(message) {
        const container = document.getElementById('event-container');
        if (container) {
            container.innerHTML = `
                <div class="col s12">
                    <div class="card red lighten-2">
                        <div class="card-content white-text">
                            <span class="card-title">Error</span>
                            <p>${message}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    function displayNoMediaMessage() {
        const container = document.getElementById('event-container');
        if (container) {
            container.innerHTML = `
                <div class="col s12">
                    <div class="card yellow lighten-2">
                        <div class="card-content">
                            <span class="card-title">No Media Available</span>
                            <p>There are no images or videos for this attraction.</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    function renderPagination(currentPage, totalPages) {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = i === currentPage ? 'active' : 'waves-effect';
            li.innerHTML = `<a href="#!" onclick="attachTarikanAcaraScripts.fetchEvent(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }
    }

   
    fetchEvent();
}