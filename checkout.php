<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/config/config.php';

if (!isAuthenticated()) {
    header('Location: /login.php?redirect=/checkout.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<section style="padding: 3rem 0;">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 2rem;">Checkout</h1>
        
        <div id="checkout-container">
            <!-- Checkout form will be loaded here -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const items = cart.items;
    
    if (items.length === 0) {
        document.getElementById('checkout-container').innerHTML = `
            <div style="text-align: center; padding: 6rem 0;">
                <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Your cart is empty</h3>
                <a href="/products.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Products</a>
            </div>
        `;
        return;
    }
    
    const total = cart.getTotal();
    
    document.getElementById('checkout-container').innerHTML = `
        <div class="grid grid-cols-2" style="gap: 3rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem;">Shipping Address</h2>
                <form id="checkout-form">
                    <div class="form-group">
                        <label>Address Line 1</label>
                        <input type="text" name="line1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" name="zip" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">Place Order</button>
                </form>
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem;">Order Summary</h2>
                <div class="card" style="padding: 1.5rem;">
                    ${items.map(item => `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                            <div>
                                <div style="font-weight: 600;">${item.name}</div>
                                <div class="text-muted" style="font-size: 0.875rem;">Qty: ${item.quantity}</div>
                            </div>
                            <div>${formatPrice(item.price * item.quantity)}</div>
                        </div>
                    `).join('')}
                    <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold;">
                            <span>Total</span>
                            <span>${formatPrice(total)}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('checkout-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        const address = {
            line1: formData.get('line1'),
            city: formData.get('city'),
            zip: formData.get('zip'),
            country: formData.get('country')
        };
        
        const orderData = {
            items: items.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            })),
            address
        };
        
        try {
            const order = await api.createOrder(orderData);
            cart.clearCart();
            showToast('Order placed successfully!');
            window.location.href = `/tracking.php?order_id=${order.id}`;
        } catch (error) {
            showToast(error.message || 'Failed to place order', 'error');
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
