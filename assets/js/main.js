// OVARALL E-Commerce JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Header scroll effect
    const header = document.querySelector('.main-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Quantity controls
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        const minusBtn = input.parentElement.querySelector('.qty-minus');
        const plusBtn = input.parentElement.querySelector('.qty-plus');
        
        if (minusBtn) {
            minusBtn.addEventListener('click', function() {
                let value = parseInt(input.value) || 1;
                if (value > 1) {
                    input.value = value - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
        
        if (plusBtn) {
            plusBtn.addEventListener('click', function() {
                let value = parseInt(input.value) || 1;
                let max = parseInt(input.getAttribute('max')) || 99;
                if (value < max) {
                    input.value = value + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });

    // Product image gallery
    const mainImage = document.querySelector('.product-main-image');
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const newSrc = this.getAttribute('data-image');
                mainImage.src = newSrc;
                
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    // Size selection
    const sizeOptions = document.querySelectorAll('.size-option');
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            sizeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            
            const sizeInput = document.querySelector('input[name="size"]');
            if (sizeInput) {
                sizeInput.value = this.getAttribute('data-size');
            }
        });
    });

    // Color selection
    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            colorOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            
            const colorInput = document.querySelector('input[name="color"]');
            if (colorInput) {
                colorInput.value = this.getAttribute('data-color');
            }
        });
    });

    // Payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // Price range slider
    const priceRange = document.getElementById('priceRange');
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    
    if (priceRange && priceMin && priceMax) {
        priceRange.addEventListener('input', function() {
            priceMax.textContent = this.value;
        });
    }

    // Add to cart AJAX
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            const quantity = document.querySelector('.quantity-input')?.value || 1;
            const size = document.querySelector('input[name="size"]')?.value || '';
            const color = document.querySelector('input[name="color"]')?.value || '';
            
            fetch('ajax/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&quantity=${quantity}&size=${size}&color=${color}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        cartCount.style.display = 'inline-flex';
                    }
                    
                    // Show success message
                    showToast('Product added to cart!', 'success');
                } else {
                    showToast(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding to cart', 'error');
            });
        });
    });

    // Wishlist toggle
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-product-id');
            const icon = this.querySelector('i');
            
            fetch('ajax/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        showToast('Added to wishlist!', 'success');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        showToast('Removed from wishlist!', 'success');
                    }
                } else {
                    showToast(data.message || 'Please login first', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'error');
            }
        });
    });

    // Image lazy loading
    const lazyImages = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Cart update function
function updateCart(productId, quantity) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Remove from cart
function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('ajax/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Apply coupon
function applyCoupon() {
    const code = document.getElementById('couponCode')?.value;
    if (!code) {
        alert('Please enter a coupon code');
        return;
    }
    
    fetch('ajax/coupon.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `code=${code}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Coupon applied!');
            location.reload();
        } else {
            alert(data.message || 'Invalid coupon code');
        }
    })
    .catch(error => console.error('Error:', error));
}
