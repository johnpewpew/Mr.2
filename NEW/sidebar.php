<div class="container"></div>
<div class="navigation">
    <ul>
        <!-- Logo Section -->
        <li>
            <a href="#">
                <span class="icon">
                    <img src="img/Logo.png" alt="Mr. Special Tea Logo" class="logo-image">
                </span>
                <span class="title">Mr. Boy Special Tea</span>
            </a>
        </li>

        <!-- Dashboard Section -->
        <li>
            <a href="admin_dash.php"><span class="icon">
            <ion-icon name="grid-outline"></ion-icon>
                </span><span class="title">Dashboard</span>
            </a>
        </li>

        <!-- Inventory -->
        <li>
            <a href="Inventory.php"><span class="icon">
            <ion-icon name="file-tray-stacked-outline"></ion-icon></span>
                <span class="title">Inventory</span>
            </a>
        </li>

        <!-- Items Section -->
        <li>
            <a href="item.php"><span class="icon">
            <ion-icon name="archive-outline"></ion-icon></span>
                <span class="title">Items</span>
            </a>
        </li>

        <!-- Category Section -->
        <li>
            <a href="categories.php"><span class="icon">
            <ion-icon name="file-tray-stacked-outline"></ion-icon></span>
                <span class="title">Category</span>
            </a>
        </li>

        <!-- Transaction Section -->
        <li>
            <a href="transaction.php"><span class="icon">
            <ion-icon name="repeat-outline"></ion-icon></span>
                <span class="title">Transaction</span>
            </a>
        </li>

        <!-- Logout Section -->
        <li>
            <a href="logout.php" id="logout-link"><span class="icon">
            <ion-icon name="log-out-outline"></ion-icon></span>
                <span class="title">Logout</span>
            </a>
        </li>
    </ul>
</div>
<script>
    // Attach confirmation prompt to the logout link
    document.getElementById('logout-link').addEventListener('click', function(event) {
        // Show confirmation dialog
        const confirmed = confirm('Are you sure you want to logout?');
        if (!confirmed) {
            // Prevent default action if user cancels
            event.preventDefault();
        }
    });
</script>
<!-- Include Ionicons Script -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>