const API_BASE = '/api';

class API {
    async request(url, options = {}) {
        const response = await fetch(API_BASE + url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers,
            },
            credentials: 'include',
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({ message: 'Request failed' }));
            throw new Error(error.message || 'Request failed');
        }

        if (response.status === 204 || response.status === 201) {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.status === 201 ? response.json().catch(() => ({})) : null;
            }
        }

        const text = await response.text();
        if (!text || text.trim() === '') {
            return null;
        }

        try {
            return JSON.parse(text);
        } catch (e) {
            return text || null;
        }
    }

    async getProducts(params = {}) {
        const query = new URLSearchParams();
        if (params.search) query.append('search', params.search);
        if (params.category) query.append('category', params.category);
        
        const queryString = query.toString();
        return this.request('/products.php' + (queryString ? '?' + queryString : ''));
    }

    async getProduct(id) {
        return this.request(`/products.php?id=${id}`);
    }

    async createProduct(data) {
        return this.request('/products.php', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    async updateProduct(id, data) {
        return this.request(`/products.php?id=${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    async deleteProduct(id) {
        const response = await fetch(API_BASE + `/products.php?id=${id}`, {
            method: 'DELETE',
            credentials: 'include',
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({ message: 'Request failed' }));
            throw new Error(error.message || 'Failed to delete product');
        }

        return true;
    }

    // Orders
    async getOrders() {
        return this.request('/orders.php');
    }

    async getOrder(id) {
        return this.request(`/orders.php?id=${id}`);
    }

    async createOrder(data) {
        return this.request('/orders.php', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    // Reviews
    async getReviews(productId) {
        return this.request(`/reviews.php?product_id=${productId}`);
    }

    async createReview(productId, data) {
        return this.request(`/reviews.php?product_id=${productId}`, {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    // Auth
    async getCurrentUser() {
        return this.request('/auth.php');
    }

    async login(email, password) {
        return this.request('/auth.php?action=login', {
            method: 'POST',
            body: JSON.stringify({ email, password }),
        });
    }

    async register(data) {
        return this.request('/auth.php?action=register', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    async logout() {
        return this.request('/auth.php?action=logout', {
            method: 'POST',
        });
    }
}

const api = new API();
