<?php
$pageTitle = 'Admin Login';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding: 6rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: calc(100vh - 200px);">
    <div class="container">
        <div style="max-width: 450px; margin: 0 auto;">
            <div class="card" style="padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 0.5rem;">Admin Portal</h1>
                    <p style="color: var(--muted-foreground);">Administrator Access Only</p>
                </div>
                
                <form id="admin-login-form">
                    <div class="form-group">
                        <label style="font-weight: 600; margin-bottom: 0.5rem;">Email Address</label>
                        <input type="email" id="admin-email" class="form-control" placeholder="admin@example.com" required autofocus>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600; margin-bottom: 0.5rem;">Password</label>
                        <input type="password" id="admin-password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <div id="admin-error" style="display: none; padding: 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;"></div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 48px; font-weight: 600;">Sign In</button>
                </form>
                
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border); text-align: center;">
                    <a href="/" style="color: var(--muted-foreground); text-decoration: none; font-size: 0.875rem;">← Back to Store</a>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 2rem; color: rgba(255,255,255,0.8); font-size: 0.875rem;">
                <p>Unauthorized access is prohibited</p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('admin-login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('admin-email').value;
    const password = document.getElementById('admin-password').value;
    const errorDiv = document.getElementById('admin-error');
    
    errorDiv.style.display = 'none';
    
    try {
        const response = await fetch('/api/auth.php?action=admin-login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Login failed');
        }
        
        if (data.user && data.user.is_admin) {
            // Success - redirect to admin dashboard
            window.location.href = '/admin.php';
        } else {
            throw new Error('Access denied. Admin privileges required.');
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Invalid credentials. Please try again.';
        errorDiv.style.display = 'block';
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
