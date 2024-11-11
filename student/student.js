// Helper function to display error messages dynamically
function displayErrors(errors, errorElementId) {
    let errorElement = document.getElementById(errorElementId);
    errorElement.innerHTML = ''; // Clear previous errors
    errors.forEach(function (error) {
        let errorItem = document.createElement('p');
        errorItem.style.color = 'red';
        errorItem.innerText = error;
        errorElement.appendChild(errorItem);
    });
}

// Helper function to handle success messages (custom or alert-based)
// Function to show custom success messages
function showSuccessMessage(message) {
    let successContainer = document.createElement('div');
    successContainer.className = 'notification';
    successContainer.innerText = message;
    document.body.appendChild(successContainer);

    // Show the notification
    setTimeout(() => {
        successContainer.classList.add('show');
    }, 100);

    // Automatically hide the success message after 3 seconds
    setTimeout(() => {
        successContainer.classList.remove('show');
        setTimeout(() => {
            successContainer.remove();
        }, 500); // Wait for the fade-out transition to complete before removing
    }, 3000);
}

// Client-side validation for Testimonial Form
function validateTestimonialForm() {
    let name = document.getElementById('name').value.trim();
    let profession = document.getElementById('profession').value.trim();
    let testimonialText = document.getElementById('testimonial_text').value.trim();
    let errors = [];

    // Validate name
    if (!name) {
        errors.push('Name is required.');
    }

    // Validate profession
    if (!profession) {
        errors.push('Profession is required.');
    }

    // Validate testimonial text
    if (!testimonialText) {
        errors.push('Testimonial text is required.');
    }

    return errors;
}

// Handle Testimonial Form Submission with validation, loading, and Ajax
document.querySelector('form[action="submit_testimonial.php"]').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    let errors = validateTestimonialForm(); // Perform client-side validation
    let errorContainer = document.getElementById('testimonial-errors'); // Error container
    let formData = new FormData(this); // Capture form data
    let submitButton = this.querySelector('button'); // Get the submit button
    let loadingMessage = document.getElementById('testimonial-loading'); // Loading message element

    if (errors.length > 0) {
        displayErrors(errors, 'testimonial-errors'); // Display validation errors
        return; // Stop if there are validation errors
    }

    // Clear previous errors and show loading message
    errorContainer.innerHTML = '';
    loadingMessage.style.display = 'block';
    submitButton.disabled = true;

    // Ajax request to submit the form
    fetch('submit_testimonial.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading message and re-enable the button
        loadingMessage.style.display = 'none';
        submitButton.disabled = false;

        if (data.success) {
            showSuccessMessage('Testimonial submitted successfully!');
            this.reset(); // Clear the form
        } else {
            // Display specific error messages from server
            displayErrors(data.errors || ['Failed to submit testimonial. Please try again.'], 'testimonial-errors');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayErrors(['An error occurred while submitting the testimonial. Please try again later.'], 'testimonial-errors');
        loadingMessage.style.display = 'none';
        submitButton.disabled = false;
    });
});

// Password validation in the settings form
function validatePasswordForm() {
    let password = document.getElementById('password').value.trim();
    let errors = [];

    // Validate password strength (at least 6 characters, contains numbers and letters)
    if (password.length < 6) {
        errors.push('Password must be at least 6 characters long.');
    }

    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number.');
    }

    if (!/[a-zA-Z]/.test(password)) {
        errors.push('Password must contain at least one letter.');
    }

    return errors;
}

// Handle Settings Update Form Submission with validation, loading, and Ajax
document.querySelector('form[action="update_settings.php"]').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    let errors = validatePasswordForm(); // Perform client-side validation
    let errorContainer = document.getElementById('settings-errors'); // Error container
    let formData = new FormData(this); // Capture form data
    let submitButton = this.querySelector('button'); // Get the submit button
    let loadingMessage = document.getElementById('settings-loading'); // Loading message element

    if (errors.length > 0) {
        displayErrors(errors, 'settings-errors'); // Display validation errors
        return; // Stop if there are validation errors
    }

    // Clear previous errors and show loading message
    errorContainer.innerHTML = '';
    loadingMessage.style.display = 'block';
    submitButton.disabled = true;

    // Ajax request to submit the form
    fetch('update_settings.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading message and re-enable the button
        loadingMessage.style.display = 'none';
        submitButton.disabled = false;

        if (data.success) {
            showSuccessMessage('Settings updated successfully!');
            this.reset(); // Clear the form
        } else {
            // Display specific error messages from server
            displayErrors(data.errors || ['Failed to update settings. Please try again.'], 'settings-errors');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayErrors(['An error occurred while updating settings. Please try again later.'], 'settings-errors');
        loadingMessage.style.display = 'none';
        submitButton.disabled = false;
    });
});

// Smooth scroll functionality for navigation links
document.querySelectorAll('nav ul li a').forEach(function (link) {
    link.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        let targetSection = document.querySelector(this.getAttribute('href')); // Target section to scroll to

        // Scroll smoothly to the target section
        targetSection.scrollIntoView({ behavior: 'smooth' });

        // Optionally highlight the active section
        document.querySelectorAll('section').forEach(function (section) {
            section.style.backgroundColor = '#FFFFFF'; // Reset background color for all sections
        });
        targetSection.style.backgroundColor = '#F2E5BF'; // Highlight the active section
    });
});
