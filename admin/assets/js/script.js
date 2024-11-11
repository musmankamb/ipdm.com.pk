// Add the event listener to toggle the sidebar
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('collapsed');
});

// Collapse the sidebar by default on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    sidebar.classList.add('collapsed'); // Add collapsed class to sidebar
    content.classList.add('collapsed'); // Add collapsed class to content
});
$(document).ready(function() {
    // Toggle dropdown on settings icon click
    $('#settingsIcon').on('click', function() {
        $('#settingsMenu').toggleClass('active');
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.settings-dropdown').length) {
            $('#settingsMenu').removeClass('active');
        }
    });
});