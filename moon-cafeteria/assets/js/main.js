/**
 * Main JavaScript
 * Moon Cafeteria Management System
 */

// Shopping Cart
let cart = [];

/**
 * Add product to cart
 */
function addToCart(productId, productName, price) {
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: parseFloat(price),
            quantity: 1
        });
    }

    updateCart();
    showNotification('Product added to cart', 'success');
}

/**
 * Remove product from cart
 */
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCart();
}

/**
 * Update product quantity in cart
 */
function updateQuantity(productId, quantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = parseInt(quantity);
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            updateCart();
        }
    }
}

/**
 * Update cart display
 */
function updateCart() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartCount = document.getElementById('cart-count');

    if (!cartItems) return;

    // Clear cart display
    cartItems.innerHTML = '';

    // Calculate total
    let total = 0;

    // Display cart items
    cart.forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;

        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div>
                <div class="product-name">${item.name}</div>
                <div class="product-price">$${item.price.toFixed(2)}</div>
            </div>
            <div class="d-flex align-center gap-1">
                <input type="number" 
                       value="${item.quantity}" 
                       min="1" 
                       class="form-control" 
                       style="width: 60px;"
                       onchange="updateQuantity(${item.id}, this.value)">
                <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">×</button>
            </div>
        `;
        cartItems.appendChild(cartItem);
    });

    // Update total
    if (cartTotal) {
        cartTotal.textContent = '$' + total.toFixed(2);
    }

    // Update cart count
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }

    // Store cart in session storage
    sessionStorage.setItem('cart', JSON.stringify(cart));
}

/**
 * Clear cart
 */
function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCart();
        showNotification('Cart cleared', 'info');
    }
}

/**
 * Checkout
 */
function checkout() {
    if (cart.length === 0) {
        showNotification('Cart is empty', 'warning');
        return;
    }

    const paymentMethod = document.getElementById('payment-method').value;

    // Send cart data to server
    fetch('process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart: cart,
            payment_method: paymentMethod
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order completed successfully!', 'success');
                cart = [];
                updateCart();

                // Redirect to receipt
                if (data.order_id) {
                    window.location.href = 'receipt.php?id=' + data.order_id;
                }
            } else {
                showNotification(data.message || 'Order failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '250px';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

/**
 * Confirm delete
 */
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

/**
 * Filter products by category
 */
function filterProducts(categoryId) {
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        if (categoryId === 'all' || product.dataset.category === categoryId) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

/**
 * Search products
 */
function searchProducts(query) {
    const products = document.querySelectorAll('.product-card');
    const searchTerm = query.toLowerCase();

    products.forEach(product => {
        const productName = product.querySelector('.product-name').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

/**
 * Toggle sidebar on mobile
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

/**
 * Print receipt
 */
function printReceipt() {
    window.print();
}

/**
 * Load cart from session storage
 */
function loadCart() {
    const savedCart = sessionStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCart();
    }
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function () {
    // Load cart
    loadCart();

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Add slideOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
document.head.appendChild(style);
