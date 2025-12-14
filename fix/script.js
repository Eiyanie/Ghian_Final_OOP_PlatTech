document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('book-carousel');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    // --- Modal Variables ---
    const modal = document.getElementById('book-modal');
    const modalContentContainer = document.getElementById('modal-body-content');
    const closeBtn = document.querySelector('.close-btn');
    
    // --- Carousel Navigation Logic ---
    const scrollDistance = 200; 

    if (prevBtn && carousel) {
        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -scrollDistance, behavior: 'smooth' });
        });
    }

    if (nextBtn && carousel) {
        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: scrollDistance, behavior: 'smooth' });
        });
    }

    // --- Modal Close Function ---
    window.closeModal = () => {
        modal.style.display = 'none';
        modalContentContainer.innerHTML = '';
        window.location.reload();
    };

    // Close modal listeners
    if (closeBtn) {
        closeBtn.addEventListener('click', window.closeModal);
    }
    
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            window.closeModal();
        }
    });

    // --- Helper function for Book Deletion ---
    function attachDeleteListener() {
        const deleteBtn = document.getElementById('delete-book-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                if (confirm("Are you sure you want to permanently delete this book? This cannot be undone.")) {
                    const bookId = this.dataset.id;
                    const formData = new FormData();
                    formData.append('id', bookId);
                    formData.append('action', 'delete');
                    
                    const originalText = this.textContent;
                    this.disabled = true;
                    this.textContent = 'Deleting...';

                    fetch('book_details.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.startsWith("SUCCESS:")) {
                            alert('Book deleted successfully!');
                            window.closeModal(); 
                        } else {
                            alert('Error: ' + data);
                            this.disabled = false;
                            this.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        alert('Network Error: ' + error.message);
                        this.disabled = false;
                        this.textContent = originalText;
                    });
                }
            });
        }
    }

    // --- FIXED: Helper function to handle form submission ---
    function handleFormSubmission(e) {
        e.preventDefault(); 
        
        const form = e.target;
        const submitBtn = form.querySelector('.btn-primary, .btn-submit');
        const originalSubmitText = submitBtn ? submitBtn.textContent : 'Save Changes';
        
        if (submitBtn) {
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
        }

        // IMPORTANT: Get the form action and method
        const formAction = form.getAttribute('action') || form.action;
        const formMethod = form.getAttribute('method') || 'POST';
        
        console.log('Form submitting to:', formAction, 'Method:', formMethod); // DEBUG

        const formData = new FormData(form);

        fetch(formAction, {
            method: formMethod.toUpperCase(),
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status); // DEBUG
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text.substring(0, 100)); // DEBUG
            
            if (text.startsWith("SUCCESS:")) {
                alert('Success! ' + text.substring(8)); 
                window.closeModal();
            } else {
                // Server returned HTML form with error
                modalContentContainer.innerHTML = text;
                
                // Re-attach handlers
                const newForm = document.getElementById('add-book-form') || 
                                document.getElementById('book-details-form');
                if (newForm) {
                    newForm.addEventListener('submit', handleFormSubmission);
                    
                    if (newForm.id === 'book-details-form') {
                        attachDeleteListener();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error); // DEBUG
            alert('Network error: ' + error.message);
            window.closeModal();
        })
        .finally(() => {
            if (submitBtn && document.body.contains(submitBtn)) {
                submitBtn.textContent = originalSubmitText;
                submitBtn.disabled = false;
            }
        });
    }

    // --- Event listener for Book Cards (Details Modal) ---
    document.querySelectorAll('.book-card').forEach(card => {
        card.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const url = `book_details.php?id=${bookId}`; 

            console.log('Loading book details for ID:', bookId); // DEBUG

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => Promise.reject(text));
                    }
                    return response.text();
                })
                .then(html => {
                    modalContentContainer.innerHTML = html;
                    modal.style.display = 'flex';
                    
                    const detailsForm = document.getElementById('book-details-form');
                    if (detailsForm) {
                        console.log('Book details form found, attaching listeners'); // DEBUG
                        detailsForm.addEventListener('submit', handleFormSubmission);
                        attachDeleteListener();
                    } else {
                        console.error('Book details form NOT found in loaded HTML'); // DEBUG
                    }
                })
                .catch(error => {
                    console.error('Error loading book details:', error);
                    
                    if (typeof error === 'string' && error.includes('<p class=')) {
                        modalContentContainer.innerHTML = error;
                        modal.style.display = 'flex';
                    } else {
                        alert('Could not load book details. Error: ' + (error.message || error));
                    }
                });
        });
    });

    // --- Event listener for ADD BOOK button ---
    const addBookTrigger = document.getElementById('add-book-trigger');

    if (addBookTrigger) {
        addBookTrigger.addEventListener('click', (e) => {
            e.preventDefault(); 

            console.log('Loading add book form'); // DEBUG

            fetch('add_book.php')
                .then(response => response.text())
                .then(html => {
                    modalContentContainer.innerHTML = html;
                    modal.style.display = 'flex'; 

                    const form = document.getElementById('add-book-form');
                    if (form) {
                        console.log('Add book form found, attaching listener'); // DEBUG
                        form.addEventListener('submit', handleFormSubmission);
                    } else {
                        console.error('Add book form NOT found in loaded HTML'); // DEBUG
                    }
                })
                .catch(error => {
                    console.error('Error loading add book form:', error);
                    modalContentContainer.innerHTML = `<p class="error">Error loading form: ${error.message}</p>`;
                    modal.style.display = 'flex';
                });
        });
    }

});