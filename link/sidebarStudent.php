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
           <li>
    <a href="../code/dashboardStudent.php">
        <i class="material-symbols-outlined">dashboard</i>                    
        <span class="link_name">Dashboard</span>
    </a>
</li>
            <li>
                    <a href="userProfile.php">
                        <i class="material-symbols-outlined">account_circle</i>
                        <span class="link_name">Profile</span>
                    </a>
                </li>
    <li>
                <a href="orderStudent.php">
                    <i class="material-symbols-outlined">shopping_cart</i>
                    <span class="link_name">Orders</span>
                </a>
            </li>
             <li>
                <a href="manageMembershipCard.php">
                    <i class="material-symbols-outlined">credit_card</i>
                    <span class="link_name">Membership Card</span>
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
