<div id="header">
    <?php
        if (isset($_SESSION['user_id'])) {
            echo "<a href=\"home.php\"><img src=\"../_static/fakefolio_wordmark_white.png\" alt=\"Fakefolio\" width=\"150\"></a>";

            $active_user = getUserById($_SESSION['user_id']);
            $username = $active_user['username'];

            echo '<div id="nav"><ul>';
            // Stock Exchange
            echo '<a href="stock_exchange.php">Stock Exchange</a>';

            // Marketplace
            echo '<div class="dropdown">';
            echo '<a href="marketplace.php" class="dropbtn">Marketplace</a>';
            echo '</span>';
            echo '<div class="dropdown-content">';
            echo '<a href="dealers.html">Dealers</a>';
            echo '<a href="businesses.html">Businesses</a>';
            echo '</div>';
            echo '</div>';
            // User
            echo '<div class="dropdown">';
            echo '<span class="dropbtn">' . htmlspecialchars($username) . '</span>';
            echo '</span>';
            echo '<div class="dropdown-content">';
            echo '<a href="messages.php">Messages</a>';
            echo '<a href="settings.php">Account Settings</a>';
            if (isAdmin($_SESSION['user_id'])) {
                echo '<a href="admin.php">Admin Panel</a>';
            }
            echo '<a href="logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
            echo "</div>";
        } else {
            echo "<a href=\"index.php\"><img src=\"../_static/fakefolio_wordmark_white.png\" alt=\"Fakefolio\" width=\"150\"></a>";
            echo '<div id="nav"><ul><li><a href="login.php">Log In</a></li><li><a href="register.php">Register</a></li></ul></div>';
        }
    ?>
</div><br>