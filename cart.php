<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding: 3rem 0;">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 2rem;">Shopping Cart</h1>
        
        <div id="cart-container">
            <!-- Cart items will be loaded here -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    renderCart();
    cart.subscribe(() => renderCart());
});

function renderCart() {
    const container = document.getElementById('cart-container');
    const items = cart.items;
    
    if (items.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 6rem 0;">
                <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Your cart is empty</h3>
                <p class="text-muted" style="margin-bottom: 2rem;">Add some products to get started.</p>
                <a href="/products.php" class="btn btn-primary">Browse Products</a>
            </div>
        `;
        return;
    }
    
    const total = cart.getTotal();
    
    container.innerHTML = `
        <div class="grid grid-cols-1" style="gap: 2rem;">
            <div>
                ${items.map(item => `
                    <div class="card" style="display: flex; gap: 2rem; padding: 1.5rem; margin-bottom: 1rem;">
                        <img src="${item.image_url}" alt="${item.name}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0.5rem;">
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">${item.name}</h3>
                            <p class="text-muted" style="margin-bottom: 1rem;">${item.description.substring(0, 100)}...</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary); margin-bottom: 0.5rem;">
                                        ${formatPrice(item.price)}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0;">-</button>
                                        <span style="min-width: 40px; text-align: center;">${item.quantity}</span>
                                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0;">+</button>
                                    </div>
                                </div>
                                <button onclick="removeItem(${item.id})" class="btn btn-ghost" style="color: var(--destructive);">Remove</button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="card" style="padding: 2rem; height: fit-content;">
                <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span class="text-muted">Subtotal</span>
                    <span>${formatPrice(total)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span class="text-muted">Shipping</span>
                    <span>Free</span>
                </div>
                <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold;">
                        <span>Total</span>
                        <span>${formatPrice(total)}</span>
                    </div>
                </div>
                <a href="/checkout.php" class="btn btn-primary btn-lg" style="width: 100%;">Proceed to Checkout</a>
            </div>
        </div>
    `;
}

function updateQuantity(productId, quantity) {
    cart.updateQuantity(productId, quantity);
    renderCart();
}

function removeItem(productId) {
    cart.removeItem(productId);
    renderCart();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
