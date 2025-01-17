<?php require APPROOT . '/views/inc/header1.php';?>

<div class="background-overlay"></div>

<nav class="navbar">
    <div class="nav-content">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold">MelodyLink Store</h1>
        </div>
        <div class="flex items-center gap-4">
            <?php if(!$data['isLoggedIn']): ?>
                <a href="<?php echo URLROOT; ?>/users/login" class="login-btn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                    </svg>
                    Login
                </a>
            <?php else: ?>
                <a href="<?php echo URLROOT; ?>/users/logout" class="login-btn">Logout</a>
            <?php endif; ?>
            <button class="cart-btn" onclick="toggleCart()">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-count" id="cartCount">
                    <?php echo isset($data['cartItems']) ? array_sum(array_column($data['cartItems'], 'quantity')) : 0; ?>
                </span>
                <span class="cart-tooltip">View Cart</span>
            </button>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="merch-grid">
        <?php if(isset($data['merchandise']) && is_array($data['merchandise'])): ?>
            <?php foreach ($data['merchandise'] as $item): ?>
                <div class="merch-card">
                    <div class="merch-image-container">
                        <img 
                            src="<?php echo URLROOT; ?>/assets/images/<?= htmlspecialchars($item->image ?? ''); ?>"
                            alt="<?= htmlspecialchars($item->Name ?? ''); ?>"
                            class="merch-image"
                            loading="lazy"
                        >
                        <div class="merch-image-overlay"></div>
                    </div>
                    <div class="merch-details">
                        <div class="merch-header">
                            <h2 class="merch-title"><?= htmlspecialchars($item->Name ?? ''); ?></h2>
                            <span class="merch-price">$<?= number_format($item->Price ?? 0, 2); ?></span>
                        </div>
                        <p class="merch-description"><?= htmlspecialchars($item->Description ?? ''); ?></p>
                        <button 
                            class="add-to-cart-btn"
                            data-merch-id="<?= $item->merch_id ?>"
                            onclick="addToCart(<?= $item->merch_id ?>)"
                            <?= !$data['isLoggedIn'] ? 'disabled' : '' ?>
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <?= !$data['isLoggedIn'] ? 'Login to Add' : 'Add to Cart' ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer class="footer">
    <div class="footer-content">
        <div>
            <h3>About MelodyLink</h3>
            <p>MelodyLink is an innovative web application designed to revolutionize the way we engage with music by offering a comprehensive, all-in-one platform for artists, fans, event organizers, merchandise vendors and event equipment renters.</p>
        </div>
        <div>
            <h3>Quick Links</h3>
            <ul class="quick-links">
                <li><a href="<?php echo URLROOT; ?>">Home</a></li>
                <li><a href="<?php echo URLROOT; ?>/music">Music</a></li>
                <li><a href="<?php echo URLROOT; ?>/artists">Artists</a></li>
                <li><a href="<?php echo URLROOT; ?>/events">Events</a></li>
                <li><a href="<?php echo URLROOT; ?>/store">Store</a></li>
            </ul>
        </div>
        <div>
            <h3>Follow Us</h3>
            <div class="social-icons">
                <a href="#" aria-label="Facebook">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Twitter">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Instagram">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                    </svg>
                </a>
                <a href="#" aria-label="YouTube">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';
</script>

<script src="<?php echo URLROOT; ?>/js/cart.js"></script>

