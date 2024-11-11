
 // Show spinner with full page overlay
 function showSpinner() {
    document.getElementById('spinner-overlay').style.display = 'flex';  // Show spinner overlay
    
}

// Hide spinner after loading
function hideSpinner() {
    document.getElementById('spinner-overlay').style.display = 'none';  // Hide spinner overlay
}


document.addEventListener('DOMContentLoaded', function() {
    hideSpinner();  // Ensure spinner is hidden when the DOM is fully loaded
});


(function ($) {
    "use strict";
    
    
    // Initiate the wowjs
    new WOW().init();


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


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });

})(jQuery);






document.addEventListener('DOMContentLoaded', function() {

     // Utility function to show spinner and disable the button
     function showSpinner(button) {
        button.querySelector('.button-spinner').style.display = 'inline-block'; // Show spinner
        button.disabled = true; // Disable the button
    }

    // Utility function to hide spinner and enable the button
    function hideSpinner(button) {
        button.querySelector('.button-spinner').style.display = 'none'; // Hide spinner
        button.disabled = false; // Re-enable the button
    }

// Step 1: Fetch courses from the PHP API
fetch('get_courses.php')
.then(response => response.json())
.then(data => {
    const coursesContainer = document.getElementById('courses-container');

    // Clear container and iterate over courses to generate HTML
    coursesContainer.innerHTML = '';
    data.forEach((course, index) => {
const scheduleDate = new Date(course.schedule);
const formattedDate = scheduleDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
const courseHTML = `
    <div class="swiper-slide">
        <div class="course-item bg-light">
            <div class="position-relative overflow-hidden">
                <img class="img-fluid" src="${course.image}" alt="${course.title}">
                <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                    <a href="#" class="flex-shrink-0 btn btn-sm px-3 border-end read-more-btn" style="border-radius: 30px 0 0 30px;">Read More</a>
                    <a href="#" class="flex-shrink-0 btn btn-sm px-3 join-now-button register-btn" data-title="${course.title}" style="border-radius: 0 30px 30px 0;">Join Now</a>
                </div>
            </div>
            <div class="text-center p-4 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-4 cname-left">${course.cname}</h6>
                    <h5 class="mb-0 price-circle">PKR ${course.price}</h5>
                </div>
                <h4 class="mb-4">${course.title}</h4>
                <p>${course.description}</p>
            </div>
            <div class="d-flex border-top">
                <small class="flex-fill text-center border-end py-2">
                    <i class="fa fa-user-tie text-primary me-2"></i>${course.instructor_name}
                </small>
                <small class="flex-fill text-center py-2">
                    <i class="fa fa-clock text-primary me-2"></i>${formattedDate}
                </small>
            </div>
        </div>
    </div>
`;

coursesContainer.innerHTML += courseHTML;
});


    // Attach event listeners to dynamically generated buttons
    attachJoinNowButtons();
})
.catch(error => console.error('Error fetching courses:', error));

// Step 2: Attach event listeners for "Join Now" buttons
function attachJoinNowButtons() {
document.querySelectorAll('.join-now-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        const courseTitle = this.getAttribute('data-title');

        // Set the modal title dynamically
        document.getElementById('joinNowModalLabel').innerText = `Join Course: ${courseTitle}`;
        document.getElementById('courseName').value = courseTitle;  // Store the course title for form submission

        const joinNowModal = new bootstrap.Modal(document.getElementById('joinNowModal'));
        joinNowModal.show();
    });
});
}

// Step 3: Handle OTP sending
const sendOtpButton = document.getElementById('sendOtpButton');
const verifyOtpButton = document.getElementById('verifyOtpButton');
const joinForm = document.getElementById('joinForm');
const otpForm = document.getElementById('otpForm');
const paymentAccordion = document.getElementById('paymentAccordion'); // Payment accordion

sendOtpButton.addEventListener('click', function() {
const email = document.getElementById('email').value;
const fullName = document.getElementById('studentName').value;
const contactNumber = document.getElementById('contactNumber').value;

// Perform client-side validation
if (!email || !fullName || !contactNumber) {
    alert('Please fill out all fields.');
    return;
}

if (email) {
    showSpinner(sendOtpButton); // Show spinner and disable the Send OTP button
    $.ajax({
        url: 'registration/send_otp.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ email: email }),
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                joinForm.style.display = 'none';
                otpForm.style.display = 'block';

                $('#OtpFeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
            } else {
                $('#OtpFeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
            }
            hideSpinner(sendOtpButton);
        },
        error: function() {
            $('#OtpFeedbackMessage').html('<div class="alert alert-danger">Error occurred while sending the OTP.</div>');
            hideSpinner(sendOtpButton);
        }
    });
} else {
    $('#OtpFeedbackMessage').html('<div class="alert alert-warning">Please enter a valid email address.</div>');
}


});

// Step 4: Handle OTP verification
verifyOtpButton.addEventListener('click', function() {
const otp = document.getElementById('otp').value;
const email = document.getElementById('email').value;
// Perform client-side validation
if (!otp) {
    alert('Please enter the OTP.');
    return;
}

if (otp) {
    showSpinner(verifyOtpButton); 
    $.ajax({
        url: 'registration/verify_otp.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ otp: otp, email: email }),
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.success) {
                $('#OtpFeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
                otpForm.style.display = 'none';
                paymentAccordion.style.display = 'block';  // Make the accordion visible
            } else {
                $('#OtpFeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
            }
            hideSpinner(verifyOtpButton);
        },
        error: function() {
            $('#OtpFeedbackMessage').html('<div class="alert alert-danger">Error occurred while verifying the OTP.</div>');
            hideSpinner(verifyOtpButton);
        }
    });
} else {
    $('#OtpFeedbackMessage').html('<div class="alert alert-warning">Please enter the OTP.</div>');
}

});



// Step 5: Handle voucher submission
const submitVoucherButton = document.getElementById('submitLater');

submitVoucherButton.addEventListener('click', function() {
const courseName = document.getElementById('courseName').value;
const studentName = document.getElementById('studentName').value;
const contactNumber = document.getElementById('contactNumber').value;
const email = document.getElementById('email').value;
const paymentMethod = 1; // 1 for Voucher

// Ensure all fields are filled out
if (!courseName || !studentName || !email) {
    alert('Please fill out all fields.');
    return;
}


showSpinner(submitVoucherButton); 

// Send data to the PHP backend for saving in MySQL
fetch('registration/registration.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        course_name: courseName,
        student_name: studentName,
        email: email,
        contact: contactNumber,
        payment_method: paymentMethod,
        transaction_id: null // No transaction ID for voucher payments
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {

        hideSpinner(submitVoucherButton);
        // Populate modal with registration details
        const currentDate = new Date().toLocaleDateString();
        document.getElementById('receiptStudentName').innerText = studentName;
        document.getElementById('receiptCourseName').innerText = courseName;
        document.getElementById('receiptEmail').innerText = email;
        document.getElementById('receiptContact').innerText = contactNumber;
        document.getElementById('receiptVoucherNumber').innerText = data.voucher_number || 'N/A';
        document.getElementById('receiptDate').innerText = currentDate;

        // Show the modal
        const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        receiptModal.show();

        // Handle Print Receipt
        document.getElementById('printReceipt').addEventListener('click', function() {
            window.print();  // Open print dialog with the receipt modal content only
        });
        

        // Handle Download PDF
        document.getElementById('downloadReceipt').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
        
            // Set custom PDF size (325pt width x 400pt height)
            const doc = new jsPDF({
                unit: 'pt',   // Unit is set to points
                format: [325, 400]  // Custom dimensions: 325pt width, 400pt height
            });
        
            // Fetch the logo image from the HTML
            const logoImage = document.getElementById('logo');
        
            // Create a canvas to convert image to base64
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
        
            // Set canvas dimensions to match the logo image
            canvas.width = logoImage.width;
            canvas.height = logoImage.height;
        
            // Draw the image onto the canvas
            context.drawImage(logoImage, 0, 0, canvas.width, canvas.height);
        
            // Convert the canvas to a base64-encoded string
            const logoBase64 = canvas.toDataURL('image/png');
        
            // Calculate horizontal center position for the logo and text (based on PDF width)
            const centerX = doc.internal.pageSize.getWidth() / 2;
        
            // Add the logo to the PDF (centered)
            const imgWidth = 180;  // Desired width of the image in the PDF
            const imgHeight = 60;  // Desired height of the image in the PDF
            doc.addImage(logoBase64, 'PNG', centerX - imgWidth / 2, 10, imgWidth, imgHeight); // Centered logo
        
            // Heading - Provisional Registration Receipt (centered)
            doc.setFontSize(18);
            doc.setTextColor(40, 44, 52);  // Dark color for the heading
            doc.setFont('helvetica', 'bold');  // Bold font for the heading
            doc.text("Provisional Registration Receipt", centerX, imgHeight + 30, { align: 'center' }); // Centered heading
        
            // Add date (centered)
           // const currentDate = new Date().toLocaleDateString();
            doc.setFontSize(12);
            doc.setFont('helvetica', 'normal');
            doc.text(`Date: ${currentDate}`, centerX, imgHeight + 50, { align: 'center' });
        
            // Subheading (Student and Course Information) with proper vertical spacing
            doc.setFontSize(14);
            doc.setTextColor(40, 44, 52);
            doc.setFont('helvetica', 'bold');
            doc.text("Student Information", 10, imgHeight + 80); // Left-aligned subheading
        
            // Normal text styling for content with proper vertical spacing
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);  // Black for regular text
            doc.setFont('helvetica', 'normal');
        
            // Add student and course details with better vertical spacing
            const startY = imgHeight + 100;
            const lineHeight = 15; // Line height for better spacing
        
            doc.text(`Student Name: ${studentName}`, 10, startY);
            doc.text(`Course Name: ${courseName}`, 10, startY + lineHeight);
            doc.text(`Email: ${email}`, 10, startY + 2 * lineHeight);
            doc.text(`Contact: ${contactNumber}`, 10, startY + 3 * lineHeight);
            doc.text(`Voucher Number: ${data.voucher_number || 'N/A'}`, 10, startY + 4 * lineHeight);
        
            // Footer message with proper vertical spacing
            doc.setFontSize(10);
            doc.setTextColor(100);  // Gray color for footer text
            doc.text("Thank you for your registration!", 10, startY + 6 * lineHeight);
        
            // Save the PDF with the custom size
            doc.save(`registration_receipt_${studentName}.pdf`);
        });
        

    } else {
        alert('Error during registration. Please try again.');
        hideSpinner(submitVoucherButton);
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again later.');
    hideSpinner(submitVoucherButton);
});

});
});


var swiper = new Swiper('.swiper-container', {
slidesPerView: 1,
spaceBetween: 20,
loop: true,
autoplay: {
    delay: 5000,
    disableOnInteraction: false,
},
pagination: {
    el: '.swiper-pagination',
    clickable: true,  // Make pagination buttons clickable
},
navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
},
breakpoints: {
    640: {
        slidesPerView: 1,
        spaceBetween: 20,
    },
    768: {
        slidesPerView: 2,
        spaceBetween: 40,
    },
    1024: {
        slidesPerView: 3,
        spaceBetween: 50,
    },
}
});

document.addEventListener('DOMContentLoaded', function() {
// Fetch the instructors from the PHP API
fetch('get_instructors.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const teamContainer = document.querySelector('.Getinstructors'); // Correct class selector

        // Clear the container
        teamContainer.innerHTML = '';

        // Iterate over instructors and generate HTML
        data.forEach((instructor, index) => {
            // Split the social links by semicolon
            const socialLinks = instructor.social.split(';');
            
// Create a map to link domains with relevant icons
const socialIcons = {
'facebook.com': 'fab fa-facebook-f',
'instagram.com': 'fab fa-instagram',
'youtube.com': 'fab fa-youtube',
'tiktok.com': 'fa-brands fa-tiktok',
'linkedin.com': 'fa-brands fa-linkedin',
// Add more social platforms if needed
};

// Function to ensure proper URL format
function ensureValidURL(url) {
// Add https:// if the URL doesn't already start with http:// or https://
if (!/^https?:\/\//i.test(url)) {
    return 'https://' + url;
}
return url;
}

// Generate social media icons with links
let socialHTML = '';
socialLinks.forEach(link => {
const formattedLink = ensureValidURL(link);  // Ensure the URL is properly formatted
const domainMatch = link.match(/(?:www\.)?([a-zA-Z0-9-]+\.[a-zA-Z]+)/i); // Extract the domain

if (domainMatch) {
    const domain = domainMatch[1];  // Get the domain part of the URL
    const iconClass = socialIcons[domain] || 'fas fa-link';  // Get the icon class or use a default icon

    // Generate the social media link HTML
    socialHTML += `<a class="btn btn-sm-square mx-1" href="${formattedLink}" target="_blank"><i class="${iconClass}"></i></a>`;
}
});


            // Generate the instructor card HTML
            const instructorHTML = `
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="${0.1 * (index + 1)}s">
                    <div class="team-item">
                        <div class="overflow-hidden">
                            <img class="img-fluid" src="${instructor.image}" alt="${instructor.name}">
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="instructor-social d-flex justify-content-center pt-2 px-1">
                                ${socialHTML} <!-- Insert the dynamically generated social icons here -->
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">${instructor.name}</h5>
                            <small>${instructor.bio}</small>
                        </div>
                    </div>
                </div>
            `;

            // Append the generated HTML to the container
            teamContainer.innerHTML += instructorHTML;
        });
    })
    .catch(error => console.error('Error fetching instructors:', error));
});

document.addEventListener('DOMContentLoaded', function() {
// Fetch the testimonials from the PHP API
fetch('get_testimonials.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const carouselContainer = document.querySelector('.testimonial-carousel'); // Class selector for the carousel

        // Clear the container
        carouselContainer.innerHTML = '';

        // Iterate over testimonials and generate HTML
        data.forEach((testimonial, index) => {
            const testimonialHTML = `
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="${testimonial.image}" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">${testimonial.name}</h5>
                    <p>${testimonial.profession}</p>
                    <div class="testimonial-text text-center p-4">
                        <p class="mb-0">${testimonial.testimonial_text}</p>
                    </div>
                </div>
            `;

            // Append the generated HTML to the container
            carouselContainer.innerHTML += testimonialHTML;
        });

        // Reinitialize the Owl Carousel after content is added
        $(".testimonial-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            center: true,
            margin: 24,
            dots: true,
            loop: true,
            nav: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    })
    .catch(error => console.error('Error fetching testimonials:', error));
});


// Function to dynamically create event HTML
function formatTime(timeString) {
const [hours, minutes] = timeString.split(':').map(Number);
const period = hours >= 12 ? 'PM' : 'AM';
const formattedHours = hours % 12 || 12; // Convert to 12-hour format
return `${formattedHours}:${minutes.toString().padStart(2, '0')} ${period}`;
}

function formatDateRange(startDate, endDate) {
const options = { month: 'long' }; // Extract full month name
const startDay = startDate.getDate();
const startMonth = new Intl.DateTimeFormat('en-US', options).format(startDate).slice(0, 3); // Get first 3 letters of month

const endDay = endDate.getDate();
const endMonth = new Intl.DateTimeFormat('en-US', options).format(endDate).slice(0, 3); // Get first 3 letters of month

// Check if the start and end months are the same
if (startMonth === endMonth) {
    return `${startDay}-${endDay}<br><small>${startMonth}</small>`;
} else {
    return `${startDay} <small>${startMonth}</small> - ${endDay} <small>${endMonth}</small>`;
}
}

function createEventHTML(event, index) {
// Add 'flipped' class to every second event (alternating layout)
const alignmentClass = (index % 2 !== 0) ? 'flipped' : '';

// If it's flipped, the date will be placed on the left, otherwise on the right.
const datePositionClass = (index % 2 !== 0) ? 'date-left' : 'date-right';

// Format time with AM/PM
const formattedTime = `${formatTime(event.start_time)} - ${formatTime(event.end_time)}`;

// Format date to be "10-12 Sep" with smaller month size
const startDate = new Date(event.start_date);
const endDate = new Date(event.end_date);
const formattedDate = formatDateRange(startDate, endDate);

// Return the HTML template
return `
<div class="col-sm-12 events_full_box">
    <div class="events_single ${alignmentClass}">
        <div class="event_banner">
            <a href="#"><img src="img/events/${event.image}" alt="${event.title}" class="img-fluid"></a>
        </div>
        <div class="event_info">
            <h3><a href="#" title="${event.title}">${event.title}</a></h3>
            <div class="events_time">
                <span class="time"><i class="flaticon-clock-circular-outline"></i>${formattedTime}</span>
                <span><i class="fas fa-map-marker-alt"></i>${event.location}</span>
            </div>
            <p>${event.description}</p>
        </div>
        <div class="event_date ${datePositionClass}">
            <span class="date">${formattedDate}</span>
        </div>
    </div>  
</div>`;
}



// Fetch events from the backend
function loadEvents() {
fetch('get_events.php')
.then(response => response.json())
.then(events => {
    const eventsList = document.getElementById('events-list');
    eventsList.innerHTML = ''; // Clear previous content
    events.forEach((event, index) => {
        eventsList.innerHTML += createEventHTML(event, index);
    });
})
.catch(error => console.error('Error fetching events:', error));
}

// Load events on page load
window.onload = loadEvents;



$(document).ready(function() {
$('#contactForm').on('submit', function(event) {
    event.preventDefault();

    // Clear any previous errors
    $('.invalid-feedback').text('');
    $('#responseMessage').text('');

    $.ajax({
        url: 'contact_us.php',
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
    let otpVerified = false; // Flag to track OTP verification

    // Utility function to show spinner and disable the button
    function showSpinner(button) {
        button.querySelector('.button-spinner').style.display = 'inline-block'; // Show spinner
        button.disabled = true; // Disable the button
    }

    // Utility function to hide spinner and enable the button
    function hideSpinner(button) {
        button.querySelector('.button-spinner').style.display = 'none'; // Hide spinner
        button.disabled = false; // Re-enable the button
    }

    // Send OTP
    $('#sendOtpBtn').on('click', function() {
        const email = $('#sEmail').val();
        const sendOtpButton = $(this);  // Reference to the Send OTP button

        if (email) {
            showSpinner(sendOtpButton);  // Show spinner
            $.ajax({
                url: 'registration/send_otp.php',
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
                    hideSpinner(sendOtpButton);  // Hide spinner after response
                },
                error: function() {
                    $('#FeedbackMessage').html('<div class="alert alert-danger">Error occurred while sending the OTP.</div>');
                    hideSpinner(sendOtpButton);  // Hide spinner on error
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
        const verifyOtpButton = $(this);  // Reference to the Verify OTP button
   

        if (otp) {
            showSpinner(verifyOtpButton);  // Show spinner
            $.ajax({
                url: 'registration/verify_otp.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ otp: otp, email: email }),
                success: function(response) {
                    let jsonResponse = JSON.parse(response);
                    if (jsonResponse.success) {
                        otpVerified = true;  // Set the flag to true when OTP is verified
                        $('#FeedbackMessage').html('<div class="alert alert-success">' + jsonResponse.message + '</div>');
                        $('#registerForm button[type="submit"]').show(); // Show the Register button
                    } else {
                        $('#FeedbackMessage').html('<div class="alert alert-danger">' + jsonResponse.message + '</div>');
                    }
                    hideSpinner(verifyOtpButton);  // Hide spinner after response
                },
                error: function() {
                    $('#FeedbackMessage').html('<div class="alert alert-danger">Error occurred while verifying the OTP.</div>');
                    hideSpinner(verifyOtpButton);  // Hide spinner on error
                }
            });
        } else {
            $('#FeedbackMessage').html('<div class="alert alert-warning">Please enter the OTP.</div>');
        }
    });

    // Fetch courses when the modal is shown
    $('#registerModal').on('show.bs.modal', function () {
        $.ajax({
            url: 'fetch_courses.php', // Adjust the URL as needed
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
        const registerButton = $(this).find('button[type="submit"]');  // Reference to the Register button

        const CourseId = $('#courseSelect').val();
        const StudentName = $('#sName').val();
        const StudentContact = $('#sContact').val();
        const StudentEmail = $('#sEmail').val();

        if (otpVerified) {
            showSpinner(registerButton);  // Show spinner
            $.ajax({
                url: 'registration/registration_save.php',
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
                    hideSpinner(registerButton);  // Hide spinner after response
                },
                error: function() {
                    $('#FeedbackMessage').html('<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>');
                    hideSpinner(registerButton);  // Hide spinner on error
                }
            });
        } else {
            $('#FeedbackMessage').html('<div class="alert alert-danger">Please verify the OTP before submitting the form.</div>');
        }
    });
});




$('#submitNewsletterBtn').on('click', function() {
    var email = $('#emailInput').val().trim();  // Trim whitespace around the email

    // Basic email validation
    if (email === '' || !validateEmail(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // AJAX request to send email to the server
    $.ajax({
        url: 'subscribe.php',
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



let categoriesContainer; // Declare the variable in a higher scope

$(document).ready(function(){
    // Fetch categories from the backend
    $.getJSON('categories.php', function(data) {
        categoriesContainer = $('#categories-carousel'); // Unique ID selector

        // Iterate over categories and generate HTML for each
        $.each(data, function(index, category) {
    const categoryHTML = `
        <div class="item position-relative">
            <a class="position-relative d-block overflow-hidden" href="#">
                <img class="img-fluid" src="${category.image}" alt="${category.name}">
                <div class="text-container d-flex flex-column justify-content-center align-items-center">
                    <h5 class="m-0">${category.name}</h5>
                    <small class="count">
                        <i class="fas fa-book"></i> ${category.course_count} Courses
                    </small>
                    <button class="view-details-btn" data-id="${category.id}">View Details</button>
                </div>
            </a>
        </div>
    `;
    categoriesContainer.append(categoryHTML);
});


        // Initialize Owl Carousel after injecting categories
        categoriesContainer.owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1024: {
                    items: 3
                }
            },
            onInitialized: createCustomDots,  // Create custom lines (dots) after initialization
            onChanged: updateActiveDot  // Update active line on slide change
        });

    });


    function createCustomDots(event) {
        const totalItems = event.item.count;
        let dotsContainer = $('<div class="custom-dots"></div>').appendTo(categoriesContainer.parent());
        
        for (let i = 0; i < totalItems; i++) {
            dotsContainer.append('<div class="custom-dot"></div>');
        }
        
        // Set the first line as active
        dotsContainer.find('.custom-dot').eq(0).addClass('active');
    }

    // Update active line when the slide changes
    function updateActiveDot(event) {
        const currentIndex = event.item.index - event.relatedTarget._clones.length / 2;
        const totalItems = event.item.count;
        const currentItem = (currentIndex % totalItems + totalItems) % totalItems;  // Handle loop
        
        // Update active line
        $('.custom-dot').removeClass('active');
        $('.custom-dot').eq(currentItem).addClass('active');
    }
});

// Event listener for the View Details button
$(document).on('click', '.view-details-btn', function() {
    let categoryId = $(this).data('id');

    // Fetch category details
    $.getJSON(`category_details.php?id=${categoryId}`, function(data) {
        // Populate modal with category details
        $('#modal-category-name').text(data.category.name);
        $('#modal-course-count').text(data.category.course_count);

        let coursesTableBody = $('#modal-courses-table tbody');
        coursesTableBody.empty(); // Clear old table rows

        // Populate table with course details
        $.each(data.courses, function(index, course) {
            let row = `
                <tr>
                    <td>${course.title}</td>
                    <td>${course.description}</td>
                    <td>${course.price}</td>
                    <td>${course.schedule}</td>
                    <td>${course.batch}</td>
                </tr>
            `;
            coursesTableBody.append(row);
        });

        // Show the modal
        $('#categoryModal').css('display', 'flex');
    });
});

// Close the modal
$('.close-modal').on('click', function() {
    $('#categoryModal').hide();
});
