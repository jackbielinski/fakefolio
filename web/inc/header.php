<div id="header">
    <?php
        if (isset($_SESSION['user_id'])) {
            // Get social settings
            $social_settings = getUserSocialSettings($_SESSION['user_id']);


            echo "<a href=\"home\"><img src=\"../_static/fakefolio_wordmark_white.png\" alt=\"Fakefolio\" width=\"150\"></a>";

            $active_user = getUserById($_SESSION['user_id']);
            $username = $active_user['username'];

            echo '<div id="nav">';
            // Stock Exchange
            echo '<a href="stock_exchange">Stock Exchange</a>';

            // Marketplace
            echo '<div class="dropdown">';
            echo '<a href="marketplace" class="dropbtn">Marketplace</a>';
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
            if ($social_settings["allow_messages"] == 1) {
                echo '<a href="messages">Messages</a>';
            }
            echo '<a href="settings">Account Settings</a>';
            if (isAdmin($_SESSION['user_id'])) {
                echo '<a href="admin">Admin Panel</a>';
            }
            echo '<a href="logout">Logout</a>';
            echo '</div>';
            echo '</div>';
            echo "</div>";
        } else {
            echo "<a href=\"index\"><img src=\"../_static/fakefolio_wordmark_white.png\" alt=\"Fakefolio\" width=\"150\"></a>";
            echo '<div id="nav"><a href="login">Log In</a><a href="register">Register</a></div>';
        }
    ?>
</div><br>