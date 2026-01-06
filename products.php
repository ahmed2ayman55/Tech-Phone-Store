<?php
$pageTitle = 'Products';
require_once __DIR__ . '/includes/header.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
?>

<section style="background: var(--muted); padding: 3rem 0; border-bottom: 1px solid var(--border);">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">Shop</h1>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted-foreground);">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" 
                       id="search-input" 
                       placeholder="Search products..." 
                       class="form-control" 
                       style="padding-left: 2.5rem; height: 48px;"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="/products.php" class="btn <?php echo !$category ? 'btn-primary' : 'btn-outline'; ?>">All Products</a>
                <a href="/products.php?category=Phones" class="btn <?php echo $category === 'Phones' ? 'btn-primary' : 'btn-outline'; ?>">Smartphones</a>
                <a href="/products.php?category=Laptops" class="btn <?php echo $category === 'Laptops' ? 'btn-primary' : 'btn-outline'; ?>">Laptops</a>
                <a href="/products.php?category=Audio" class="btn <?php echo $category === 'Audio' ? 'btn-primary' : 'btn-outline'; ?>">Audio</a>
                <a href="/products.php?category=Accessories" class="btn <?php echo $category === 'Accessories' ? 'btn-primary' : 'btn-outline'; ?>">Accessories</a>
            </div>
        </div>
    </div>
</section>

<section style="padding: 3rem 0;">
    <div class="container">
        <div id="products-container" class="grid grid-cols-4">
            <!-- Products will be loaded here -->
            <div class="skeleton" style="height: 400px;"></div>
            <div class="skeleton" style="height: 400px;"></div>
            <div class="skeleton" style="height: 400px;"></div>
            <div class="skeleton" style="height: 400px;"></div>
        </div>
    </div>
</section>

<script>
let searchTimeout;

document.addEventListener('DOMContentLoaded', async () => {
    await loadProducts();
    
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(() => {
            const search = searchInput.value;
            setQueryParam('search', search);
            loadProducts();
        }, 300));
    }
});

async function loadProducts() {
    const container = document.getElementById('products-container');
    const category = getQueryParam('category') || '';
    const search = getQueryParam('search') || '';
    
    try {
        const products = await api.getProducts({ category, search });
        
        if (products.length === 0) {
            container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 6rem 0;">
                    <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">No products found</h3>
                    <p class="text-muted">Try adjusting your search or category filter.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = products.map(product => `
            <div class="card product-card">
                <a href="/product-detail.php?id=${product.id}">
                    <img src="${product.image_url}" alt="${product.name}">
                </a>
                <div class="product-card-content">
                    <a href="/product-detail.php?id=${product.id}" style="text-decoration: none; color: inherit;">
                        <h3>${product.name}</h3>
                    </a>
                    <p style="min-height: 3rem;">${product.description.substring(0, 100)}${product.description.length > 100 ? '...' : ''}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="price">${formatPrice(product.price)}</div>
                        <span class="badge ${product.stock > 0 ? 'badge-success' : 'badge-destructive'}">
                            ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load products:', error);
        container.innerHTML = '<p class="text-center text-muted">Failed to load products. Please try again.</p>';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
