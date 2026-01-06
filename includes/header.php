<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Tech Phone Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12" y2="18"></line>
                    </svg>
                    <span>TechStore</span>
                </a>

                <nav class="desktop-nav">
                    <a href="/products.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : ''; ?>">Shop</a>
                    <a href="/tracking.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'tracking.php' ? 'active' : ''; ?>">Track Order</a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="/admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>">Admin</a>
                    <?php endif; ?>
                </nav>

                <div class="header-actions">
                    <a href="/cart.php" class="btn btn-ghost btn-icon" style="position: relative;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        <span id="cart-badge" class="badge badge-primary" style="position: absolute; top: -4px; right: -4px; display: none; width: 20px; height: 20px; font-size: 10px; padding: 0; align-items: center; justify-content: center;">0</span>
                    </a>

                    <div id="user-menu" style="display: none;">
                        <div style="position: relative;">
                            <button class="btn btn-ghost btn-icon" id="user-menu-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </button>
                            <div id="user-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: white; border: 1px solid var(--border); border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); min-width: 200px; z-index: 100;">
                                <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                                    <div style="font-weight: 600;" id="user-name"></div>
                                    <div style="font-size: 0.875rem; color: var(--muted-foreground);" id="user-email"></div>
                                </div>
                                <a href="/tracking.php" style="display: block; padding: 0.75rem 1rem; text-decoration: none; color: var(--foreground); border-bottom: 1px solid var(--border);">My Orders</a>
                                <a href="/admin.php" id="admin-dropdown-link" style="display: none; padding: 0.75rem 1rem; text-decoration: none; color: var(--foreground); border-bottom: 1px solid var(--border);">Admin Dashboard</a>
                                <button onclick="logout()" style="width: 100%; text-align: left; padding: 0.75rem 1rem; border: none; background: none; color: var(--destructive); cursor: pointer;">Log out</button>
                            </div>
                        </div>
                    </div>

                    <a href="/login.php" class="btn btn-primary" id="auth-button" style="display: none;">Log In</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <script>
            // Toggle user dropdown
            document.addEventListener('DOMContentLoaded', () => {
                const menuBtn = document.getElementById('user-menu-btn');
                const dropdown = document.getElementById('user-dropdown');
                
                if (menuBtn && dropdown) {
                    menuBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                    });
                    
                    document.addEventListener('click', () => {
                        dropdown.style.display = 'none';
                    });
                }
            });
        </script>
