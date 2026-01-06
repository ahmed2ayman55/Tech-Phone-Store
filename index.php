<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div>
                <h1>
                    Next Gen<br>
                    <span class="gradient">Tech Is Here</span>
                </h1>
                <p>
                    Discover premium devices engineered for professionals. 
                    Uncompromising performance meets stunning design.
                </p>
                <div class="hero-actions">
                    <a href="/products.php" class="btn btn-primary btn-lg">Shop Now</a>
                    <a href="/products.php?category=Accessories" class="btn btn-outline btn-lg">View Accessories</a>
                </div>
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?q=80&w=800&auto=format&fit=crop" 
                     alt="Premium Phone" 
                     style="width: 100%; max-width: 500px; border-radius: 1.5rem; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</section>

<section style="padding: 5rem 0; background: white;">
    <div class="container">
        <div class="grid grid-cols-3">
            <div style="padding: 2rem; border-radius: 1rem; background: var(--muted);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Lightning Fast</h3>
                <p style="color: var(--muted-foreground);">Powered by the latest processors for unmatched speed.</p>
            </div>
            <div style="padding: 2rem; border-radius: 1rem; background: var(--muted);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Secure & Reliable</h3>
                <p style="color: var(--muted-foreground);">Enterprise-grade security features built into every device.</p>
            </div>
            <div style="padding: 2rem; border-radius: 1rem; background: var(--muted);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <line x1="5" y1="8" x2="12" y2="8"></line>
                    <line x1="5" y1="12" x2="12" y2="12"></line>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Free Shipping</h3>
                <p style="color: var(--muted-foreground);">Complimentary express shipping on all orders over $500.</p>
            </div>
        </div>
    </div>
</section>

<section style="padding: 6rem 0; background: var(--muted);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
            <div>
                <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">Featured Devices</h2>
                <p style="color: var(--muted-foreground);">Our most popular products this week.</p>
            </div>
            <a href="/products.php" class="btn btn-outline">View All →</a>
        </div>

        <div id="featured-products" class="grid grid-cols-4">
            <!-- Products will be loaded here -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const products = await api.getProducts();
        const featured = products.slice(0, 4);
        const container = document.getElementById('featured-products');
        
        if (featured.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">No products available.</p>';
            return;
        }
        
        container.innerHTML = featured.map(product => `
            <div class="card product-card">
                <a href="/product-detail.php?id=${product.id}">
                    <img src="${product.image_url}" alt="${product.name}">
                </a>
                <div class="product-card-content">
                    <a href="/product-detail.php?id=${product.id}" style="text-decoration: none; color: inherit;">
                        <h3>${product.name}</h3>
                    </a>
                    <p>${product.description.substring(0, 100)}...</p>
                    <div class="price">${formatPrice(product.price)}</div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load products:', error);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
