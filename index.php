<?php 
include "includes/connection.php"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Fetch restaurant settings (cached)
$settings = get_settings();

$mapLink = $settings['restaurant_maps'] ?? '#';
?>

<?php include 'includes/header.php' ?>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- External CSS for Index -->
<link rel="stylesheet" href="assets/css/index.css">
<link rel="stylesheet" href="assets/css/about.css">

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg" style="background-image: url('<?php echo htmlspecialchars($settings['home_bg'] ?? 'assets/images/admin/bgs/hero-bg.jpg'); ?>');"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">Welcome to <span><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Our Kitchen'); ?></span></h1>
        <p class="hero-description">
            <?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Experience the finest Lebanese flavors, crafted with tradition and served with love.'); ?>
        </p>
        <div class="hero-btns">
            <a href="menu" class="btn-hero-primary">View Our Menu</a>
        </div>
    </div>
</section>

<!-- Dark Green Banner (3 custom texts) -->
<?php if (!empty($settings['banner1_visible'])): ?>
<section class="green-banner-section">
    <div class="container">
        <div class="green-banner">
            <!-- Left Item -->
            <div class="banner-item">
                <i class="fas fa-map-marker-alt"></i>
                <span class="banner-text uppercase-text"><?php echo htmlspecialchars($settings['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL'); ?></span>
            </div>
            
            <div class="banner-separator"></div>
            
            <!-- Middle Item -->
            <div class="banner-item middle-item">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" style="transform: scaleX(-1);">
                    <path d="M12,2C11.5,4 9,6 6,7C6.5,5.5 8,3.5 10,2.5C10.7,2.2 11.4,2 12,2M12,2C12.5,4 15,6 18,7C17.5,5.5 16,3.5 14,2.5C13.3,2.2 12.6,2 12,2M12,2V22M12,6C10.5,7.5 8.5,8.5 6,9C7,7.5 9,6.5 11,6C11.3,6 11.7,6 12,6M12,6C13.5,7.5 15.5,8.5 18,9C17,7.5 15,6.5 13,6C12.7,6 12.3,6 12,6M12,11C10,12.5 7.5,13.5 5,14C6.5,12.5 8.5,11.5 11,11C11.3,11 11.7,11 12,11M12,11C14,12.5 16.5,13.5 19,14C17.5,12.5 15.5,11.5 13,11C12.7,11 12.3,11 12,11M12,16C9.5,17.5 7,18 4.5,18C6.5,16.5 8.5,16 11,16C11.3,16 11.7,16 12,16M12,16C14.5,17.5 17,18 19.5,18C17.5,16.5 15.5,16 13,16C12.7,16 12.3,16 12,16"/>
                </svg>
                <span class="banner-text cursive-text"><?php echo htmlspecialchars($settings['banner1_t2'] ?? 'Made with fresh ingredients & lots of love'); ?></span>
                <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
                    <path d="M12,2C11.5,4 9,6 6,7C6.5,5.5 8,3.5 10,2.5C10.7,2.2 11.4,2 12,2M12,2C12.5,4 15,6 18,7C17.5,5.5 16,3.5 14,2.5C13.3,2.2 12.6,2 12,2M12,2V22M12,6C10.5,7.5 8.5,8.5 6,9C7,7.5 9,6.5 11,6C11.3,6 11.7,6 12,6M12,6C13.5,7.5 15.5,8.5 18,9C17,7.5 15,6.5 13,6C12.7,6 12.3,6 12,6M12,11C10,12.5 7.5,13.5 5,14C6.5,12.5 8.5,11.5 11,11C11.3,11 11.7,11 12,11M12,11C14,12.5 16.5,13.5 19,14C17.5,12.5 15.5,11.5 13,11C12.7,11 12.3,11 12,11M12,16C9.5,17.5 7,18 4.5,18C6.5,16.5 8.5,16 11,16C11.3,16 11.7,16 12,16M12,16C14.5,17.5 17,18 19.5,18C17.5,16.5 15.5,16 13,16C12.7,16 12.3,16 12,16"/>
                </svg>
            </div>
            
            <div class="banner-separator"></div>
            
            <!-- Right Item -->
            <div class="banner-item">
                <i class="fas fa-leaf"></i>
                <span class="banner-text uppercase-text"><?php echo htmlspecialchars($settings['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR'); ?></span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$galleryRes = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6");
$hasPhotos = ($galleryRes && $galleryRes->num_rows > 0);
if ($hasPhotos): 
?>
<!-- Gallery Section -->
<section class="gallery-section">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <br>
            <h2 class="gallery-title" style="font-family: 'Poppins', sans-serif; font-size: 42px; font-weight: 800; color: var(--text-color);">The <span style="color: var(--accent-blue);">Vibe</span></h2>
            <p style="color: var(--text-muted); font-size: 18px;">A glimpse into our kitchen and atmosphere.</p>
        </div>
        <!-- Swiper Container -->
        <div class="swiper gallery-slider">
            <div class="swiper-wrapper">
                <?php
                while ($gRow = $galleryRes->fetch_assoc()) {
                    echo '<div class="swiper-slide gallery-slide">';
                    echo '    <img src="' . htmlspecialchars($gRow['photo_path']) . '" alt="Restaurant Vibe" loading="lazy" width="600" height="450">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Cream Banner (4 custom texts) -->
<?php if (!empty($settings['banner2_visible'])): ?>
<section class="cream-banner-section">
    <div class="container">
        <div class="cream-banner">
            <!-- Item 1 -->
            <div class="cream-banner-item">
                <i class="fas fa-seedling"></i>
                <span class="cream-banner-text"><?php echo htmlspecialchars($settings['banner2_t1'] ?? 'FRESH INGREDIENTS'); ?></span>
            </div>
            
            <div class="cream-banner-separator"></div>
            
            <!-- Item 2 -->
            <div class="cream-banner-item">
                <svg viewBox="0 0 512 512" width="24" height="24" fill="currentColor">
                    <path d="M256 32c-66.3 0-120 53.7-120 120 0 11.2 1.5 22.1 4.5 32.4C71.3 194.7 16 252.1 16 320c0 70.7 57.3 128 128 128h240c70.7 0 128-57.3 128-128 0-67.9-55.3-125.3-124.5-135.6 3-10.3 4.5-21.2 4.5-32.4 0-66.3-53.7-120-120-120zM176 384c-8.8 0-16-7.2-16-16s7.2-16 16-16h160c8.8 0 16 7.2 16 16s-7.2 16-16 16H176z"/>
                </svg>
                <span class="cream-banner-text"><?php echo htmlspecialchars($settings['banner2_t2'] ?? 'MADE DAILY'); ?></span>
            </div>
            
            <div class="cream-banner-separator"></div>
            
            <!-- Item 3 -->
            <div class="cream-banner-item">
                <i class="fas fa-leaf"></i>
                <span class="cream-banner-text"><?php echo htmlspecialchars($settings['banner2_t3'] ?? 'AUTHENTIC RECIPES'); ?></span>
            </div>
            
            <div class="cream-banner-separator"></div>
            
            <!-- Item 4 -->
            <div class="cream-banner-item">
                <i class="fas fa-heart"></i>
                <span class="cream-banner-text"><?php echo htmlspecialchars($settings['banner2_t4'] ?? 'MADE WITH LOVE'); ?></span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>



<!-- Combined Info Section -->
<section class="combined-info-section">
    <div class="container">
        <div class="info-grid">
            <!-- Open Daily Box -->
            <div class="info-box">
                <div class="info-box-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo htmlspecialchars($settings['opening_title'] ?? 'Open Daily'); ?></h3>
                <p>We are ready to serve you every single day. Join us for an unforgettable dining experience.</p>
                <div class="highlight"><?php echo htmlspecialchars($settings['opening_hours'] ?? '12:00 PM - 11:00 PM'); ?></div>
            </div>

            <!-- Location Box -->
            <a href="<?php echo htmlspecialchars($mapLink); ?>" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit; width: 100%;">
                <div class="info-box alt">
                    <div class="info-box-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Our Location</h3>
                    <p>Find us in the heart of the city. We're easily accessible and waiting to welcome you.</p>
                    <div class="highlight">
                        <?php echo htmlspecialchars($settings['restaurant_address'] ?? '#'); ?>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>



<!-- External JS for Index -->
<script src="assets/js/index.js"></script>

<?php include 'includes/footer.php' ?>


