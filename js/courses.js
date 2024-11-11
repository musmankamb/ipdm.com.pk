 // Show spinner with full page overlay
function showSpinner() {
    document.getElementById('spinner-overlay').style.display = 'block';  // Show spinner overlay
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
    let courses = [];
    let currentPage = 1;
    const coursesPerPage = 10; // Limit results per page

    // Fetch and Render Courses with Filters
    // Fetch and Render Courses with Filters
function fetchAndRenderCourses() {
    showSpinner();  // Show spinner while fetching data
    
    // Apply a transition effect before starting re-render
    const coursesContainer = document.getElementById('courses-container');
    coursesContainer.style.transition = 'opacity 0.5s';
    coursesContainer.style.opacity = 0; // Start fade-out

    const selectedCategory = document.getElementById('filterCategory').value;
    const selectedInstructor = document.getElementById('filterInstructor').value;
    const searchQuery = document.getElementById('searchCourse').value.toLowerCase();
    const sortDirection = document.getElementById('sortPrice').value;

    let url = `data_fetcher.php?action=courses&page=${currentPage}&limit=${coursesPerPage}`;

    // Append filters to URL if present
    if (selectedCategory) {
        url += `&category_id=${selectedCategory}`;
    }
    if (selectedInstructor) {
        url += `&instructor_name=${encodeURIComponent(selectedInstructor)}`;
    }
    if (searchQuery) {
        url += `&search_query=${encodeURIComponent(searchQuery)}`;
    }
    if (sortDirection) {
        url += `&sort_price=${sortDirection}`;
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch courses');
            }
            return response.json();
        })
        .then(data => {
            courses = data;

            // After fetching, delay the fade-in effect until courses are rendered
            setTimeout(() => {
                renderCourses(courses); // Render fetched courses
                renderPagination();     // Render pagination controls
                hideSpinner();          // Hide the spinner after rendering
                coursesContainer.style.opacity = 1; // Fade-in the new content
            }, 200);  // Adjust the delay as needed
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
            hideSpinner();          // Hide spinner in case of error
            coursesContainer.style.opacity = 1; // Ensure visibility even in case of error
        });
}

    // Render the Courses in HTML
    function renderCourses(coursesToRender) {
        const coursesContainer = document.getElementById('courses-container');
        coursesContainer.innerHTML = '';

        if (coursesToRender.length === 0) {
            coursesContainer.innerHTML = '<p>No courses found.</p>';
            return;
        }

        coursesToRender.forEach(course => {
            const scheduleDate = new Date(course.schedule);
            const formattedDate = scheduleDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
            const courseHTML = `
               <div class="swiper-slide wow fadeInUp">
                   <div class="course-item">
                       <!-- Image Section -->
                       <div class="course-image">
                           <img src="../${course.image}" alt="${course.title}">
                       </div>

                       <!-- Course Details -->
                       <div class="course-details">
                           <h4>${course.title}</h4>
                           <p>${course.description}</p>
                           <div class="price-circle">PKR ${course.price}</div>
                       </div>

                       <!-- Meta Information (Instructor & Schedule) -->
                       <div class="course-meta">
                           <small><i class="fa fa-user-tie"></i>${course.instructor_name}</small>
                           <small><i class="fa fa-clock"></i>${formattedDate}</small>
                       </div>

                       <!-- Action Buttons (Read More & Register) -->
                       <div class="btn-container">
                           <a href="#" class="btn read-more-btn" data-title="${course.title}" data-bs-toggle="tooltip" title="Learn more about this course!">Read More</a>
                           <a href="#" class="btn register-btn" data-title="${course.title}">Register Now</a>
                       </div>
                   </div>
               </div>
            `;
            coursesContainer.innerHTML += courseHTML;
        });

        // Re-attach event listeners for "Read More" and "Register Now" buttons after rendering
        attachReadMoreButtons();
        attachJoinNowButtons();
    }





    // Attach event listeners for "Read More" buttons
    function attachReadMoreButtons() {
        document.querySelectorAll('.read-more-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const courseTitle = this.dataset.title;
                const course = courses.find(c => c.title === courseTitle);

                // Inject detailed course data into modal
                const modalContent = `
                    <div class="course-modal-header text-center mb-4">
                        <h4 class="course-modal-title">${course.title}</h4>
                        <p class="course-modal-instructor">By: ${course.instructor_name}</p>
                    </div>
                    <div class="course-modal-body">
                        <p><strong>Description:</strong> ${course.description}</p>
                        <p><strong>Category:</strong> ${course.cname}</p>
                        <p><strong>Price:</strong> PKR ${course.price}</p>
                        <p><strong>Schedule:</strong> ${course.schedule}</p>
                        <p><strong>Instructor Bio:</strong> ${course.instructor_bio}</p>
                    </div>
                    ${course.video_url ? `<div class="course-modal-video mb-4">
                        <h5>Course Overview Video</h5>
                        <iframe width="100%" height="315" src="../${course.video_url}" 
                            title="Course Video" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>` : ''}
                    ${course.extra_images ? `<div class="course-modal-images mb-4">
                        <h5>Course Images</h5>
                        ${course.extra_images.map(img => `<img src="../${img}" class="img-fluid mb-3" alt="${course.title}">`).join('')}
                    </div>` : ''}
                `;
                document.getElementById('course-modal-content').innerHTML = modalContent;

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('courseDetailModal'));
                modal.show();
            });
        });
    }

    // Attach event listeners for "Register Now" buttons
    function attachJoinNowButtons() {
        document.querySelectorAll('.register-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const courseTitle = this.getAttribute('data-title');
                document.getElementById('joinNowModalLabel').innerText = `Join Course: ${courseTitle}`;
                document.getElementById('courseName').value = courseTitle;
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
        $.ajax({
            url: '../registration/send_otp.php',
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
            },
            error: function() {
                $('#OtpFeedbackMessage').html('<div class="alert alert-danger">Error occurred while sending the OTP.</div>');
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
        $.ajax({
            url: '../registration/verify_otp.php',
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
            },
            error: function() {
                $('#OtpFeedbackMessage').html('<div class="alert alert-danger">Error occurred while verifying the OTP.</div>');
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
    
    // Send data to the PHP backend for saving in MySQL
    fetch('../registration/registration.php', {
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
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    });
    
    });

    // Render Pagination Controls
    function renderPagination() {
        const paginationContainer = document.getElementById('pagination-container');
        paginationContainer.innerHTML = '';

        const totalCourses = courses.length;
        const totalPages = Math.ceil(totalCourses / coursesPerPage);

        // Create "Previous" button
        const prevItem = document.createElement('li');
        prevItem.classList.add('page-item');
        if (currentPage === 1) prevItem.classList.add('disabled');
        const prevLink = document.createElement('a');
        prevLink.classList.add('page-link');
        prevLink.textContent = 'Previous';
        prevLink.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                fetchAndRenderCourses();
            }
        });
        prevItem.appendChild(prevLink);
        paginationContainer.appendChild(prevItem);

        // Create numbered page buttons
        for (let page = 1; page <= totalPages; page++) {
            const pageItem = document.createElement('li');
            pageItem.classList.add('page-item');
            if (page === currentPage) pageItem.classList.add('active');

            const pageLink = document.createElement('a');
            pageLink.classList.add('page-link');
            pageLink.textContent = page;
            pageLink.addEventListener('click', function() {
                currentPage = page;
                fetchAndRenderCourses();
            });

            pageItem.appendChild(pageLink);
            paginationContainer.appendChild(pageItem);
        }

        // Create "Next" button
        const nextItem = document.createElement('li');
        nextItem.classList.add('page-item');
        if (currentPage === totalPages) nextItem.classList.add('disabled');
        const nextLink = document.createElement('a');
        nextLink.classList.add('page-link');
        nextLink.textContent = 'Next';
        nextLink.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                fetchAndRenderCourses();
            }
        });
        nextItem.appendChild(nextLink);
        paginationContainer.appendChild(nextItem);
    }

    // Fetch and Populate Categories
    fetch('data_fetcher.php?action=categories')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch categories');
            }
            return response.json();
        })
        .then(categories => {
            const categorySelect = document.getElementById('filterCategory');
            categorySelect.innerHTML = '<option value="">All Categories</option>'; // Default option

            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;  // Use category ID for filtering
                option.textContent = `${category.name} (${category.course_count})`; // Display category name and count
                categorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching categories:', error);
        });

    // Fetch and Populate Instructors
    fetch('data_fetcher.php?action=instructors')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch instructors');
            }
            return response.json();
        })
        .then(instructors => {
            const instructorSelect = document.getElementById('filterInstructor');
            instructorSelect.innerHTML = '<option value="">All Instructors</option>'; // Default option

            instructors.forEach(instructor => {
                const option = document.createElement('option');
                option.value = instructor.name;  // Use instructor name for filtering
                option.textContent = instructor.name; // Display instructor name
                instructorSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching instructors:', error);
        });

    // Initial data fetch and rendering
    fetchAndRenderCourses();

    // Event listeners for filtering
    document.getElementById('filterCategory').addEventListener('change', fetchAndRenderCourses);
    document.getElementById('filterInstructor').addEventListener('change', fetchAndRenderCourses);
    document.getElementById('sortPrice').addEventListener('change', fetchAndRenderCourses);
    document.getElementById('searchCourse').addEventListener('input', fetchAndRenderCourses);
    
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
fetch('../get_courses.php')
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
       url: '../registration/send_otp.php',
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
       url: '../registration/verify_otp.php',
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
fetch('../registration/registration.php', {
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
        const registerButton = $(this).find('button[type="submit"]');  // Reference to the Register button

        const CourseId = $('#courseSelect').val();
        const StudentName = $('#sName').val();
        const StudentContact = $('#sContact').val();
        const StudentEmail = $('#sEmail').val();

        if (otpVerified) {
            showSpinner(registerButton);  // Show spinner
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