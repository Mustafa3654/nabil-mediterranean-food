<?php 
include "includes/connection.php"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch restaurant settings (cached)
$settings = get_settings();

// Get current category
$current_cat = isset($_GET['category']) ? $_GET['category'] : '';

// If no category selected, get the first one
if (empty($current_cat)) {
    $firstCatQuery = "SELECT cat_name FROM categories ORDER BY `Order` ASC LIMIT 1";
    $firstCatResult = $conn->query($firstCatQuery);
    if ($firstCatResult && $firstCatResult->num_rows > 0) {
        $firstCat = $firstCatResult->fetch_assoc();
        $current_cat = $firstCat['cat_name'];
    }
}
// Check if any item in the database has a photo
$globalPhotoCheck = $conn->query("SELECT 1 FROM items WHERE item_pic != '' LIMIT 1");
$globalHasPhoto = ($globalPhotoCheck && $globalPhotoCheck->num_rows > 0);
?>

<?php include 'includes/header.php' ?>

<!-- Custom Viewport for Menu Page (Zoomed Out) -->
<script>

    // Pass restaurant settings to JS
    window.restaurantPhone = "<?php echo htmlspecialchars(($settings['country_code'] ?? '') . ($settings['whatsapp_number'] ?? '')); ?>";
    window.orderMethod = "<?php echo htmlspecialchars($settings['order_method'] ?? 'whatsapp'); ?>";
    window.cartEnabled = <?php echo !isset($settings['show_cart']) ? 'true' : (((string)$settings['show_cart'] === '1') ? 'true' : 'false'); ?>;
</script>

<!-- External CSS for Menu -->
<link rel="stylesheet" href="assets/css/menu.css">

<section class="menu-header">
    <div class="menu-header-bg" style="background-image: url('<?php echo htmlspecialchars($settings['menu_bg'] ?? 'assets/images/admin/bgs/menu-bg.jpg'); ?>');"></div>
    <div class="menu-header-overlay"></div>
    <div class="container">
        <h1>Our Menu</h1>
        <p>Authentic flavors prepared with passion</p>
    </div>
</section>

<div class="category-tabs-wrapper">
    <div class="container">
        <div class="category-tabs">
            <?php
            $catSql = "SELECT * FROM categories ORDER BY `Order` ASC";
            $catResult = $conn->query($catSql);
            if ($catResult && $catResult->num_rows > 0) {
                while($cat = $catResult->fetch_assoc()) {
                    $isActive = ($current_cat == $cat['cat_name']) ? 'active' : '';
                    $iconSrc = !empty($cat['cat_icon']) && filter_var($cat['cat_icon'], FILTER_VALIDATE_URL) === false
                        ? htmlspecialchars($cat['cat_icon'])
                        : '';
                    $iconHtml = !empty($iconSrc) ? '<img src="'.$iconSrc.'" class="category-icon" alt="" loading="lazy" width="24" height="24">' : '';
                    echo '<a href="menu?category='.urlencode($cat['cat_name']).'" class="category-tab '.$isActive.'">'.$iconHtml.'<span>'.htmlspecialchars($cat['cat_name']).'</span></a>';
                }
            }
            ?>
        </div>
    </div>
</div>

<section class="menu-content">
    <div class="container">
        <?php
        // Fetch Category Footer Notes
        $bottomNote = '';
        if (!empty($current_cat)) {
            $catNoteStmt = $conn->prepare("SELECT cat_footer, cat_footer_bottom FROM categories WHERE cat_name = ?");
            $catNoteStmt->bind_param("s", $current_cat);
            $catNoteStmt->execute();
            $catNoteResult = $catNoteStmt->get_result();
            if ($catNoteRow = $catNoteResult->fetch_assoc()) {
                if (!empty($catNoteRow['cat_footer'])) {
                    echo '<div class="category-footer-note" style="margin-top: 0; margin-bottom: 30px;">';
                    echo '  <div class="footer-note-content">';
                    echo '    <i class="fas fa-leaf"></i>';
                    echo '    <span style="text-align: center; line-height: 1.4;">' . str_replace('.', '.<br>', htmlspecialchars($catNoteRow['cat_footer'])) . '</span>';
                    echo '    <i class="fas fa-leaf"></i>';
                    echo '  </div>';
                    echo '</div>';
                }
                if (!empty($catNoteRow['cat_footer_bottom'])) {
                    $bottomNote = $catNoteRow['cat_footer_bottom'];
                }
            }
            $catNoteStmt->close();
        }
        ?>
        <div class="menu-grid">
            <?php
            if (!empty($current_cat)) {
                // Use prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT * FROM items WHERE item_category = ? ORDER BY `Order` ASC");
                $stmt->bind_param("s", $current_cat);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $items = [];
                    while($item = $result->fetch_assoc()) {
                        $items[] = $item;
                    }

                    foreach($items as $item) {
                        ?>

                        <div class="menu-card"
                             data-id="<?php echo $item['item_id']; ?>"
                             data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                             data-category="<?php echo htmlspecialchars($item['item_category']); ?>"
                             data-price-usd="<?php echo $item['item_priceusd']; ?>"
                             data-price-suffix="<?php echo htmlspecialchars($item['price_suffix'] ?? ''); ?>">
                            <?php if (!empty($item['item_pic'])): ?>
                                <div onclick="openQuickView(this)" style="cursor: pointer; display: block;">
                                <div class="menu-card-img-container">
                                    <img src="<?php echo htmlspecialchars($item['item_pic']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" loading="lazy" class="menu-card-img" width="350" height="250">
                                </div>
                                </div>
                            <?php elseif ($globalHasPhoto): ?>
                                <div onclick="openQuickView(this)" style="cursor: pointer; display: block;">
                                <div class="menu-card-img-container placeholder-img">
                                    <img src="assets/images/items/placeholder-food.png" alt="No image" loading="lazy" class="menu-card-img" width="350" height="250">
                                </div>
                                </div>
                            <?php endif; ?>
                            <div class="menu-card-body">
                                <div onclick="openQuickView(this)" style="cursor: pointer; display: block;">
                                <h3 class="menu-card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                </div>
                                <?php if (!empty($item['Ingredients']) && $item['Ingredients'] !== '0'): ?>
                                    <p class="menu-card-desc"><?php echo htmlspecialchars($item['Ingredients']); ?></p>
                                <?php endif; ?>
                                <div class="menu-card-price">
                                    <?php 
                                    $usd_price = $item['item_priceusd'] > 0 ? '$' . number_format($item['item_priceusd'], 2) : '';
                                    if (!empty($item['price_suffix'])) {
                                        $usd_price .= ' <small>' . htmlspecialchars($item['price_suffix']) . '</small>';
                                    }
                                    ?>
                                    <span class="price-usd"><?php echo $usd_price; ?></span>
                                </div>
                                <?php if (!isset($settings['show_cart']) || (string)$settings['show_cart'] === '1'): ?>
                                    <div class="cart-controls" onclick="event.stopPropagation();">
                                        <!-- Buttons injected by JS -->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                    echo '<div class="col-12 no-items"><h3>No items found in this category.</h3></div>';
                }
                $stmt->close();
            } else {
                echo '<div class="col-12 no-items"><h3>Please select a category.</h3></div>';
            }
            ?>
        </div>
        <?php
        if (!empty($bottomNote)) {
            echo '<div class="category-footer-note" style="margin-top: 40px; margin-bottom: 0;">';
            echo '  <div class="footer-note-content">';
            echo '    <i class="fas fa-leaf"></i>';
            echo '    <span style="text-align: center; line-height: 1.4;">' . str_replace('.', '.<br>', htmlspecialchars($bottomNote)) . '</span>';
            echo '    <i class="fas fa-leaf"></i>';
            echo '  </div>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php if (!isset($settings['show_cart']) || (string)$settings['show_cart'] === '1'): ?>
    <!-- Cart Modal -->
    <div id="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-header">
                <h2>Your Order</h2>
                <button class="close-cart" onclick="cart.closeCart()"><i class="fas fa-times"></i></button>
            </div>
            <div id="cart-items-container">
                <!-- Items injected by JS -->
            </div>
            <div id="cart-totals"></div>
            
            <!-- Customer Details Section -->
            <div id="customer-details" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                <div class="form-group mb-2">
                    <input type="text" id="customer-name" placeholder="Your Name" class="cart-input">
                </div>
                <div class="form-group mb-2">
                    <input type="tel" id="customer-phone" placeholder="Phone Number" class="cart-input">
                </div>
                <div class="form-group mb-3">
                    <textarea id="customer-note" placeholder="Special Instructions / Notes (Optional)" class="cart-input" rows="2" style="resize: none;"></textarea>
                </div>
                <div class="form-group mb-2">
                    <select id="order-time-choice" class="cart-input" onchange="document.getElementById('customer-time-wrap').style.display = this.value === 'scheduled' ? 'block' : 'none';">
                        <option value="asap">As soon as possible</option>
                        <option value="scheduled">Schedule for a specific time</option>
                    </select>
                </div>
                <div class="form-group mb-3" id="customer-time-wrap" style="display:none;">
                    <input type="time" id="customer-time" class="cart-input">
                </div>
            </div>

            <button class="checkout-btn" onclick="cart.checkout()" id="main-checkout-btn">
                <i id="checkout-btn-icon" class="fab fa-whatsapp"></i> <span id="checkout-btn-text">Order on WhatsApp</span>
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Quick View Modal -->
<div id="quickview-modal" class="modal-overlay" style="display: none;">
    <div class="quickview-content">
        <button class="close-quickview" onclick="closeQuickView()"><i class="fas fa-times"></i></button>
        <div class="quickview-img-container" id="qv-img-container">
            <img id="qv-img" src="" alt="Item Image">
        </div>
        <div class="quickview-body">
            <h2 id="qv-title"></h2>
            <div class="menu-card-price">
                <span id="qv-price-usd" class="price-usd"></span>
                <span id="qv-price-suffix" style="font-size: 0.8em; color: var(--text-muted);"></span>
            </div>
            <p id="qv-ingredients" class="menu-card-desc"></p>
        </div>
    </div>
</div>

<!-- External JS for Menu -->
<script src="assets/js/menu.js?v=1.1"></script>
<script src="assets/js/cart.js?v=1.1"></script>

<?php include 'includes/footer.php' ?>


