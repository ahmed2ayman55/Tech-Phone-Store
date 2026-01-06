let currentUser = null;

async function initAuth() {
    try {
        const response = await api.getCurrentUser();
        currentUser = response.user;
        updateAuthUI();
    } catch (error) {
        console.error('Auth init failed:', error);
        currentUser = null;
        updateAuthUI();
    }
}

function updateAuthUI() {
    const authButton = document.getElementById('auth-button');
    const userMenu = document.getElementById('user-menu');
    const userName = document.getElementById('user-name');
    const userEmail = document.getElementById('user-email');
    const adminLink = document.getElementById('admin-link');
    const adminDropdownLink = document.getElementById('admin-dropdown-link');
    
    if (currentUser) {
        if (authButton) authButton.style.display = 'none';
        if (userMenu) userMenu.style.display = 'block';
        if (userName) {
            const name = `${currentUser.first_name || ''} ${currentUser.last_name || ''}`.trim() || currentUser.email;
            userName.textContent = name;
        }
        if (userEmail) {
            userEmail.textContent = currentUser.email;
        }
        if (adminLink && currentUser.is_admin) {
            adminLink.style.display = 'block';
        }
        if (adminDropdownLink && currentUser.is_admin) {
            adminDropdownLink.style.display = 'block';
        }
    } else {
        if (authButton) authButton.style.display = 'block';
        if (userMenu) userMenu.style.display = 'none';
        if (adminLink) adminLink.style.display = 'none';
        if (adminDropdownLink) adminDropdownLink.style.display = 'none';
    }
}

async function login(email, password) {
    try {
        const response = await api.login(email, password);
        currentUser = response.user;
        updateAuthUI();
        showToast('Logged in successfully');
        return true;
    } catch (error) {
        showToast(error.message || 'Login failed', 'error');
        return false;
    }
}

async function register(data) {
    try {
        const response = await api.register(data);
        currentUser = response.user;
        updateAuthUI();
        showToast('Account created successfully');
        return true;
    } catch (error) {
        showToast(error.message || 'Registration failed', 'error');
        return false;
    }
}

async function logout() {
    try {
        await api.logout();
        currentUser = null;
        updateAuthUI();
        showToast('Logged out successfully');
        window.location.href = '/';
    } catch (error) {
        showToast('Logout failed', 'error');
    }
}

function isAuthenticated() {
    return currentUser !== null;
}

function isAdmin() {
    return currentUser && currentUser.is_admin === true;
}

document.addEventListener('DOMContentLoaded', initAuth);
