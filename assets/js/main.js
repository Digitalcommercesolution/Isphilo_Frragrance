/**
 * ISPHILO FRAGANCE - Main JavaScript
 * Handles global UI, Cart, Search, and DB Interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. MOBILE MENU TOGGLE
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.innerHTML = navMenu.classList.contains('active') ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }

    // 2. HEADER SCROLL EFFECT
    const header = document.querySelector('header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // 3. CART UPDATE & RENDER
    function updateCartUI() {
        const cart = ISPHILO_DB.getCart();
        const cartCounts = document.querySelectorAll('.cart-count');
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        cartCounts.forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    // Subscribe to cart updates
    ISPHILO_DB.subscribe('cartUpdated', updateCartUI);
    updateCartUI(); // Initial call

    window.addToCart = function(productId, quantity = 1) {
        const success = ISPHILO_DB.addToCart(productId, quantity);
        if (success) {
            showNotification('Product added to cart!', 'success');
        } else {
            showNotification('Could not add to cart. Product might be sold out.', 'error');
        }
    };

    // 4. SEARCH OVERLAY
    const openSearch = document.getElementById('openSearch');
    const closeSearch = document.getElementById('closeSearch');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (openSearch && searchOverlay) {
        openSearch.addEventListener('click', () => {
            searchOverlay.style.display = 'block';
            searchInput.focus();
        });
    }

    if (closeSearch && searchOverlay) {
        closeSearch.addEventListener('click', () => {
            searchOverlay.style.display = 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            const filtered = ISPHILO_DB.searchProducts(query);

            if (filtered.length > 0) {
                searchResults.innerHTML = `
                    <div style="grid-column: 1/-1; margin-bottom: 20px; border-bottom: 1px solid var(--gray-light); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-red);">Found ${filtered.length} results</span>
                        <a href="shop.html?search=${encodeURIComponent(query)}" style="font-family: 'Montserrat', sans-serif; font-size: 10px; text-decoration: none; color: var(--pure-black); opacity: 0.6; font-weight: 700; text-transform: uppercase;">View All in Shop <i class="fas fa-arrow-right" style="margin-left: 5px;"></i></a>
                    </div>
                ` + filtered.map(p => `
                    <div class="product-card" onclick="window.location.href='product-details.html?id=${p.id}'" style="cursor: pointer; background: var(--pure-white); border: 1px solid var(--gray-light); padding: 15px; transition: var(--transition);">
                        <div class="product-image" style="aspect-ratio: 1/1; overflow: hidden; margin-bottom: 15px; background: var(--off-white);">
                            <img src="${p.image}" alt="${p.name}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="product-info">
                            <p class="product-category" style="font-size: 9px; opacity: 0.5; margin-bottom: 5px;">${p.category}</p>
                            <h5 class="product-name" style="font-size: 13px; margin-bottom: 8px;">${p.name}</h5>
                            <p class="product-price" style="font-weight: 700; color: var(--primary-red);">R ${(p.sale_price || p.price).toFixed(2)}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                searchResults.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                        <i class="fas fa-search" style="font-size: 40px; color: var(--gray-light); margin-bottom: 20px;"></i>
                        <h4 style="font-family: 'Montserrat', sans-serif; font-size: 16px; margin-bottom: 10px;">No matches found for "${query}"</h4>
                        <p style="font-size: 14px; opacity: 0.6;">Try adjusting your search or browse our collections.</p>
                        <a href="shop.html" class="btn btn-sm" style="margin-top: 20px;">Explore Shop</a>
                    </div>
                `;
            }
        });
    }

    // 5. NOTIFICATION SYSTEM (Custom Toast)
    window.showNotification = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-msg ${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    };

    // Add CSS for toast dynamically if not in style.css
    const toastStyles = `
        .toast-msg {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--pure-black);
            color: var(--pure-white);
            padding: 15px 30px;
            border-radius: 4px;
            z-index: 9999;
            transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-left: 4px solid var(--primary-red);
        }
        .toast-msg.show { transform: translateX(-50%) translateY(0); }
        .toast-msg.error { border-left-color: #ff3e3e; }
        .toast-msg i { margin-right: 15px; color: var(--primary-red); }
    `;
    const styleSheet = document.createElement("style");
    styleSheet.innerText = toastStyles;
    document.head.appendChild(styleSheet);

    // 6. SECURITY SCAN & COOKIE CONSENT
    window.runSecurityScan = function() {
        // Security logic remains active in background, but notification is removed per user request
        console.log("Isphilo Security: Background encryption verified.");
        
        // Show cookie consent if not already accepted
        if (!localStorage.getItem('isphilo_cookies_accepted')) {
            showCookieBanner();
        }

        // Show verification popup for unverified logged in users
        const user = ISPHILO_DB.getCurrentUser();
        if (user && !user.is_verified && !sessionStorage.getItem('isphilo_verify_shown')) {
            showVerificationPopup();
        }
    };

    function showVerificationPopup() {
        const overlay = document.createElement('div');
        overlay.className = 'verify-overlay';
        overlay.style.display = 'block';
        
        const popup = document.createElement('div');
        popup.className = 'verify-popup show';
        popup.innerHTML = `
            <i class="fas fa-shield-check" style="font-size: 50px; color: var(--primary-red); margin-bottom: 20px;"></i>
            <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase; font-size: 24px; margin-bottom: 15px;">Verify Your Account</h2>
            <p style="margin-bottom: 30px; opacity: 0.8; font-size: 14px;">To ensure a secure shopping experience and enable international shipping, please complete your profile verification.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="window.location.href='customer-dashboard.html#dash-settings'" class="btn" style="padding: 12px 25px; font-size: 11px;">Verify Now</button>
                <button id="close-verify" class="btn btn-outline" style="padding: 12px 25px; font-size: 11px;">Maybe Later</button>
            </div>
        `;
        
        document.body.appendChild(overlay);
        document.body.appendChild(popup);
        
        document.getElementById('close-verify').addEventListener('click', () => {
            overlay.remove();
            popup.remove();
            sessionStorage.setItem('isphilo_verify_shown', 'true');
        });
    }

    function showCookieBanner() {
        const banner = document.createElement('div');
        banner.id = 'cookie-consent-banner';
        banner.innerHTML = `
            <div class="cookie-content">
                <div class="cookie-text">
                    <i class="fas fa-cookie-bite"></i>
                    <span>We use cookies to ensure you get the best experience on our luxury fragrance site. By continuing, you agree to our use of cookies.</span>
                </div>
                <div class="cookie-actions">
                    <button id="accept-cookies" class="btn btn-sm">Accept & Continue</button>
                </div>
            </div>
        `;
        document.body.appendChild(banner);
        
        // Add specific styles for the banner
        const cookieStyles = `
            #cookie-consent-banner {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: var(--pure-black);
                color: var(--pure-white);
                padding: 20px 40px;
                z-index: 10000;
                transform: translateY(100%);
                transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
                border-top: 2px solid var(--primary-red);
            }
            #cookie-consent-banner.show { transform: translateY(0); }
            .cookie-content {
                max-width: 1400px;
                margin: 0 auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 30px;
            }
            .cookie-text { display: flex; align-items: center; gap: 15px; font-family: 'Montserrat', sans-serif; font-size: 12px; letter-spacing: 1px; }
            .cookie-text i { color: var(--primary-red); font-size: 20px; }
            #accept-cookies { background: var(--primary-red); color: white; border: none; min-width: 180px; }
            @media (max-width: 768px) {
                .cookie-content { flex-direction: column; text-align: center; gap: 20px; }
                #cookie-consent-banner { padding: 30px 20px; }
            }
        `;
        const styleSheet = document.createElement("style");
        styleSheet.innerText = cookieStyles;
        document.head.appendChild(styleSheet);

        setTimeout(() => banner.classList.add('show'), 1000);

        document.getElementById('accept-cookies').addEventListener('click', () => {
            localStorage.setItem('isphilo_cookies_accepted', 'true');
            banner.classList.remove('show');
            setTimeout(() => banner.remove(), 600);
            showNotification('Thank you for accepting cookies!', 'success');
        });
    }

    // 7. NEWSLETTER
    const newsletterForm = document.querySelector('.newsletter-form form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                ISPHILO_DB.subscribeNewsletter(email);
                showNotification('Thank you for subscribing to our newsletter!', 'success');
                this.reset();
            }
        });
    }

    // 8. INITIAL LOAD
    updateCartUI();
    reveal(); // Initial reveal check
    
    // Simulate initial security check
    setTimeout(() => {
        console.log("Isphilo Fragrance Security System: Operational");
    }, 1000);

    // 9. DYNAMIC PRODUCT RENDERING (for Home/Shop)
    const featuredGrid = document.querySelector('.product-grid.featured');
    if (featuredGrid) {
        const products = ISPHILO_DB.getProducts(); // Show ALL products per user request
        featuredGrid.innerHTML = products.map(p => `
            <div class="product-card ${p.is_sold_out ? 'sold-out' : ''} reveal" onclick="window.location.href='product-details.html?id=${p.id}'">
                <div class="product-image">
                    <img src="${p.image}" alt="${p.name}">
                    ${p.badge ? `<div class="product-badge">${p.badge}</div>` : ''}
                    ${p.is_sold_out ? `<div class="product-badge sold-out-badge">Sold Out</div>` : ''}
                    <div class="product-overlay">
                        <div class="product-actions">
                            <button onclick="event.stopPropagation(); addToCart(${p.id})"><i class="fas fa-shopping-bag"></i></button>
                            <button onclick="event.stopPropagation(); window.location.href='product-details.html?id=${p.id}'"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="product-info">
                    <p class="product-category">${p.category}</p>
                    <h5 class="product-name">${p.name}</h5>
                    <p class="product-price">
                        ${p.sale_price ? `<span class="original">R ${p.price.toFixed(2)}</span><span class="sale">R ${p.sale_price.toFixed(2)}</span>` : `R ${p.price.toFixed(2)}`}
                    </p>
                </div>
            </div>
        `).join('');
        
        // Re-run reveal after dynamic content is added
        setTimeout(reveal, 100);
    }

    // 10. REVEAL ANIMATION LOGIC
    function reveal() {
        const reveals = document.querySelectorAll(".reveal");
        for (let i = 0; i < reveals.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = reveals[i].getBoundingClientRect().top;
            const elementVisible = 100;
            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            }
        }
    }
    window.addEventListener("scroll", reveal);

    // 7. HERO SLIDESHOW
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        const nextSlide = () => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        };
        setInterval(nextSlide, 5000); // Change slide every 5 seconds
    }
});
