/**
 * ISPHILO FRAGANCE - Client-side Database System
 * Powered by localStorage for persistent data without PHP
 */

const ISPHILO_DB = {
    // Initial data to seed if localStorage is empty
    initialData: {
        products: [
            { id: 1, name: "Midnight Rose", category: "Ladies Fragrances", price: 495.00, sale_price: 395.00, image: "Images/IMG-20260316-WA0035.jpg", badge: "Best Seller", description: "A mysterious and elegant rose scent for the evening.", is_sold_out: false },
            { id: 2, name: "Oceanic Blue", category: "Men's Colognes", price: 600.00, sale_price: null, image: "Images/IMG-20260316-WA0036.jpg", badge: "New Arrival", description: "Fresh and invigorating ocean breeze in a bottle.", is_sold_out: false },
            { id: 3, name: "Summer Breeze", category: "Ladies Fragrances", price: 450.00, sale_price: null, image: "Images/IMG-20260316-WA0037.jpg", badge: "", description: "Light and airy floral notes for sunny days.", is_sold_out: false },
            { id: 4, name: "Midnight Leather", category: "Men's Colognes", price: 750.00, sale_price: 650.00, image: "Images/IMG-20260316-WA0038.jpg", badge: "Premium", description: "Bold and masculine leather fragrance.", is_sold_out: true },
            { id: 5, name: "Velvet Vanilla", category: "Unisex", price: 550.00, sale_price: null, image: "Images/IMG-20260316-WA0039.jpg", badge: "", description: "Smooth and creamy vanilla with a hint of spice.", is_sold_out: false },
            { id: 6, name: "Golden Oud", category: "Luxury Collection", price: 1200.00, sale_price: 999.00, image: "Images/IMG-20260316-WA0040.jpg", badge: "Exclusive", description: "Rich and opulent oud from the East.", is_sold_out: false },
            { id: 7, name: "Royal Jasmine", category: "Ladies Fragrances", price: 850.00, sale_price: 750.00, image: "Images/IMG-20260316-WA0041.jpg", badge: "Hot", description: "Majestic jasmine flowers for a royal presence.", is_sold_out: false },
            { id: 8, name: "Desert Sandalwood", category: "Men's Colognes", price: 950.00, sale_price: null, image: "Images/IMG-20260316-WA0042.jpg", badge: "New", description: "Warm and earthy sandalwood with desert spice.", is_sold_out: false },
            { id: 9, name: "Citrus Grove", category: "Unisex", price: 400.00, sale_price: 350.00, image: "Images/IMG-20260316-WA0043.jpg", badge: "Sale", description: "Zesty and bright citrus notes from the grove.", is_sold_out: false }
        ],
        users: [
            { 
                id: 1, 
                name: "Admin", 
                email: "admin@isphilo.com", 
                password: "admin", 
                role: "admin", 
                balance: 1000.00, 
                orders: [], 
                avatar: "Images/LOGO.jpg",
                phone: "+27 123 456 789",
                country: "South Africa",
                is_verified: true,
                verification_doc: null
            }
        ],
        orders: [],
        newsletter: [],
        cart: [],
        coupons: [
            { code: "WELCOME10", discount: 0.10, type: "percentage" },
            { code: "ISPHILO20", discount: 0.20, type: "percentage" },
            { code: "FIXED50", discount: 50.00, type: "fixed" }
        ]
    },

    // Event Listeners
    listeners: {},

    subscribe(event, callback) {
        if (!this.listeners[event]) this.listeners[event] = [];
        this.listeners[event].push(callback);
    },

    notify(event, data) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => callback(data));
        }
    },

    // Initialize DB
    init() {
        // Versioning check (simple)
        const currentVersion = "1.1";
        const savedVersion = localStorage.getItem('isphilo_db_version');

        if (savedVersion !== currentVersion) {
            console.log("Updating ISPHILO Database schema...");
            // Perform migrations if needed
            localStorage.setItem('isphilo_db_version', currentVersion);
        }

        if (!localStorage.getItem('isphilo_products')) {
            localStorage.setItem('isphilo_products', JSON.stringify(this.initialData.products));
        }
        if (!localStorage.getItem('isphilo_users')) {
            localStorage.setItem('isphilo_users', JSON.stringify(this.initialData.users));
        }
        if (!localStorage.getItem('isphilo_orders')) {
            localStorage.setItem('isphilo_orders', JSON.stringify(this.initialData.orders));
        }
        if (!localStorage.getItem('isphilo_newsletter')) {
            localStorage.setItem('isphilo_newsletter', JSON.stringify(this.initialData.newsletter));
        }
        if (!localStorage.getItem('isphilo_cart')) {
            localStorage.setItem('isphilo_cart', JSON.stringify(this.initialData.cart));
        }
        if (!localStorage.getItem('isphilo_coupons')) {
            localStorage.setItem('isphilo_coupons', JSON.stringify(this.initialData.coupons));
        }
        console.log("ISPHILO Database Initialized (v" + currentVersion + ")");
    },

    // Helper methods
    getData(key) {
        try {
            return JSON.parse(localStorage.getItem(`isphilo_${key}`)) || [];
        } catch (e) {
            console.error("Error reading from ISPHILO DB:", e);
            return [];
        }
    },

    setData(key, data) {
        try {
            localStorage.setItem(`isphilo_${key}`, JSON.stringify(data));
            this.notify(key + 'Updated', data);
        } catch (e) {
            console.error("Error writing to ISPHILO DB:", e);
        }
    },

    // Search & Filter
    searchProducts(query, category = null, sort = 'default') {
        let products = this.getProducts();
        
        if (query) {
            query = query.toLowerCase();
            products = products.filter(p => 
                p.name.toLowerCase().includes(query) || 
                p.description.toLowerCase().includes(query) ||
                p.category.toLowerCase().includes(query)
            );
        }

        if (category && category !== 'All Fragrances') {
            products = products.filter(p => p.category === category);
        }

        switch (sort) {
            case 'price-low':
                products.sort((a, b) => (a.sale_price || a.price) - (b.sale_price || b.price));
                break;
            case 'price-high':
                products.sort((a, b) => (b.sale_price || b.price) - (a.sale_price || a.price));
                break;
            case 'newest':
                products.sort((a, b) => b.id - a.id);
                break;
        }

        return products;
    },

    // Product methods
    validateCoupon(code) {
        const coupons = this.getData('coupons');
        return coupons.find(c => c.code.toUpperCase() === code.toUpperCase());
    },

    // Product methods
    getProducts() {
        return this.getData('products');
    },

    saveProduct(product) {
        const products = this.getProducts();
        if (product.id) {
            const index = products.findIndex(p => p.id === product.id);
            products[index] = product;
        } else {
            product.id = Date.now();
            products.push(product);
        }
        this.setData('products', products);
        return product;
    },

    deleteProduct(id) {
        const products = this.getProducts().filter(p => p.id !== id);
        this.setData('products', products);
    },

    // User methods
    getUsers() {
        return this.getData('users');
    },

    getCurrentUser() {
        return JSON.parse(sessionStorage.getItem('isphilo_current_user'));
    },

    login(email, password) {
        const users = this.getUsers();
        const user = users.find(u => u.email === email && u.password === password);
        if (user) {
            sessionStorage.setItem('isphilo_current_user', JSON.stringify(user));
            return user;
        }
        return null;
    },

    logout() {
        sessionStorage.removeItem('isphilo_current_user');
        window.location.href = 'login.html';
    },

    register(name, email, password, phone = "", country = "South Africa") {
        const users = this.getUsers();
        if (users.find(u => u.email === email)) return { success: false, message: "Email already exists" };
        
        const newUser = {
            id: Date.now(),
            name,
            email,
            password,
            role: "customer",
            balance: 0.00,
            orders: [],
            avatar: "Images/LOGO.jpg",
            phone,
            country,
            is_verified: false,
            verification_doc: null,
            created_at: new Date().toISOString()
        };
        users.push(newUser);
        this.setData('users', users);
        return { success: true, user: newUser };
    },

    updateUser(updatedUser) {
        const users = this.getUsers();
        const index = users.findIndex(u => u.id === updatedUser.id);
        if (index !== -1) {
            users[index] = updatedUser;
            this.setData('users', users);
            // Update session if it's the current user
            const currentUser = this.getCurrentUser();
            if (currentUser && currentUser.id === updatedUser.id) {
                sessionStorage.setItem('isphilo_current_user', JSON.stringify(updatedUser));
            }
            return true;
        }
        return false;
    },

    resetPassword(email, newPassword) {
        const users = this.getUsers();
        const user = users.find(u => u.email === email);
        if (user) {
            user.password = newPassword;
            this.updateUser(user);
            return { success: true, message: "Password updated successfully" };
        }
        return { success: false, message: "Email not found" };
    },

    // Cart methods
    getCart() {
        return this.getData('cart');
    },

    addToCart(productId, quantity = 1) {
        const products = this.getProducts();
        const product = products.find(p => p.id === productId);
        if (!product || product.is_sold_out) return false;

        let cart = this.getCart();
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += parseInt(quantity);
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.sale_price || product.price,
                image: product.image,
                quantity: parseInt(quantity)
            });
        }
        this.setData('cart', cart);
        return true;
    },

    removeFromCart(productId) {
        let cart = this.getCart().filter(item => item.id !== productId);
        this.setData('cart', cart);
    },

    clearCart() {
        this.setData('cart', []);
    },

    // Order methods
    getOrders() {
        return this.getData('orders');
    },

    placeOrder(customerDetails, paymentDetails) {
        const cart = this.getCart();
        if (cart.length === 0) return false;

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const order = {
            id: 'ORD-' + Math.random().toString(36).substr(2, 9).toUpperCase(),
            customer: customerDetails,
            items: cart,
            total: total,
            status: 'Pending',
            payment: paymentDetails.type, // e.g., 'Credit Card'
            date: new Date().toISOString()
        };

        const orders = this.getOrders();
        orders.push(order);
        this.setData('orders', orders);

        // If logged in, add to user's history
        const currentUser = this.getCurrentUser();
        if (currentUser) {
            currentUser.orders.push(order.id);
            this.updateUser(currentUser);
        }

        this.clearCart();
        return order;
    },

    updateOrder(orderId, newStatus) {
        const orders = this.getOrders();
        const index = orders.findIndex(o => o.id === orderId);
        if (index !== -1) {
            orders[index].status = newStatus;
            this.setData('orders', orders);
            
            // Send alert to user notifications
            const user = this.getUsers().find(u => u.email === orders[index].customer.email);
            if (user) {
                this.addNotification(user.id, `Order Update: Your order #${orderId} is now ${newStatus}.`, "order");
            }
            return true;
        }
        return false;
    },

    // Notification methods
    getNotifications(userId) {
        const allNotifs = JSON.parse(localStorage.getItem('isphilo_notifications') || '{}');
        return allNotifs[userId] || [];
    },

    addNotification(userId, message, type = "general") {
        const allNotifs = JSON.parse(localStorage.getItem('isphilo_notifications') || '{}');
        if (!allNotifs[userId]) allNotifs[userId] = [];
        
        allNotifs[userId].push({
            id: Date.now(),
            message,
            type,
            date: new Date().toISOString(),
            read: false
        });
        
        localStorage.setItem('isphilo_notifications', JSON.stringify(allNotifs));
    },

    markNotificationsRead(userId) {
        const allNotifs = JSON.parse(localStorage.getItem('isphilo_notifications') || '{}');
        if (allNotifs[userId]) {
            allNotifs[userId].forEach(n => n.read = true);
            localStorage.setItem('isphilo_notifications', JSON.stringify(allNotifs));
        }
    },

    // Newsletter methods
    subscribeNewsletter(email) {
        const newsletter = this.getData('newsletter');
        if (!newsletter.includes(email)) {
            newsletter.push({
                email,
                date: new Date().toISOString()
            });
            this.setData('newsletter', newsletter);
            return true;
        }
        return false;
    }
};

// Initialize on load
ISPHILO_DB.init();
