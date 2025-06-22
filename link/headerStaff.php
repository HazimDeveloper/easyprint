<?php

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Staff') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

$userQuery = "SELECT * FROM user WHERE role = 'Staff' AND userID = " . $_SESSION['userID'];
$userResult = mysqli_query($conn, $userQuery);

if (!$userResult) {
    die("Error fetching user data: " . mysqli_error($conn));
}

$user['username'] = '';
$user['email'] = '';
$user['contactNum'] = '';

// Fetch user data from the database
if (mysqli_num_rows($userResult) > 0) {
    while ($row = mysqli_fetch_assoc($userResult)) {
        // Check if the role is 'admin'
        $user['username'] = $row['username'];
        $user['email'] = $row['email'];
        $user['contactNum'] = $row['contactNum'];
    }
} else {
}
?>

<div class="main ">
    <div class="top z-3">
        <i id="btn" class="material-symbols-outlined">menu</i>

        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle d-flex align-items-center" style="background-color: #ffffff; color:  #505b64; border: none;" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="fw-bolder" style="padding-right: 5px;">
                    <?php echo $user['username']; ?>
                </div>
                <i id="btn" class="material-symbols-outlined">account_circle</i>
            </button>
            <ul class="dropdown-menu ">
                <li><a class="dropdown-item" style="font-size: 14px;" href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" style="font-size: 14px;" href="login.php">Sign Out</a></li>
            </ul>
        </div>
    </div>


    <!-- Profile Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Administrator</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                    <p><strong>Contact Number:</strong> <?php echo $user['contactNum']; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary editbtn" data-bs-toggle="modal" data-bs-target="#editProfileModal<?php echo $_SESSION['userID'] ?>">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal<?php echo $_SESSION['userID'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Administrator</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../links/Edit_Administrator.php" method="POST">


                    <div class="modal-body">
                        <input type="hidden" id="userID" name="userID" value="<?php echo $_SESSION['userID'] ?>">

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username'] ?>" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="contactNum" class="form-label">Contact Num</label>
                            <input type="text" class="form-control" id="contactNum" value="<?php echo $user['contactNum'] ?>" name="contactNum" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateAdmin">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.editbtn').on('click', function() {

                $('#editProfile').modal('show');
                $('#userID').val(data[0]);
                $('#username').val(data[1]);
                $('#email').val(data[2]);
                $('#contactNum').val(data[3]);
            });
        });
    </script>