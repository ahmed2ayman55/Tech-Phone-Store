<?php
$pageTitle = 'Order Tracking';
require_once __DIR__ . '/config/config.php';

if (!isAuthenticated()) {
    header('Location: /login.php?redirect=/tracking.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';

$orderId = $_GET['order_id'] ?? null;
?>

<section style="padding: 3rem 0;">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 2rem;">My Orders</h1>
        
        <div id="orders-container">
            <!-- Orders will be loaded here -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const orders = await api.getOrders();
        const container = document.getElementById('orders-container');
        
        if (orders.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 6rem 0;">
                    <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">No orders yet</h3>
                    <p class="text-muted" style="margin-bottom: 2rem;">Start shopping to see your orders here.</p>
                    <a href="/products.php" class="btn btn-primary">Browse Products</a>
                </div>
            `;
            return;
        }
        
        container.innerHTML = orders.map(order => {
            const statusColors = {
                pending: 'badge',
                processing: 'badge badge-primary',
                shipped: 'badge badge-primary',
                delivered: 'badge badge-success',
                cancelled: 'badge badge-destructive'
            };
            
            return `
                <div class="card" style="padding: 2rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Order #${order.id}</h3>
                            <p class="text-muted" style="font-size: 0.875rem;">Placed on ${formatDate(order.created_at)}</p>
                        </div>
                        <div>
                            <span class="${statusColors[order.status] || 'badge'}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
                        </div>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        ${order.items.map(item => `
                            <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                                <img src="${item.product_image || 'https://placehold.co/100x100'}" alt="${item.product_name}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.5rem;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600;">${item.product_name}</div>
                                    <div class="text-muted" style="font-size: 0.875rem;">Quantity: ${item.quantity}</div>
                                </div>
                                <div style="font-weight: 600;">${formatPrice(item.price * item.quantity)}</div>
                            </div>
                        `).join('')}
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <div>
                            <div class="text-muted" style="font-size: 0.875rem;">Shipping to:</div>
                            <div>${order.address.line1}, ${order.address.city}, ${order.address.zip}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="text-muted" style="font-size: 0.875rem;">Total</div>
                            <div style="font-size: 1.5rem; font-weight: bold;">${formatPrice(order.total)}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Failed to load orders:', error);
        document.getElementById('orders-container').innerHTML = `
            <div style="text-align: center; padding: 6rem 0;">
                <p class="text-muted">Failed to load orders. Please try again.</p>
            </div>
        `;
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
