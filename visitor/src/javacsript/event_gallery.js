function attachGaleriAcaraScripts(attractionId) {
    // [FIX 1] Ensure function is globally accessible for pagination
    window.attachGaleriAcaraScripts = {
        fetchGallery: fetchGallery
    };

    async function fetchGallery(page = 1) {
        // [FIX 2] Consistent error handling and input validation
        if (!attractionId) {
            displayErrorMessage('Invalid attraction selection');
            return;
        }

        // [FIX 3] Select DOM elements with more robust error checking
        const container = document.getElementById('gallery-container');
        const paginationContainer = document.getElementById('pagination');
        const loadingIndicator = document.getElementById('loading');
        const galleryCardTemplate = document.getElementById('gallery-card-template');

        // Validate all required elements
        if (!container || !paginationContainer || !loadingIndicator || !galleryCardTemplate) {
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
            const url = `/MARANGUIDE/api/fetch_event_gallery.php?page=${page}&eventId=${eventId}`;
            
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

            const galleries = data.galleries || [];

            // [FIX 7] Handle empty galleries
            if (galleries.length === 0) {
                displayNoMediaMessage();
                return;
            }

            // [FIX 8] Improved gallery rendering with error handling
            galleries.forEach(gallery => {
                try {
                    const cardClone = galleryCardTemplate.content.cloneNode(true);
                    const cardElement = cardClone.querySelector('.gallery-card');
                    
                    // Image handling
                    const imgElement = cardClone.querySelector('img');
                    imgElement.src = gallery.media_path || '../media/default_image.png';
                    imgElement.alt = gallery.media_title || 'Gallery Image';
                    imgElement.onerror = () => {
                        imgElement.src = '../media/default_image.png';
                    };

                    // Title and description
                    const titleElement = cardClone.querySelector('.card-title');
                    const descriptionElement = cardClone.querySelector('.card-description');
                    const mediaTypeElement = cardClone.querySelector('.media-type');

                    titleElement.textContent = gallery.media_title || 'Untitled';
                    descriptionElement.textContent = gallery.media_description || 'No description';
                    mediaTypeElement.textContent = gallery.media_type || 'Media';

                    // [FIX 9] Add interaction
                    cardElement.addEventListener('click', () => {
                        cardElement.classList.toggle('card-zoomed');
                    });

                    container.appendChild(cardClone);
                } catch (renderError) {
                    console.error('Error rendering gallery item:', renderError);
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
        const container = document.getElementById('gallery-container');
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
        const container = document.getElementById('gallery-container');
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
            li.innerHTML = `<a href="#!" onclick="attachGaleriAcaraScripts.fetchGallery(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }
    }

    // Initial gallery fetch
    fetchGallery();
}