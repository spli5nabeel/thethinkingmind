<?php
/**
 * Navigation Bar Component
 * @param string $active Active page identifier
 * @param bool $show_admin Show admin link if user is admin
 */
function renderNavigation($active = '', $show_admin = false) {
    $user = getCurrentUser();
?>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">🧠 The Thinking Mind</a>
            </div>
            <ul class="nav-menu">
                <li class="<?php echo $active === 'home' ? 'active' : ''; ?>">
                    <a href="index.php">Home</a>
                </li>
                <li class="<?php echo $active === 'categories' ? 'active' : ''; ?>">
                    <a href="categories.php">Categories</a>
                </li>
                <?php if ($user): ?>
                    <li class="<?php echo $active === 'results' ? 'active' : ''; ?>">
                        <a href="my_results.php">My Results</a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="<?php echo $active === 'admin' ? 'active' : ''; ?>">
                            <a href="admin.php">Admin Panel</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="logout.php">Logout (<?php echo htmlspecialchars($user['username']); ?>)</a>
                    </li>
                <?php else: ?>
                    <li class="<?php echo $active === 'login' ? 'active' : ''; ?>">
                        <a href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
<?php
}
?>
