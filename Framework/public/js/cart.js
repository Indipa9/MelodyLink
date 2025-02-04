// cart.js
document.addEventListener('DOMContentLoaded', function() {
    // Get initial cart count
    const cartCountElement = document.getElementById('cartCount');
    let currentCount = parseInt(cartCountElement.textContent) || 0;

    // Function to update cart count
    function updateCartCount(newCount) {
        cartCountElement.textContent = newCount;
        cartCountElement.classList.add('pulse');
        setTimeout(() => {
            cartCountElement.classList.remove('pulse');
        }, 200);
    }

    // Function to add item to cart
    window.addToCart = async function(merchId) {
        if (!merchId) {
            showNotification('Invalid product selected.', 'error');
            return;
        }

        const button = document.querySelector(`button[data-merch-id="${merchId}"]`);
        if (button) {
            button.disabled = true; // Prevent double-clicks
        }

        try {
            const response = await fetch(`${URLROOT}/merchandise/addToCart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ merchId: merchId })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                currentCount++;
                updateCartCount(currentCount);
                showNotification('Item added to cart successfully!', 'success');
            } else {
                throw new Error(result.message || 'Failed to add item to cart.');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'Error occurred while adding to cart.', 'error');
        } finally {
            if (button) {
                button.disabled = false; // Re-enable the button
            }
        }
    };

    // Function to show notification
    function showNotification(message, type) {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => {
            document.body.removeChild(notification);
        });

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        // Style the notification
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '10px 20px';
        notification.style.borderRadius = '4px';
        notification.style.zIndex = '1000';
        notification.style.animation = 'slideIn 0.3s ease-out';
        notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
        notification.style.color = 'white';
        notification.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';

        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 0.2s ease-in-out;
        }

        .notification {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});

// Function to toggle cart visibility
function toggleCart() {
    // Implement cart toggle logic here
    window.location.href = `${URLROOT}/cart`;
}