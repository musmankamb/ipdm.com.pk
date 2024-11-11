
// Show spinner with full page overlay
function showSpinner() {
    document.getElementById('spinner-overlay').style.display = 'block';  // Show spinner overlay
}

// Hide spinner after loading
function hideSpinner() {
    document.getElementById('spinner-overlay').style.display = 'none';  // Hide spinner overlay
}
(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').css('top', '0px');
        } else {
            $('.sticky-top').css('top', '-100px');
        }
    });
    
    
    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";

    // Optional: Ensure hover works smoothly if necessary
document.querySelectorAll('.dropdown-submenu > a').forEach(submenu => {
    submenu.addEventListener('mouseover', function() {
        this.nextElementSibling.style.display = 'block';
    });
    submenu.addEventListener('mouseout', function() {
        this.nextElementSibling.style.display = 'none';
    });
});

    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

})(jQuery);

document.addEventListener('DOMContentLoaded', function () {
    // Fetch gallery data from server using AJAX
    showSpinner();
    fetch('fetch_gallery.php')
        .then(response => response.json())
        .then(data => {
            const galleryContainer = document.getElementById('gallery');
            let galleryHtml = '';
            data.forEach(item => {
                const correctFullImagePath = item.full_image_path.replace('../../', '../');
                const correctThumbImagePath = item.thumbnail_path.replace('../../', '../');

                galleryHtml += `
                    <div class="col-lg-3 col-md-6 gallery-item" data-title="${item.title}">
                        <img src="${correctFullImagePath}" class="img-fluid gallery-img" alt="${item.title}"
                            data-bs-toggle="modal" data-bs-target="#imageModal" data-image="${correctFullImagePath}">
                    </div>
                `;
            });

            galleryContainer.innerHTML = galleryHtml;

            // Add event listeners to gallery images for modal functionality
            const galleryItems = document.querySelectorAll('.gallery-item img');
            const modalImage = document.getElementById('modalImage');

            galleryItems.forEach(item => {
                item.addEventListener('click', function () {
                    const imgSrc = this.getAttribute('data-image');
                    modalImage.src = imgSrc;
                });
            });
        hideSpinner();

        })
        .catch(error => console.error('Error fetching gallery data:', error));
});


$(document).ready(function() {
$('#contactForm').on('submit', function(event) {
    event.preventDefault();

    // Clear any previous errors
    $('.invalid-feedback').text('');
    $('#responseMessage').text('');

    $.ajax({
        url: '../contact_us.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                $('#contactForm')[0].reset();
            } else {
                // Display errors
                if (response.errors.name) {
                    $('#nameError').text(response.errors.name);
                }
                if (response.errors.email) {
                    $('#emailError').text(response.errors.email);
                }
                if (response.errors.contact) {
                    $('#contactError').text(response.errors.contact);
                }
                if (response.errors.message) {
                    $('#messageError').text(response.errors.message);
                }
                if (response.errors.database) {
                    $('#responseMessage').html('<div class="alert alert-danger">' + response.errors.database + '</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#responseMessage').html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
        }
    });
});
});


$(document).ready(function() {

$('#submitNewsletterBtn').on('click', function() {
    var email = $('#emailInput').val().trim();  // Trim whitespace around the email

    // Basic email validation
    if (email === '' || !validateEmail(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // AJAX request to send email to the server
    $.ajax({
        url: '../subscribe.php',
        type: 'POST',
        data: { email: email },
        success: function(response) {
            alert(response); // Display success or error message from the server
            $('#emailInput').val(''); // Clear the input field on success
        },
        error: function() {
            alert('There was an error processing your request. Please try again.');
        }
    });
});

// Improved email validation function
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}

});



$(document).ready(function() {
        let otpVerified = false; // Flag to track OTP verification
    
        // Send OTP
        $('#sendOtpBtn').on('click', function() {
            const email = $('#sEmail').val();
    
            if (email) {
                $.ajax({
                    url: '../registration/send_otp.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ email: email }),
                    success: function(response) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.status === 'success') {
                            $('#otpSection').show();
                            $('#FeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
                        } else {
                            $('#FeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#FeedbackMessage').html('<div class="alert alert-danger">Error occurred while sending the OTP.</div>');
                    }
                });
            } else {
                $('#FeedbackMessage').html('<div class="alert alert-warning">Please enter a valid email address.</div>');
            }
        });
    
        // Verify OTP
        $('#verifyOtpBtn').on('click', function() {
            const otp = $('#otpInput').val();
            const email = $('#sEmail').val();
    
            if (otp) {
                $.ajax({
                    url: '../registration/verify_otp.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ otp: otp, email: email }),
                    success: function(response) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            otpVerified = true; // Set the flag to true when OTP is verified
                            $('#FeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
                        } else {
                            $('#FeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#FeedbackMessage').html('<div class="alert alert-danger">Error occurred while verifying the OTP.</div>');
                    }
                });
            } else {
                $('#FeedbackMessage').html('<div class="alert alert-warning">Please enter the OTP.</div>');
            }
        });
    
        // Fetch courses when the modal is shown
        $('#registerModal').on('show.bs.modal', function () {
            $.ajax({
                url: '../fetch_courses.php', // Adjust the URL as needed
                method: 'GET',
                success: function(data) {
                    // Initialize course options with a default option
                    let courseOptions = '<option selected disabled>Select a course</option>';
    
                    // Loop through each course and append it as an option
                    data.forEach(course => {
                        courseOptions += `
                            <option value="${course.id}" 
                                    data-description="${course.description}" 
                                    data-price="${course.price}" 
                                    data-instructor="${course.iname}" 
                                    data-schedule="${course.schedule}">
                                ${course.title}
                            </option>`;
                    });
    
                    // Populate the select element with course options
                    $('#courseSelect').html(courseOptions);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        });
    
        // Show course details when a course is selected
        $('#courseSelect').on('change', function() {
            const selectedCourse = $(this).find(':selected');
            $('#courseDescription').text(selectedCourse.data('description'));
            $('#coursePrice').text(selectedCourse.data('price'));
            $('#courseInstructor').text(selectedCourse.data('instructor'));
            $('#courseSchedule').text(selectedCourse.data('schedule'));
            $('#courseDetails').show();
        });
    
        // Form submission
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            $('#FeedbackMessage').text('');
    
            const CourseId = $('#courseSelect').val();
            const StudentName = $('#sName').val();
            const StudentContact = $('#sContact').val();
            const StudentEmail = $('#sEmail').val();
    
            if (otpVerified) {
                $.ajax({
                    url: '../registration/registration_save.php',
                    method: 'POST',
                    data: {
                        course_id: CourseId,
                        Student_name: StudentName,
                        Scontact: StudentContact,
                        Semail: StudentEmail
                    },
                    success: function(response) {
                        let jsonResponse = JSON.parse(response);
    
                        if (jsonResponse.status === 'success') {
                            $('#FeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
                        } else if (jsonResponse.status === 'error') {
                            $('#FeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#FeedbackMessage').html('<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>');
                    }
                });
            } else {
                $('#FeedbackMessage').html('<div class="alert alert-danger">Please verify the OTP before submitting the form.</div>');
            }
        });
    });
    