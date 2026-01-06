<?php
$pageTitle = 'Product Details';
require_once __DIR__ . '/includes/header.php';

$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: /products.php');
    exit;
}
?>

<section style="padding: 3rem 0;">
    <div class="container">
        <div id="product-container">
            <!-- Product will be loaded here -->
            <div class="grid grid-cols-2" style="gap: 3rem;">
                <div class="skeleton" style="height: 500px;"></div>
                <div>
                    <div class="skeleton" style="height: 3rem; margin-bottom: 1rem;"></div>
                    <div class="skeleton" style="height: 2rem; margin-bottom: 1rem;"></div>
                    <div class="skeleton" style="height: 6rem;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const productId = <?php echo json_encode($productId); ?>;
    
    try {
        const [product, reviews] = await Promise.all([
            api.getProduct(productId),
            api.getReviews(productId)
        ]);
        
        const specs = product.specs || {};
        const specsHtml = Object.keys(specs).length > 0 
            ? Object.entries(specs).map(([key, value]) => `
                <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--muted-foreground); text-transform: capitalize;">${key}</span>
                    <span style="font-weight: 600;">${value}</span>
                </div>
            `).join('')
            : '<p class="text-muted">No specifications listed.</p>';
        
        const reviewsHtml = reviews.length > 0
            ? reviews.map(review => `
                <div style="border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <div style="font-weight: 600;">
                            ${review.first_name || 'Anonymous'} ${review.last_name ? review.last_name.charAt(0) : ''}
                        </div>
                        <div style="font-size: 0.875rem; color: var(--muted-foreground);">
                            ${formatDate(review.created_at)}
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.25rem; margin-bottom: 0.5rem;">
                        ${Array(review.rating).fill(0).map(() => '⭐').join('')}
                    </div>
                    <p style="color: var(--muted-foreground);">${review.comment || ''}</p>
                </div>
            `).join('')
            : '<p class="text-muted italic">No reviews yet.</p>';
        
        document.getElementById('product-container').innerHTML = `
            <div style="margin-bottom: 5rem;">
                <div class="grid grid-cols-2" style="gap: 3rem; margin-bottom: 5rem;">
                    <div style="background: white; border-radius: 1.5rem; padding: 2rem; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;">
                        <img src="${product.image_url}" alt="${product.name}" style="width: 100%; max-height: 500px; object-fit: contain;">
                    </div>
                    <div>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <span class="badge">${product.category}</span>
                            <span class="badge ${product.stock > 0 ? 'badge-success' : 'badge-destructive'}">
                                ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}
                            </span>
                        </div>
                        <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">${product.name}</h1>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; gap: 0.25rem;">
                                ${Array(5).fill(0).map((_, i) => `<span style="color: ${i < 4 ? '#fbbf24' : '#e5e7eb'}; font-size: 1.25rem;">⭐</span>`).join('')}
                            </div>
                            <span class="text-muted">(${reviews.length} reviews)</span>
                        </div>
                        <p style="font-size: 1.125rem; color: var(--muted-foreground); margin-bottom: 2rem; line-height: 1.8;">
                            ${product.description}
                        </p>
                        <div style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 1.5rem 0; margin-bottom: 2rem;">
                            <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;">${formatPrice(product.price)}</div>
                            <p style="font-size: 0.875rem; color: var(--muted-foreground);">Free shipping on this item.</p>
                        </div>
                        <button onclick="addToCart(${JSON.stringify(product).replace(/"/g, '&quot;')})" 
                                class="btn btn-primary btn-lg" 
                                style="width: 100%;"
                                ${product.stock <= 0 ? 'disabled' : ''}>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-3" style="gap: 3rem;">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem;">Specifications</h3>
                    <div style="background: var(--muted); border-radius: 1rem; padding: 1.5rem;">
                        ${specsHtml}
                    </div>
                </div>
                <div style="grid-column: span 2;">
                    <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem;">Customer Reviews</h3>
                    <div id="review-form-container"></div>
                    <div style="margin-top: 2rem;">
                        ${reviewsHtml}
                    </div>
                </div>
            </div>
        `;
        
        // Load review form if authenticated
        if (typeof currentUser !== 'undefined' && currentUser) {
            document.getElementById('review-form-container').innerHTML = `
                <form id="review-form" style="background: var(--muted); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
                    <h4 style="font-weight: 600; margin-bottom: 1rem;">Write a review</h4>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                        ${Array(5).fill(0).map((_, i) => `
                            <button type="button" onclick="setRating(${i + 1})" style="background: none; border: none; cursor: pointer; font-size: 1.5rem;">
                                <span id="star-${i + 1}" style="color: #e5e7eb;">⭐</span>
                            </button>
                        `).join('')}
                    </div>
                    <textarea id="review-comment" class="form-control" placeholder="Share your thoughts..." required style="margin-bottom: 1rem; min-height: 100px;"></textarea>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            `;
            
            let selectedRating = 5;
            window.setRating = (rating) => {
                selectedRating = rating;
                for (let i = 1; i <= 5; i++) {
                    document.getElementById(`star-${i}`).style.color = i <= rating ? '#fbbf24' : '#e5e7eb';
                }
            };
            window.setRating(5);
            
            document.getElementById('review-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const comment = document.getElementById('review-comment').value;
                
                try {
                    await api.createReview(productId, { rating: selectedRating, comment });
                    showToast('Review submitted!');
                    location.reload();
                } catch (error) {
                    showToast(error.message || 'Failed to submit review', 'error');
                }
            });
        } else {
            document.getElementById('review-form-container').innerHTML = `
                <div style="background: var(--muted); border-radius: 1rem; padding: 1.5rem; text-align: center;">
                    <p style="margin-bottom: 1rem;">Please log in to leave a review.</p>
                    <a href="/login.php" class="btn btn-outline">Log In</a>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Failed to load product:', error);
        document.getElementById('product-container').innerHTML = `
            <div style="text-align: center; padding: 6rem 0;">
                <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Product not found</h3>
                <p class="text-muted">The product you're looking for doesn't exist.</p>
                <a href="/products.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Products</a>
            </div>
        `;
    }
});

function addToCart(product) {
    cart.addItem(product);
    showToast(`${product.name} added to cart`);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
