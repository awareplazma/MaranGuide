document.addEventListener('DOMContentLoaded', function () {    
   // Create Success Modal
    const createSuccessModal = () => {
        const modalHtml = `
            <div id="success-modal" class="modal">
                <div class="modal-content center-align">
                    <i class="material-icons large green-text">check_circle</i>
                    <h4>Maklum Balas Berjaya</h4>
                    <p>Terima kasih kerana berkongsi pengalaman anda!</p>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-close waves-effect waves-green btn-flat">Tutup</a>
                </div>
            </div>
        `;
        
        // Append modal to body if not exists
        if (!document.getElementById('success-modal')) {
            const modalDiv = document.createElement('div');
            modalDiv.innerHTML = modalHtml.trim();
            document.body.appendChild(modalDiv.firstChild);
        }
    };

    // Feedback form submission
    const form = document.querySelector('#feedback-form');
    if (form) {
        // Create success modal
        createSuccessModal();
        const successModal = document.getElementById('success-modal');
        const modalInstance = successModal ? M.Modal.init(successModal) : null;

        form.addEventListener('submit', async function (event) {
            // Prevent Default submission
            event.preventDefault();

            // Validate form
            const username = document.getElementById('title');
            const content = document.getElementById('content');

            // Client-side validation
            if (!username.value.trim()) {
                M.toast({ html: 'Tajuk isu yang dihadapi semasa melayari' });
                username.focus();
                return;
            }

            if (!content.value.trim()) {
                M.toast({ html: 'Sila terangkan isu anda' });
                content.focus();
                return;
            }

            // Disable submit button to prevent multiple submissions
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menghantar... <i class="material-icons right">hourglass_empty</i>';

            const formData = new FormData(form);

            try {
                const response = await fetch('/MARANGUIDE/api/feedback.php', {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) {
                    // Try to parse error response
                    const errorData = await response.json().catch(() => null);
                    throw new Error(errorData?.message || `HTTP error! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.status === 'success') {
                    // Open success modal
                    if (modalInstance) {
                        modalInstance.open();
                    } else {
                        M.toast({ html: 'Ulasan Berjaya Dihantar' });
                    }

                    // Reset form
                    form.reset();
            
                } else {
                    M.toast({ html: 'Gagal. Cuba lagi' });
                }
            } catch (error) {
                M.toast({ html: `Error: ${error.message}` });
                console.error('Error:', error);
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Hantar Maklum Balas <i class="material-icons right">send</i>';
            }

            // Update text fields and textarea
            M.updateTextFields();
            M.textareaAutoResize(document.querySelector('#content'));
        });
    }

    // Load header
    const headerContainer = document.getElementById('header-html');
    if (headerContainer) {
        fetch('/MARANGUIDE/visitor/header.html')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                headerContainer.innerHTML = data;

                // Initialize Materialize components
                const sidenavElems = document.querySelectorAll('.sidenav');
                M.Sidenav.init(sidenavElems);

                const dropdownElems = document.querySelectorAll('.dropdown-trigger');
                M.Dropdown.init(dropdownElems, {
                    coverTrigger: false,
                    constrainWidth: false,
                    alignment: 'right'
                });
            })
            .catch(error => console.error('Error loading the header:', error));
    }
});
