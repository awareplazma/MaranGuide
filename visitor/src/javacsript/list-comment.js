function attachUlasanScripts(attractionId) {
    window.attachUlasanScripts = {
        fetchComment: fetchComment
    };

    async function fetchComment(page = 1) {
        if (!attractionId) {
            displayErrorMessage('Invalid attraction selection');
            return;
        }

        const container = document.getElementById('comment-container');
        const paginationContainer = document.getElementById('pagination');
        const loadingIndicator = document.getElementById('loading');
        const commentCardTemplate = document.getElementById('comment-card-template');

        if (!container || !paginationContainer || !loadingIndicator || !commentCardTemplate) {
            console.error('Missing required DOM elements');
            displayErrorMessage('System configuration error');
            return;
        }

        loadingIndicator.style.display = 'block';
        container.innerHTML = '';
        paginationContainer.innerHTML = '';

        try {
            const url = `/MARANGUIDE/api/fetch_comment.php?page=${page}&attractionId=${attractionId}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.error) {
                displayErrorMessage(data.message || 'Unknown error occurred');
                return;
            }

            // Fix: Use data.comments instead of undefined comments variable
            if (!data.comments || data.comments.length === 0) {
                displayNoMediaMessage();
                return;
            }

            // Process each comment
            data.comments.forEach(comment => {
                try {
                    const cardClone = commentCardTemplate.content.cloneNode(true);
                    const cardElement = cardClone.querySelector('.event-card');
                    
                    // Username
                    const usernameElement = cardClone.querySelector('.card-username');
                    usernameElement.textContent = comment.username || 'Anonymous';
                    
                    // Content
                    const titleElement = cardClone.querySelector('.card-title');
                    const descriptionElement = cardClone.querySelector('.card-description');
                    
                    titleElement.textContent = comment.user || 'Anonymous';
                    descriptionElement.textContent = comment.content || 'No comment';

                    // Create star rating display
                    const ratingElement = cardClone.querySelector('.card-rating');
                    const rating = parseInt(comment.rating) || 0;
                    
                    const starRatingDiv = document.createElement('div');
                    starRatingDiv.className = 'star-rating';
                    
                    for (let i = 1; i <= 5; i++) {
                        const star = document.createElement('span');
                        star.className = 'material-icons';
                        // Set appropriate star icon based on rating
                        if (i <= rating) {
                            star.textContent = 'star'; // Filled star
                            star.style.color = '#FFD700'; // Gold color for filled stars
                        } else {
                            star.textContent = 'star_border'; // Empty star
                        }
                        starRatingDiv.appendChild(star);
                    }
                    
                    ratingElement.appendChild(starRatingDiv);
                    
                    container.appendChild(cardClone);
                } catch (renderError) {
                    console.error('Error rendering comment item:', renderError);
                }
            });

            renderPagination(data.currentPage, data.totalPages);

        } catch (error) {
            console.error('Comment fetch error:', error);
            
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

    // Helper functions remain the same...
    function displayErrorMessage(message) {
        const container = document.getElementById('comment-container');
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
        const container = document.getElementById('comment-container');
        if (container) {
            container.innerHTML = `
                <div class="col s12">
                    <div class="card yellow lighten-2">
                        <div class="card-content">
                            <span class="card-title">Tiada Komen</span>
                            <p>Tiada komen diberi.</p>
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
            li.innerHTML = `<a href="#!" onclick="attachUlasanScripts.fetchComment(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }
    }

    // Initialize the first fetch
    fetchComment();
}