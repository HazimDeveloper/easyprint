<?php
// Get the current script name to determine the active page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sideBar">
    <div class="logo_content">
        <div class="logo">
            <img src="../img/logo.png" alt="logo">
        </div>
        <span class="logo_name">EasyPrint</span>
    </div>

    <div class="menu d-flex justify-content-between">
        <ul class="p-0 m-0 list-unstyled">
            <li class="<?= ($current_page == 'dashboardStaff.php') ? 'active' : '' ?>">
                <a href="../code/dashboardStaff.php">
                    <i class="material-symbols-outlined">dashboard</i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>
            <li class="<?= ($current_page == 'packageManagement.php') ? 'active' : '' ?>">
                <a href="../code/packageManagement.php">
                    <i class="material-symbols-outlined">package_2</i>
                    <span class="link_name">Package</span>
                </a>
            </li>
            <li class="<?= ($current_page == 'userManagement.php') ? 'active' : '' ?>">
                <a href="../code/userManagement.php">
                    <i class="material-symbols-outlined">person</i>
                    <span class="link_name">User</span>
                </a>
            </li>
            <li class="<?= ($current_page == 'branchManagement.php') ? 'active' : '' ?>">
                <a href="../code/orderManagement.php">
                    <i class="material-symbols-outlined">print</i>
                    <span class="link_name">Order</span>
                </a>
            </li>

            <li>
                <a href="login.php">
                    <i class="material-symbols-outlined">logout</i>
                    <span class="link_name">Sign Out</span>
                </a>
            </li>

        </ul>
    </div>
</div>