<?php include '../sidebar.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Link Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

     <!-- Link Font Awesome CSS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
    
    <!-- Link your custom styles -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- For pages inside subfolders like pages/ -->
</head>
<div id="content" class="container mt-4">
    <h2>Users Management</h2>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add User</button>
    <table class="table table-bordered mt-4" id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- User data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing User -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId">
                    <div class="form-group">
                        <label for="userName">Name</label>
                        <input type="text" class="form-control" id="userName" required>
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email</label>
                        <input type="email" class="form-control" id="userEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password</label>
                        <input type="password" class="form-control" id="userPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="userRole">Role</label>
                        <select class="form-control" id="userRole" required>
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<script>
// Fetch users via AJAX
function fetchUsers() {
    $.ajax({
        url: '../ajax/users.php',
        method: 'GET',
        success: function(response) {
            $('#usersTable tbody').html(response);
        }
    });
}

// Add/Edit user via AJAX
$(document).ready(function() {
    fetchUsers();

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#userId').val();
        const name = $('#userName').val();
        const email = $('#userEmail').val();
        const password = $('#userPassword').val();  // You may hash this on the server-side
        const role = $('#userRole').val();
        
        $.ajax({
            url: '../ajax/users.php',
            method: 'POST',
            data: { id, name, email, password, role },
            success: function(response) {
                $('#addUserModal').modal('hide');
                fetchUsers();
            }
        });
    });
});
</script>
