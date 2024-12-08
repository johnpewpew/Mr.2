<div class="container">
    <div class="sidebar">
        <div class="nav-item home">
            <a href="order_management.php" style="color: inherit; text-decoration: none;">
                <span class="material-icons-outlined">home</span>HOME
            </a>
        </div>
        <div class="nav-item pending">
            <a href="pending.php" style="color: inherit; text-decoration: none;">
                <span class="material-icons-outlined">pending</span>Reports
            </a>
        </div>
        <!-- Enhanced Logout Section -->
        <div class="nav-item logout">
            <!-- Use a form to handle logout and confirm the action -->
            <form method="POST" action="logout.php" onsubmit="return confirmLogout();" style="display: inline;">
                <button type="submit" class="logout-button" style="background: none; border: none; color: inherit; text-decoration: none; cursor: pointer;">
                    <span class="material-icons-outlined">logout</span>Logout
                </button>
            </form>
        </div>

        <!-- JavaScript for confirmation dialog -->
        <script>
            // Function to display confirmation dialog before logging out
            function confirmLogout() {
                return confirm("Are you sure you want to log out?");
            }
        </script>
    </div>
</div>

<!-- Include Ionicons Script -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>