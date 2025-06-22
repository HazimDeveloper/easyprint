<?php
session_start();

@include '../link/db_connect.php'; // Include the database connection


if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    // Redirect to login page if user is not logged in or not an Staff
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashbaord</title>
    <link rel="stylesheet" type="text/css" href="../link/style.css">
    <link rel="stylesheet" type="text/css" href="dashboardStaff.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />

</head>

<body>

    <?php include '../link/sidebarStaff.php'; ?> <!-- Include Sidebar -->
    <?php include '../link/headerStaff.php'; ?> <!-- Include Header -->

    <div class="main_content">
        <?php
        if (isset($_SESSION['status'])) :
        ?>
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <span class="material-symbols-outlined me-2">check_circle</span>
                    <?php echo $_SESSION['status']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php
            unset($_SESSION['status']);
        endif
        ?>
        <h1>
            <span>Staff Dashboard</span>
        </h1>

        <div class="row">
            <div class="col-lg-3 col-sm-12 ">
                <div class="card face">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Customers</h6>
                            <?php
                            $sql = "SELECT count(*) AS totalCustomers FROM customer";
                            $query = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_assoc($query);
                            $totalCustomers = $result['totalCustomers'];

                            echo '<h3 class="card-title"> ' . $totalCustomers . '</h3>';
                            ?>
                        </div>
                        <span class="material-symbols-outlined face">face_2</span>
                    </div>
                </div>

            </div>
            <div class="col-lg-3 col-sm-12 ">
                <div class="card work">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Staff</h6>
                            <?php
                            $sql = "SELECT count(*) AS totalStaff FROM staff";
                            $query = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_assoc($query);
                            $totalStaff = $result['totalStaff'];

                            echo '<h3 class="card-title"> ' . $totalStaff . '</h3>';
                            ?>
                        </div>

                        <span class="material-symbols-outlined work">work</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-12 ">
                <div class="card list">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Orders</h6>
                            <?php
                            $sql = "SELECT COUNT(*) AS totalOrders FROM `order`
                            WHERE `order`.orderStatus = 'Completed'";
                            $query = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_assoc($query);
                            $totalOrders = $result['totalOrders'];

                            echo '<h3 class="card-title"> ' . $totalOrders . '</h3>';

                            ?>
                        </div>

                        <span class="material-symbols-outlined list">list_alt_check</span>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-12 ">
                <div class="card payment">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Sales</h6>
                            <?php
                            $sql = "SELECT SUM(payment.amountPaid) AS totalSales 
                            FROM payment
                            JOIN invoice ON payment.paymentID = invoice.paymentID 
                            JOIN `order` ON invoice.orderID = `order`.orderID
                            WHERE `order`.orderStatus = 'Completed'";

                            $query = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_assoc($query);
                            $totalSales = $result['totalSales'];

                            echo '<h3 class="card-title"> RM ' . $totalSales . '</h3>';
                            ?>
                        </div>

                        <span class="material-symbols-outlined payment">payments</span>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-12 pt-5">
                <div>
                    <div class="text">
                        <span>Package Sales</span>
                    </div>
                    <div class="card">
                        <?php include 'chartBar_PackageSales.php'; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-12 pt-5">
                <div>
                    <div class="text">
                        <span>Daily Sales</span>
                    </div>
                    <div class="card">
                        <?php include 'chartLine_DailySales.php'; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../link/footer.php'; ?> <!-- Include Footer -->

    <!--JavaScript-->
    <script>
        let btn = document.querySelector("#btn");
        let sideBar = document.querySelector(".sideBar");

        btn.onclick = function() {
            sideBar.classList.toggle("close");
        }

        function preventBack() {
            window.history.forward();
        }
        setTimeout("preventBack()", 0);
        window.onunload = function() {
            null
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "pageLength": 6,
                "lengthMenu": [6, 12, 18] // Customize the dropdown options
            });
        });
    </script>

</body>

</html>