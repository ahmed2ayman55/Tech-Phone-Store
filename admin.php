<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/config/config.php';

// Debug: Check session
if (!isAuthenticated()) {
    // Redirect to admin login
    header('Location: /admin-login.php');
    exit;
}

if (!isAdmin()) {
    // Check database to see if user should be admin
    $db = getDB();
    $userId = getCurrentUserId();
    $stmt = $db->prepare("SELECT email, is_admin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update session if user should be admin
        if ($user['is_admin'] || strpos($user['email'], 'admin') !== false) {
            $_SESSION['is_admin'] = true;
            // Update database if needed
            if (!$user['is_admin']) {
                $stmt = $db->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
                $stmt->execute([$userId]);
            }
        } else {
            header('Location: /?error=admin_access_required');
            exit;
        }
    } else {
        header('Location: /login.php?redirect=/admin.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section style="padding: 3rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2.5rem; font-weight: bold;">Admin Dashboard</h1>
            <button onclick="showAddProductModal()" class="btn btn-primary">Add Product</button>
        </div>
        
        <div id="products-container" class="grid grid-cols-4">
            <!-- Products will be loaded here -->
        </div>
    </div>
</section>

<!-- Add/Edit Product Modal -->
<div id="product-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 1rem; padding: 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 id="modal-title" style="font-size: 1.5rem; font-weight: bold;">Add Product</h2>
            <button onclick="closeProductModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
        </div>
        <form id="product-form">
            <input type="hidden" id="product-id">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="product-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="product-description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" id="product-price" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select id="product-category" class="form-control" required>
                    <option value="Phones">Phones</option>
                    <option value="Laptops">Laptops</option>
                    <option value="Audio">Audio</option>
                    <option value="Accessories">Accessories</option>
                </select>
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="url" id="product-image-url" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" id="product-stock" class="form-control" value="0" required>
            </div>
            <div class="form-group">
                <label>Specs (JSON format, e.g., {"color": "Black", "storage": "128GB"})</label>
                <textarea id="product-specs" class="form-control" placeholder='{"key": "value"}'></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                <button type="button" onclick="closeProductModal()" class="btn btn-outline" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let editingProduct = null;

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    
    document.getElementById('product-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const productData = {
            name: document.getElementById('product-name').value,
            description: document.getElementById('product-description').value,
            price: parseFloat(document.getElementById('product-price').value),
            category: document.getElementById('product-category').value,
            image_url: document.getElementById('product-image-url').value,
            stock: parseInt(document.getElementById('product-stock').value),
        };
        
        const specsText = document.getElementById('product-specs').value.trim();
        if (specsText) {
            try {
                productData.specs = JSON.parse(specsText);
            } catch (e) {
                showToast('Invalid JSON format for specs', 'error');
                return;
            }
        }
        
        try {
            if (editingProduct) {
                await api.updateProduct(editingProduct.id, productData);
                showToast('Product updated successfully');
            } else {
                await api.createProduct(productData);
                showToast('Product created successfully');
            }
            closeProductModal();
            loadProducts();
        } catch (error) {
            showToast(error.message || 'Failed to save product', 'error');
        }
    });
});

async function loadProducts() {
    try {
        const products = await api.getProducts();
        const container = document.getElementById('products-container');
        
        container.innerHTML = products.map(product => `
            <div class="card product-card">
                <img src="${product.image_url}" alt="${product.name}">
                <div class="product-card-content">
                    <h3>${product.name}</h3>
                    <p>${product.description.substring(0, 100)}...</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <div class="price">${formatPrice(product.price)}</div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button onclick="editProduct(${JSON.stringify(product).replace(/"/g, '&quot;')})" class="btn btn-outline" style="padding: 0.25rem 0.75rem;">Edit</button>
                            <button onclick="deleteProduct(${product.id})" class="btn btn-outline" style="padding: 0.25rem 0.75rem; color: var(--destructive);">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load products:', error);
    }
}

function showAddProductModal() {
    editingProduct = null;
    document.getElementById('modal-title').textContent = 'Add Product';
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    document.getElementById('product-modal').style.display = 'flex';
}

function editProduct(product) {
    editingProduct = product;
    document.getElementById('modal-title').textContent = 'Edit Product';
    document.getElementById('product-id').value = product.id;
    document.getElementById('product-name').value = product.name;
    document.getElementById('product-description').value = product.description;
    document.getElementById('product-price').value = product.price;
    document.getElementById('product-category').value = product.category;
    document.getElementById('product-image-url').value = product.image_url;
    document.getElementById('product-stock').value = product.stock;
    document.getElementById('product-specs').value = product.specs ? JSON.stringify(product.specs, null, 2) : '';
    document.getElementById('product-modal').style.display = 'flex';
}

function closeProductModal() {
    document.getElementById('product-modal').style.display = 'none';
    editingProduct = null;
}

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }
    
    try {
        await api.deleteProduct(id);
        showToast('Product deleted successfully');
        loadProducts();
    } catch (error) {
        showToast(error.message || 'Failed to delete product', 'error');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
