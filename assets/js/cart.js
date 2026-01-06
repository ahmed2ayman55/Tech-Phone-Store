class Cart {
    constructor() {
        this.items = this.loadCart();
        this.listeners = [];
    }

    loadCart() {
        const saved = localStorage.getItem('tech-store-cart');
        return saved ? JSON.parse(saved) : [];
    }

    saveCart() {
        localStorage.setItem('tech-store-cart', JSON.stringify(this.items));
        this.notifyListeners();
    }

    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({ ...product, quantity: 1 });
        }
        
        this.saveCart();
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveCart();
    }

    updateQuantity(productId, quantity) {
        if (quantity <= 0) {
            this.removeItem(productId);
            return;
        }
        
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
            this.saveCart();
        }
    }

    clearCart() {
        this.items = [];
        this.saveCart();
    }

    getTotal() {
        return this.items.reduce((sum, item) => {
            return sum + parseFloat(item.price) * item.quantity;
        }, 0);
    }

    getCount() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    subscribe(listener) {
        this.listeners.push(listener);
        return () => {
            this.listeners = this.listeners.filter(l => l !== listener);
        };
    }

    notifyListeners() {
        this.listeners.forEach(listener => listener(this.items));
    }
}

const cart = new Cart();

document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
    cart.subscribe(() => updateCartBadge());
});

function updateCartBadge() {
    const badge = document.getElementById('cart-badge');
    const count = cart.getCount();
    
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}
