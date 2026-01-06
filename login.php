<?php
$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';

$redirect = $_GET['redirect'] ?? '/';
?>

<section style="padding: 6rem 0;">
    <div class="container">
        <div style="max-width: 400px; margin: 0 auto;">
            <div class="card" style="padding: 2rem;">
                <h1 id="page-title" style="font-size: 2rem; font-weight: bold; margin-bottom: 1.5rem; text-align: center;">Login</h1>
                
                <div id="login-tabs" style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                    <button onclick="showLogin()" id="login-tab" class="btn btn-ghost" style="flex: 1; border-bottom: 2px solid var(--primary);">Login</button>
                    <button onclick="showRegister()" id="register-tab" class="btn btn-ghost" style="flex: 1;">Register</button>
                </div>
                <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                    <a href="/admin-login.php" style="color: var(--muted-foreground); text-decoration: none; font-size: 0.875rem;">Admin Login →</a>
                </div>
                
                <!-- Login Form -->
                <form id="login-form" style="display: block;">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="login-email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="login-password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Log In</button>
                </form>
                
                <!-- Register Form -->
                <form id="register-form" style="display: none;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="register-first-name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="register-last-name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="register-email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="register-password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
const redirect = <?php echo json_encode($redirect); ?>;

function showLogin() {
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-tab').style.borderBottom = '2px solid var(--primary)';
    document.getElementById('register-tab').style.borderBottom = 'none';
    document.getElementById('page-title').textContent = 'Login';
}

function showRegister() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
    document.getElementById('register-tab').style.borderBottom = '2px solid var(--primary)';
    document.getElementById('login-tab').style.borderBottom = 'none';
    document.getElementById('page-title').textContent = 'Register';
}

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    
    const success = await login(email, password);
    if (success) {
        window.location.href = redirect;
    }
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        first_name: document.getElementById('register-first-name').value,
        last_name: document.getElementById('register-last-name').value,
        email: document.getElementById('register-email').value,
        password: document.getElementById('register-password').value
    };
    
    const success = await register(data);
    if (success) {
        window.location.href = redirect;
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
