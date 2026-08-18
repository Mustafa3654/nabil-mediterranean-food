<?php
include "includes/connection.php";

start_secure_session();

// Fetch restaurant settings (cached)
$settings = get_settings();
$restaurantName = $settings['restaurant_name'] ?? 'Our Kitchen';
?>

<?php include 'includes/header.php'; ?>

<!-- External CSS for About -->
<link rel="stylesheet" href="assets/css/about.css">

<!-- Hero Section -->
<section class="hero-section contact-hero-section about-hero-section" style="background-image: url('<?php echo htmlspecialchars($settings['about_bg'] ?? 'assets/images/admin/bgs/hero-bg.jpg'); ?>');">
    <div class="hero-content">
        <h1 class="hero-title reveal-text">Our Story</h1>
        <p class="hero-subtitle reveal-text">Tradition, Passion, and Exquisite Mediterranean Flavors</p>
    </div>
</section>

<!-- Our Legacy Detailed Section -->
<?php
$aboutTitle = $settings['about_title'] ?? 'Flavors Crafted With Heritage & Love';
$words = explode(' ', $aboutTitle);
if (count($words) > 2) {
    $lastWords = implode(' ', array_splice($words, -2));
    $displayTitle = implode(' ', $words) . ' <span>' . htmlspecialchars($lastWords) . '</span>';
} else {
    $displayTitle = '<span>' . htmlspecialchars($aboutTitle) . '</span>';
}
?>
<section class="about-preview-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="about-image-container">
                    <div class="about-image-wrapper">
                        <img src="<?php echo htmlspecialchars($settings['about_image'] ?? 'assets/images/admin/bgs/about_story.png'); ?>" alt="Our Legacy" loading="lazy" class="about-img img-fluid" width="600" height="450">
                        <div class="about-experience-badge">
                            <span class="badge-num"><?php echo htmlspecialchars($settings['about_years'] ?? '15+'); ?></span>
                            <span class="badge-text"><?php echo htmlspecialchars($settings['about_years_label'] ?? 'Years of Tradition'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-text-content">
                    <span class="about-subtitle"><?php echo htmlspecialchars($settings['about_subtitle'] ?? 'Our Legacy'); ?></span>
                    <h2 class="about-title"><?php echo $displayTitle; ?></h2>
                    <p class="about-desc">
                        <?php echo nl2br(htmlspecialchars($settings['about_desc1'] ?? 'Since our establishment, we have dedicated ourselves to offering the finest Mediterranean and Lebanese culinary experiences. Every dish that leaves our kitchen is prepared using authentic, time-honored family recipes passed down through generations.')); ?>
                    </p>
                    <?php if (!empty($settings['about_desc2'])): ?>
                    <p class="about-desc">
                        <?php echo nl2br(htmlspecialchars($settings['about_desc2'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="about-values-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="about-subtitle"><?php echo htmlspecialchars($settings['values_subtitle'] ?? 'Our Principles'); ?></span>
            <h2 class="about-title" style="margin-bottom: 10px;"><?php echo htmlspecialchars($settings['values_title'] ?? 'What We Stand For'); ?></h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;"><?php echo nl2br(htmlspecialchars($settings['values_desc'] ?? 'Our commitment to authenticity and excellence shapes everything we do in our kitchen.')); ?></p>
        </div>
        
        <div class="values-grid">
            <!-- Value 1 -->
            <div class="value-card">
                <div class="value-icon">
                    <i class="<?php echo htmlspecialchars($settings['value1_icon'] ?? 'fas fa-seedling'); ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($settings['value1_title'] ?? '100% Fresh Daily'); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($settings['value1_desc'] ?? 'We source the freshest local vegetables, premium meats, and hand-picked herbs every morning to ensure quality you can taste.')); ?></p>
            </div>
            
            <!-- Value 2 -->
            <div class="value-card">
                <div class="value-icon">
                    <i class="<?php echo htmlspecialchars($settings['value2_icon'] ?? 'fas fa-scroll'); ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($settings['value2_title'] ?? 'Authentic Recipes'); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($settings['value2_desc'] ?? 'Our dishes are prepared using traditional Lebanese and Mediterranean methods, honoring culinary secrets preserved for decades.')); ?></p>
            </div>
            
            <!-- Value 3 -->
            <div class="value-card">
                <div class="value-icon">
                    <i class="<?php echo htmlspecialchars($settings['value3_icon'] ?? 'fas fa-heart'); ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($settings['value3_title'] ?? 'Prepared With Love'); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($settings['value3_desc'] ?? 'We believe that food should warm the soul. Every meal is cooked with the same passion and dedication as if it were for our own family.')); ?></p>
            </div>
            
            <!-- Value 4 -->
            <div class="value-card">
                <div class="value-icon">
                    <i class="<?php echo htmlspecialchars($settings['value4_icon'] ?? 'fas fa-hands-helping'); ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($settings['value4_title'] ?? 'Warm Hospitality'); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($settings['value4_desc'] ?? 'To us, every guest is family. We welcome you with open arms and strive to make your dining experience comfortable and memorable.')); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Chef Showcase Section -->
<section class="chef-showcase-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 order-lg-2">
                <div class="chef-img-wrapper">
                    <img src="<?php echo htmlspecialchars($settings['about_chef_image'] ?? 'assets/images/admin/bgs/about_chef.png'); ?>" alt="Our Chef in Action" loading="lazy" class="chef-img img-fluid" width="600" height="750">
                    <span class="chef-badge">Culinary Master</span>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="chef-details">
                    <span class="about-subtitle">Behind the Flavors</span>
                    <h3><?php echo htmlspecialchars($settings['about_chef_title'] ?? 'The Passion Behind the Plate'); ?></h3>
                    <span class="chef-title"><?php echo htmlspecialchars($settings['about_chef_subtitle'] ?? 'Handcrafted Culinary Artistry'); ?></span>
                    <p class="about-desc">
                        <?php echo nl2br(htmlspecialchars($settings['about_chef_bio1'] ?? 'Our culinary team is dedicated to preserving the absolute essence of traditional Mediterranean cuisine while incorporating modern culinary techniques. Under expert eyes, each flatbread is rolled, each spice is measured, and each grill is monitored to perfection.')); ?>
                    </p>
                    <?php if (!empty($settings['about_chef_bio2'])): ?>
                    <p class="about-desc">
                        <?php echo nl2br(htmlspecialchars($settings['about_chef_bio2'])); ?>
                    </p>
                    <?php endif; ?>
                    <span class="chef-signature"><?php echo htmlspecialchars($settings['about_chef_name'] ?? 'Nabil'); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="about-values-section text-center" style="padding: 80px 0; background: var(--card-bg);">
    <div class="container">
        <h2 class="about-title" style="margin-bottom: 20px;">Ready to Taste Tradition?</h2>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 35px auto;">Browse our diverse menu featuring savory wraps, fresh pies, dynamic combos, and mouth-watering appetizers.</p>
        <a href="menu" class="btn-about-more" style="padding: 16px 45px;">Explore Our Menu <i class="fas fa-utensils ms-2"></i></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
