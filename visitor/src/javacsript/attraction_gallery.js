function attachGaleriScripts(attractionId) {
    window.attachGaleriScripts = {
        fetchGallery: fetchGallery
    };

    async function fetchGallery(page = 1) {
        if (!attractionId) {
            displayErrorMessage('Invalid attraction selection');
            return;
        }

        const container = document.getElementById('gallery-container');
        const paginationContainer = document.getElementById('pagination');
        const loadingIndicator = document.getElementById('loading');
        const galleryCardTemplate = document.getElementById('gallery-card-template');

        if (!container || !paginationContainer || !loadingIndicator || !galleryCardTemplate) {
            console.error('Missing required DOM elements');
            displayErrorMessage('System configuration error');
            return;
        }

        loadingIndicator.style.display = 'block';
        container.innerHTML = '';
        paginationContainer.innerHTML = '';

        try {
            const url = `/MARANGUIDE/api/fetch_gallery.php?page=${page}&attractionId=${attractionId}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Full error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log(data);

            if (data.error) {
                displayErrorMessage(data.message || 'Unknown error occurred');
                return;
            }

            const galleries = data.galleries || [];

            if (galleries.length === 0) {
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

            galleries.forEach(gallery => {
                try {
                    const cardClone = galleryCardTemplate.content.cloneNode(true);
                    const cardElement = cardClone.querySelector('.gallery-card');
                    
                    // Clear existing content in media container
                    const mediaContainer = cardElement.querySelector('.card-image');
                    if (mediaContainer) {
                        mediaContainer.innerHTML = '';
                        
                        // Create appropriate media element
                        if (gallery.media_type === 'video') {
                            const videoElement = document.createElement('video');
                            videoElement.className = 'card-video';
                            videoElement.controls = true;
                            videoElement.src = gallery.media_path;
                            videoElement.preload = 'metadata';
                            mediaContainer.appendChild(videoElement);
                        } else {
                            const imgElement = document.createElement('img');
                            imgElement.className = 'card-image';
                            imgElement.src = gallery.media_path || '../media/default_image.png';
                            imgElement.alt = gallery.media_title || 'Gallery Image';
                            imgElement.onerror = () => {
                                imgElement.src = '../media/default_image.png';
                            };
                            mediaContainer.appendChild(imgElement);
                        }
                    }

                    // Set title and description
                    const titleElement = cardElement.querySelector('.card-title');
                    const descriptionElement = cardElement.querySelector('.card-description');

                    if (titleElement) titleElement.textContent = gallery.media_title || 'Untitled';
                    if (descriptionElement) descriptionElement.textContent = gallery.media_description || 'No description';

                    // Add click handler for zoom view
                    cardElement.addEventListener('click', () => {
                        modalContainer.innerHTML = `
                            <div class="modal-content">
                                <div class="modal-media">
                                    ${gallery.media_type === 'video' 
                                        ? `<video controls autoplay class="zoomed-video">
                                            <source src="${gallery.media_path}" type="video/mp4">
                                           </video>`
                                        : `<img src="${gallery.media_path}" alt="${gallery.media_title}" class="zoomed-image">`
                                    }
                                </div>
                                <div class="modal-info">
                                    <h3>${gallery.media_title || 'Untitled'}</h3>
                                    <p>${gallery.media_description || 'No description'}</p>
                                </div>
                                <button class="modal-close">&times;</button>
                            </div>
                        `;
                        
                        modalContainer.style.display = 'flex';
                        
                        // Close button handler
                        modalContainer.querySelector('.modal-close').onclick = (e) => {
                            e.stopPropagation();
                            modalContainer.style.display = 'none';
                            const video = modalContainer.querySelector('video');
                            if (video) video.pause();
                        };
                        
                        // Background click handler
                        modalContainer.onclick = (e) => {
                            if (e.target === modalContainer) {
                                modalContainer.style.display = 'none';
                                const video = modalContainer.querySelector('video');
                                if (video) video.pause();
                            }
                        };
                    });

                    container.appendChild(cardClone);
                } catch (renderError) {
                    console.error('Error rendering gallery item:', renderError);
                }
            });

            renderPagination(data.currentPage, data.totalPages);

        } catch (error) {
            console.error('Gallery fetch error:', error);
            
            if (error.name === 'AbortError') {
                displayErrorMessage('Request timed out. Please check your connection.');
            } else if (error instanceof TypeError) {
                displayErrorMessage('Network error. Please check your internet connection.');
            } else {
                displayErrorMessage('An unexpected error occurred. Please try again later.');
            }
        } finally {
            loadingIndicator.style.display = 'none';
        }
    }

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
            li.innerHTML = `<a href="#!" onclick="attachGaleriScripts.fetchGallery(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }
    }

   
    fetchGallery();
}