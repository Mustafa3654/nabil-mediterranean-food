<?php
include "../includes/connection.php";
include "../includes/auth.php";
include "../includes/webp_helper.php";
start_secure_session();
require_admin();
check_session_timeout(30);

// -------------------------
// Load current settings (cached)
// -------------------------
$settings = get_settings();

/**
 * Reuse the existing path unless a new file is uploaded successfully.
 * Automatically converts uploaded images to WebP format.
 */
function uploadSettingsImage(string $fieldName, string $prefix, string $currentPath): string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== 0) {
        return $currentPath;
    }

    $result_path = process_upload_to_webp(
        $_FILES[$fieldName]['tmp_name'],
        $_FILES[$fieldName]['name'],
        '../assets/images/admin/bgs/',
        $prefix,
        'assets/images/admin/bgs/'
    );

    if ($result_path !== false) {
        // Delete old image if it exists and is different
        if (!empty($currentPath) && file_exists('../' . $currentPath)) {
            @unlink('../' . $currentPath);
        }
        return $result_path;
    }

    return $currentPath;
}

$message = "";

// Handle Gallery Upload
if (isset($_POST['upload_gallery'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token.</div>";
    } else {
        if (isset($_FILES['photos'])) {
            $total = count($_FILES['photos']['name']);
            $success_count = 0;
            $error_count = 0;

            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['photos']['error'][$i] === 0) {
                    $result_path = process_upload_to_webp(
                        $_FILES['photos']['tmp_name'][$i],
                        $_FILES['photos']['name'][$i],
                        '../assets/images/admin/pics/',
                        'VIBE',
                        'assets/images/admin/pics/'
                    );

                    if ($result_path !== false) {
                        $stmt = $conn->prepare("INSERT INTO gallery (photo_path) VALUES (?)");
                        $stmt->bind_param("s", $result_path);
                        $stmt->execute();
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
            if ($success_count > 0) {
                $message = "<div class='alert alert-success'>$success_count photo(s) added to gallery!</div>";
                if ($error_count > 0) {
                    $message .= "<div class='alert alert-warning'>$error_count photo(s) failed to upload (invalid type or error).</div>";
                }
            } else if ($error_count > 0) {
                $message = "<div class='alert alert-danger'>Failed to upload photos. Invalid file types or errors.</div>";
            }
        }
    }
}

// Handle Gallery Delete (POST only, CSRF-protected)
if (isset($_POST['delete_gallery'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token.</div>";
    } else {
        $id = (int)$_POST['delete_gallery'];
        $stmt = $conn->prepare("SELECT photo_path FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $real_path = '../' . $row['photo_path'];
            if (file_exists($real_path)) unlink($real_path);
            $delStmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $delStmt->bind_param("i", $id);
            $delStmt->execute();
            $message = "<div class='alert alert-success'>Photo deleted.</div>";
        }
    }
}

// -------------------------
// Handle form submission
// -------------------------
if (isset($_POST['update_settings'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = "<div class='alert alert-danger'>Invalid request token. Please refresh and try again.</div>";
    } else {
        // Basic fields
        $name = trim($_POST['restaurant_name'] ?? '');

        $phone = trim($_POST['restaurant_phone'] ?? '');
        $address = trim($_POST['restaurant_address'] ?? '');
        $maps = trim($_POST['restaurant_maps'] ?? '');
        $desc = trim($_POST['restaurant_description'] ?? '');
        $hours = trim($_POST['opening_hours'] ?? '');
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $insta = trim($_POST['instagram_url'] ?? '');
        $fb = trim($_POST['facebook_url'] ?? '');
        $opening_title = trim($_POST['opening_title'] ?? '');
        $email = trim($_POST['restaurant_email'] ?? '');
        $country_code = trim($_POST['country_code'] ?? '+1');
        $order_method = trim($_POST['order_method'] ?? 'whatsapp');
        $show_cart = isset($_POST['show_cart']) ? '1' : '0';
        $notify_contact_telegram = isset($_POST['notify_contact_telegram']) ? '1' : '0';

        // Banners
        $banner1_visible = ($_POST['banner1_visible'] ?? '0') === '1' ? '1' : '0';
        $banner1_t1 = trim($_POST['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL');
        $banner1_t2 = trim($_POST['banner1_t2'] ?? 'Made with fresh ingredients & lots of love');
        $banner1_t3 = trim($_POST['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR');

        $banner2_visible = ($_POST['banner2_visible'] ?? '0') === '1' ? '1' : '0';
        $banner2_t1 = trim($_POST['banner2_t1'] ?? 'FRESH INGREDIENTS');
        $banner2_t2 = trim($_POST['banner2_t2'] ?? 'MADE DAILY');
        $banner2_t3 = trim($_POST['banner2_t3'] ?? 'AUTHENTIC RECIPES');
        $banner2_t4 = trim($_POST['banner2_t4'] ?? 'MADE WITH LOVE');

        // About section fields
        $about_title = trim($_POST['about_title'] ?? 'Flavors Crafted With Heritage & Love');
        $about_subtitle = trim($_POST['about_subtitle'] ?? 'Our Legacy');
        $about_desc1 = trim($_POST['about_desc1'] ?? '');
        $about_desc2 = trim($_POST['about_desc2'] ?? '');
        $about_chef_title = trim($_POST['about_chef_title'] ?? 'The Passion Behind the Plate');
        $about_chef_subtitle = trim($_POST['about_chef_subtitle'] ?? 'Handcrafted Culinary Artistry');
        $about_chef_name = trim($_POST['about_chef_name'] ?? 'Nabil');
        $about_chef_bio1 = trim($_POST['about_chef_bio1'] ?? '');
        $about_chef_bio2 = trim($_POST['about_chef_bio2'] ?? '');
        $about_years = trim($_POST['about_years'] ?? '15+');
        $about_years_label = trim($_POST['about_years_label'] ?? 'Years of Tradition');

        // Core Values fields
        $values_title = trim($_POST['values_title'] ?? 'What We Stand For');
        $values_subtitle = trim($_POST['values_subtitle'] ?? 'Our Principles');
        $values_desc = trim($_POST['values_desc'] ?? '');
        $value1_icon = trim($_POST['value1_icon'] ?? 'fas fa-seedling');
        $value1_title = trim($_POST['value1_title'] ?? '100% Fresh Daily');
        $value1_desc = trim($_POST['value1_desc'] ?? '');
        $value2_icon = trim($_POST['value2_icon'] ?? 'fas fa-scroll');
        $value2_title = trim($_POST['value2_title'] ?? 'Authentic Recipes');
        $value2_desc = trim($_POST['value2_desc'] ?? '');
        $value3_icon = trim($_POST['value3_icon'] ?? 'fas fa-heart');
        $value3_title = trim($_POST['value3_title'] ?? 'Prepared With Love');
        $value3_desc = trim($_POST['value3_desc'] ?? '');
        $value4_icon = trim($_POST['value4_icon'] ?? 'fas fa-hands-helping');
        $value4_title = trim($_POST['value4_title'] ?? 'Warm Hospitality');
        $value4_desc = trim($_POST['value4_desc'] ?? '');

        // Telegram fields
        $chat_id = trim($_POST['chat_id'] ?? '');
        $bot_token = trim($_POST['bot_token'] ?? '');

        // AI fields
        $deepseek_api_key = trim($_POST['deepseek_api_key'] ?? '');
        $deepseek_model = trim($_POST['deepseek_model'] ?? 'deepseek-v4-flash');
        $allowedModels = ['deepseek-v4-pro', 'deepseek-v4-flash', 'deepseek-reasoner', 'deepseek-chat'];
        if (!in_array($deepseek_model, $allowedModels)) {
            $deepseek_model = 'deepseek-v4-flash';
        }

        // Image fields
        $logo_path = $settings['restaurant_logo'] ?? '';
        $home_bg_path = $settings['home_bg'] ?? '';
        $menu_bg_path = $settings['menu_bg'] ?? '';
        $contact_bg_path = $settings['contact_bg'] ?? '';
        $about_image_path = $settings['about_image'] ?? 'assets/images/admin/bgs/about_story.png';
        $about_chef_image_path = $settings['about_chef_image'] ?? 'assets/images/admin/bgs/about_chef.png';
        $about_bg_path = $settings['about_bg'] ?? 'assets/images/admin/bgs/hero-bg.jpg';

        $logo_path = uploadSettingsImage('restaurant_logo', 'logo', $logo_path);
        $home_bg_path = uploadSettingsImage('home_bg', 'home', $home_bg_path);
        $menu_bg_path = uploadSettingsImage('menu_bg', 'menu', $menu_bg_path);
        $contact_bg_path = uploadSettingsImage('contact_bg', 'contact', $contact_bg_path);
        $about_image_path = uploadSettingsImage('about_image', 'about', $about_image_path);
        $about_chef_image_path = uploadSettingsImage('about_chef_image', 'about_chef', $about_chef_image_path);
        $about_bg_path = uploadSettingsImage('about_bg', 'about_bg', $about_bg_path);

        // Persist settings
        if ($settings) {
            $stmt = $conn->prepare("UPDATE settings SET 
                restaurant_name = ?, 
                restaurant_logo = ?, 
                home_bg = ?,
                menu_bg = ?,
                contact_bg = ?,
                restaurant_phone = ?, 
                restaurant_email = ?,
                restaurant_address = ?,
                restaurant_maps = ?,
                restaurant_description = ?, 
                opening_hours = ?, 
                whatsapp_number = ?, 
                instagram_url = ?, 
                facebook_url = ?,
                opening_title = ?,
                country_code = ?,
                order_method = ?,
                show_cart = ?,
                notify_contact_telegram = ?,
                banner1_visible = ?,
                banner2_visible = ?,
                banner1_t1 = ?,
                banner1_t2 = ?,
                banner1_t3 = ?,
                banner2_t1 = ?,
                banner2_t2 = ?,
                banner2_t3 = ?,
                banner2_t4 = ?,
                about_title = ?,
                about_subtitle = ?,
                about_desc1 = ?,
                about_desc2 = ?,
                about_image = ?,
                about_chef_image = ?,
                about_chef_title = ?,
                about_chef_subtitle = ?,
                about_chef_name = ?,
                about_chef_bio1 = ?,
                about_chef_bio2 = ?,
                about_years = ?,
                about_years_label = ?,
                about_bg = ?,
                chat_id = ?,
                bot_token = ?,
                deepseek_api_key = ?,
                deepseek_model = ?,
                values_title = ?,
                values_subtitle = ?,
                values_desc = ?,
                value1_icon = ?,
                value1_title = ?,
                value1_desc = ?,
                value2_icon = ?,
                value2_title = ?,
                value2_desc = ?,
                value3_icon = ?,
                value3_title = ?,
                value3_desc = ?,
                value4_icon = ?,
                value4_title = ?,
                value4_desc = ?
                WHERE id = ?");
            $stmt->bind_param(str_repeat("s", 61) . "i", 
                $name, $logo_path, $home_bg_path, $menu_bg_path, $contact_bg_path, 
                $phone, $email, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, 
                $opening_title, $country_code, $order_method, $show_cart, $notify_contact_telegram,
                $banner1_visible, $banner2_visible,
                $banner1_t1, $banner1_t2, $banner1_t3,
                $banner2_t1, $banner2_t2, $banner2_t3, $banner2_t4,
                $about_title, $about_subtitle, $about_desc1, $about_desc2,
                $about_image_path, $about_chef_image_path,
                $about_chef_title, $about_chef_subtitle, $about_chef_name,
                $about_chef_bio1, $about_chef_bio2,
                $about_years, $about_years_label,
                $about_bg_path,
                $chat_id, $bot_token, $deepseek_api_key, $deepseek_model,
                $values_title, $values_subtitle, $values_desc,
                $value1_icon, $value1_title, $value1_desc,
                $value2_icon, $value2_title, $value2_desc,
                $value3_icon, $value3_title, $value3_desc,
                $value4_icon, $value4_title, $value4_desc,
                $settings['id']
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO settings (restaurant_name, restaurant_logo, home_bg, menu_bg, contact_bg, restaurant_phone, restaurant_email, restaurant_address, restaurant_maps, restaurant_description, opening_hours, whatsapp_number, instagram_url, facebook_url, opening_title, country_code, order_method, show_cart, banner1_t1, banner1_t2, banner1_t3, banner2_t1, banner2_t2, banner2_t3, banner2_t4, about_title, about_subtitle, about_desc1, about_desc2, about_image, about_chef_image, about_chef_title, about_chef_subtitle, about_chef_name, about_chef_bio1, about_chef_bio2, about_years, about_years_label, about_bg, chat_id, bot_token, values_title, values_subtitle, values_desc, value1_icon, value1_title, value1_desc, value2_icon, value2_title, value2_desc, value3_icon, value3_title, value3_desc, value4_icon, value4_title, value4_desc) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(str_repeat("s", 56), 
                $name, $logo_path, $home_bg_path, $menu_bg_path, $contact_bg_path, 
                $phone, $email, $address, $maps, $desc, $hours, $whatsapp, $insta, $fb, 
                $opening_title, $country_code, $order_method, $show_cart,
                $banner1_visible, $banner2_visible,
                $banner1_t1, $banner1_t2, $banner1_t3,
                $banner2_t1, $banner2_t2, $banner2_t3, $banner2_t4,
                $about_title, $about_subtitle, $about_desc1, $about_desc2,
                $about_image_path, $about_chef_image_path,
                $about_chef_title, $about_chef_subtitle, $about_chef_name,
                $about_chef_bio1, $about_chef_bio2,
                $about_years, $about_years_label,
                $about_bg_path,
                $chat_id, $bot_token,
                $values_title, $values_subtitle, $values_desc,
                $value1_icon, $value1_title, $value1_desc,
                $value2_icon, $value2_title, $value2_desc,
                $value3_icon, $value3_title, $value3_desc,
                $value4_icon, $value4_title, $value4_desc
            );
        }

        if ($stmt->execute()) {
            invalidate_settings_cache();

            $message = "<div class='alert alert-success'>Settings updated successfully!</div>";
            // Refresh settings cache
            $settings = get_settings();
        } else {
            $message = "<div class='alert alert-danger'>Error updating settings: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// Keep token generation close to render stage.
$galleryItems = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
$csrfToken = ensure_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Settings</title>
    <link rel="stylesheet" href="../assets/css/admin_form.css">
    <link rel="stylesheet" href="../assets/css/admin-shared.css">
    <link rel="stylesheet" href="../assets/css/editSettings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head> 
<body data-open-gallery="<?php echo (isset($_POST['upload_gallery']) || isset($_POST['delete_gallery'])) ? 'true' : 'false'; ?>">
    <div class="form-container" style="max-width: 900px;">
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; margin-bottom: 30px; gap: 15px;">
            <h1 style="margin: 0; text-align: center;">Global Settings</h1>
            <a href="dashboard" class="back-link" style="margin: 0; display: inline-block;"><button type="button" style="padding: 8px 15px; font-size: 14px; margin: 0; width: auto; display: inline-block; background: #6c757d;">Back to Dashboard</button></a>
        </div>
        <?php echo $message; ?>

        <div class="settings-tabs">
            <button type="button" class="tab-btn active" onclick="openTab('tab-general', this)">General</button>
            <button type="button" class="tab-btn" onclick="openTab('tab-pages', this)">Pages</button>
            <button type="button" class="tab-btn" onclick="openTab('tab-banners', this)">Banners</button>
            <button type="button" class="tab-btn" onclick="openTab('tab-about', this)">About Us</button>
            <button type="button" class="tab-btn" onclick="openTab('tab-telegram', this)">Telegram</button>
            <button type="button" class="tab-btn" onclick="openTab('tab-ai', this)">AI</button>
            <button type="button" class="tab-btn" id="gallery-tab-btn" onclick="openTab('tab-gallery', this)">Gallery</button>
        </div>

        <form action="editSettings" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>

            <!-- TAB 1: GENERAL -->
            <div id="tab-general" class="tab-content" style="display: block;">
                <!-- Section 1: General settings -->
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">General Information</h3>
                
                <div class="form-group">
                    <label for="restaurant_name">Restaurant Name</label>
                    <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="restaurant_logo">Restaurant Logo</label>
                    <input type="file" name="restaurant_logo" accept="image/*">
                    <?php if (!empty($settings['restaurant_logo'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['restaurant_logo']); ?>" class="current-logo" alt="Current Logo">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="restaurant_description">Restaurant Description</label>
                    <br>
                    <textarea name="restaurant_description" rows="3"><?php echo htmlspecialchars($settings['restaurant_description'] ?? ''); ?></textarea>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Contact Info & Social Links</h3>
                <div class="form-group">
                    <label for="country_code">Country Code</label>
                    <input type="text" name="country_code" value="<?php echo htmlspecialchars($settings['country_code'] ?? '+1'); ?>" placeholder="+1">
                </div>
                <div class="form-group">
                    <label for="restaurant_phone">Phone Number</label>
                    <br>
                    <input type="text" name="restaurant_phone" value="<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="restaurant_email">Email Address</label>
                    <br>
                    <input type="email" name="restaurant_email" value="<?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="whatsapp_number">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" placeholder="70123456">
                </div>
                <div class="form-group">
                    <label for="restaurant_address">Address</label>
                    <br>
                    <input type="text" name="restaurant_address" value="<?php echo htmlspecialchars($settings['restaurant_address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="restaurant_maps">Location Map Link</label>
                    <br>
                    <input type="url" name="restaurant_maps" value="<?php echo htmlspecialchars($settings['restaurant_maps'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram_url">Instagram URL</label>
                    <br>
                    <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="facebook_url">Facebook URL</label>
                    <br>
                    <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="order_method">Preferred Ordering Method</label>
                    <select name="order_method" id="order_method" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        <option value="whatsapp" <?php echo ($settings['order_method'] ?? '') === 'whatsapp' ? 'selected' : ''; ?>>WhatsApp</option>
                        <option value="sms" <?php echo ($settings['order_method'] ?? '') === 'sms' ? 'selected' : ''; ?>>SMS (Standard Text)</option>
                        <option value="telegram" <?php echo ($settings['order_method'] ?? '') === 'telegram' ? 'selected' : ''; ?>>Telegram (sent directly, no app redirect)</option>
                    </select>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Opening Hours</h3>
                <div class="form-group">
                    <label for="opening_title">Opening Title (e.g., Open Daily)</label>
                    <br>
                    <input type="text" name="opening_title" value="<?php echo htmlspecialchars($settings['opening_title'] ?? 'Open Daily'); ?>">
                </div>
                <div class="form-group">
                    <label for="opening_hours">Opening Hours</label>
                    <br>
                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($settings['opening_hours'] ?? ''); ?>">
                </div>
            </div>

            <!-- TAB 2: PAGES -->
            <div id="tab-pages" class="tab-content">
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Home Page Settings</h3>
                <div class="form-group">
                    <label for="home_bg">Home Page Hero Background</label>
                    <input type="file" name="home_bg" accept="image/*">
                    <?php if (!empty($settings['home_bg'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['home_bg']); ?>" class="current-logo" alt="Home BG">
                    <?php endif; ?>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Menu Page Settings</h3>
                <div class="form-group">
                    <label for="menu_bg">Menu Page Hero Background</label>
                    <input type="file" name="menu_bg" accept="image/*">
                    <?php if (!empty($settings['menu_bg'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['menu_bg']); ?>" class="current-logo" alt="Menu BG">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="show_cart" style="display: block; margin-bottom: 8px;">Cart / Add Button</label>
                    <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" id="show_cart" name="show_cart" value="1" <?php echo (!isset($settings['show_cart']) || (string)$settings['show_cart'] === '1') ? 'checked' : ''; ?>>
                        <span>Show “Add to cart” buttons and cart checkout</span>
                    </label>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Contact Page Settings</h3>
                <div class="form-group">
                    <label for="contact_bg">Contact Page Hero Background</label>
                    <input type="file" name="contact_bg" accept="image/*">
                    <?php if (!empty($settings['contact_bg'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['contact_bg']); ?>" class="current-logo" alt="Contact BG">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px;">Contact Form Telegram Alerts</label>
                    <div class="toggle-row">
                        <label class="toggle-switch">
                            <input type="checkbox" id="notify_contact_telegram" name="notify_contact_telegram" value="1" <?php echo (!isset($settings['notify_contact_telegram']) || (string)$settings['notify_contact_telegram'] === '1') ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label-text">Send a Telegram alert when someone submits the Contact form</span>
                    </div>
                    <small style="color:#666; font-size:12px;">Turning this off only stops the Telegram alert — messages still save and appear on the Contacts page as normal.</small>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">About Page Settings</h3>
                <div class="form-group">
                    <label for="about_bg">About Page Hero Background Image</label>
                    <input type="file" name="about_bg" accept="image/*">
                    <?php if (!empty($settings['about_bg'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['about_bg']); ?>" class="current-logo" alt="About Background Image">
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 3: BANNERS -->
            <div id="tab-banners" class="tab-content">
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Dark Green Banner (3 Texts)</h3>
                <div class="form-group">
                    <input type="hidden" name="banner1_visible" value="0">
                    <label>
                        <input type="checkbox" name="banner1_visible" value="1" <?php echo !empty($settings['banner1_visible'] ?? 0) ? 'checked' : ''; ?>> Show Green Banner
                    </label>
                </div>
                <div class="form-group">
                    <label for="banner1_t1">Green Banner - Text 1</label>
                    <input type="text" name="banner1_t1" value="<?php echo htmlspecialchars($settings['banner1_t1'] ?? 'THANK YOU FOR SUPPORTING LOCAL'); ?>">
                </div>
                <div class="form-group">
                    <label for="banner1_t2">Green Banner - Text 2 (Cursive)</label>
                    <input type="text" name="banner1_t2" value="<?php echo htmlspecialchars($settings['banner1_t2'] ?? 'Made with fresh ingredients & lots of love'); ?>">
                </div>
                <div class="form-group">
                    <label for="banner1_t3">Green Banner - Text 3</label>
                    <input type="text" name="banner1_t3" value="<?php echo htmlspecialchars($settings['banner1_t3'] ?? 'AUTHENTIC MEDITERRANEAN FLAVOR'); ?>">
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Cream Banner (4 Texts)</h3>
                <div class="form-group">
                    <input type="hidden" name="banner2_visible" value="0">
                    <label>
                        <input type="checkbox" name="banner2_visible" value="1" <?php echo !empty($settings['banner2_visible'] ?? 0) ? 'checked' : ''; ?>> Show Cream Banner
                    </label>
                </div>
                <div class="form-group">
                    <label for="banner2_t1">Cream Banner - Text 1</label>
                    <input type="text" name="banner2_t1" value="<?php echo htmlspecialchars($settings['banner2_t1'] ?? 'FRESH INGREDIENTS'); ?>">
                </div>
                <div class="form-group">
                    <label for="banner2_t2">Cream Banner - Text 2</label>
                    <input type="text" name="banner2_t2" value="<?php echo htmlspecialchars($settings['banner2_t2'] ?? 'MADE DAILY'); ?>">
                </div>
                <div class="form-group">
                    <label for="banner2_t3">Cream Banner - Text 3</label>
                    <input type="text" name="banner2_t3" value="<?php echo htmlspecialchars($settings['banner2_t3'] ?? 'AUTHENTIC RECIPES'); ?>">
                </div>
                <div class="form-group">
                    <label for="banner2_t4">Cream Banner - Text 4</label>
                    <input type="text" name="banner2_t4" value="<?php echo htmlspecialchars($settings['banner2_t4'] ?? 'MADE WITH LOVE'); ?>">
                </div>
            </div>

            <!-- TAB 4: ABOUT US -->
            <div id="tab-about" class="tab-content">
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">About Section Settings</h3>
                <div class="form-group">
                    <label for="about_title">About Title (Main Headline)</label>
                    <input type="text" name="about_title" value="<?php echo htmlspecialchars($settings['about_title'] ?? 'Flavors Crafted With Heritage & Love'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="about_subtitle">About Subtitle</label>
                    <input type="text" name="about_subtitle" value="<?php echo htmlspecialchars($settings['about_subtitle'] ?? 'Our Legacy'); ?>">
                </div>
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="flex: 0 0 130px;">
                        <label for="about_years" style="white-space: nowrap;">Badge Number</label>
                        <input type="text" name="about_years" value="<?php echo htmlspecialchars($settings['about_years'] ?? '15+'); ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="about_years_label">Badge Label</label>
                        <input type="text" name="about_years_label" value="<?php echo htmlspecialchars($settings['about_years_label'] ?? 'Years of Tradition'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="about_desc1">About Description Paragraph 1</label>
                    <br>
                    <textarea name="about_desc1" rows="5"><?php echo htmlspecialchars($settings['about_desc1'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="about_desc2">About Description Paragraph 2</label>
                    <br>
                    <textarea name="about_desc2" rows="5"><?php echo htmlspecialchars($settings['about_desc2'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="about_image">About Image</label>
                    <input type="file" name="about_image" accept="image/*">
                    <?php if (!empty($settings['about_image'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['about_image']); ?>" class="current-logo" alt="About Image">
                    <?php endif; ?>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Core Values Settings</h3>
                <div class="form-group">
                    <label for="values_title">Core Values Main Title</label>
                    <input type="text" name="values_title" value="<?php echo htmlspecialchars($settings['values_title'] ?? 'What We Stand For'); ?>">
                </div>
                <div class="form-group">
                    <label for="values_subtitle">Core Values Subtitle</label>
                    <input type="text" name="values_subtitle" value="<?php echo htmlspecialchars($settings['values_subtitle'] ?? 'Our Principles'); ?>">
                </div>
                <div class="form-group">
                    <label for="values_desc">Core Values Description</label>
                    <textarea name="values_desc" rows="5"><?php echo htmlspecialchars($settings['values_desc'] ?? 'Our commitment to authenticity and excellence shapes everything we do in our kitchen.'); ?></textarea>
                </div>

                <style>
                    .icon-picker-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
                        gap: 8px;
                        margin-bottom: 15px;
                        padding: 10px;
                        background: #fff;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        max-height: 180px;
                        overflow-y: auto;
                    }
                    .icon-picker-item {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 44px;
                        height: 44px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: 18px;
                        color: #555;
                        transition: all 0.2s ease;
                        background: #fafafa;
                    }
                    .icon-picker-item:hover {
                        border-color: #42522B;
                        color: #42522B;
                        background: #f0f4e8;
                        transform: scale(1.1);
                    }
                    .icon-picker-item.selected {
                        border-color: #42522B;
                        background: #42522B;
                        color: #F7F5EA;
                        box-shadow: 0 2px 8px rgba(66, 82, 43, 0.3);
                    }
                    .icon-picker-label {
                        font-weight: 600;
                        margin-bottom: 6px;
                        color: #42522B;
                        font-size: 14px;
                    }
                </style>

                <div style="background: rgba(0,0,0,0.03); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h4 style="font-size: 16px; margin-bottom: 10px; color: #42522B;">Value 1</h4>
                    <input type="hidden" name="value1_icon" id="value1_icon" value="<?php echo htmlspecialchars($settings['value1_icon'] ?? 'fas fa-seedling'); ?>">
                    <div class="form-group">
                        <label for="value1_title">Title</label>
                        <input type="text" name="value1_title" value="<?php echo htmlspecialchars($settings['value1_title'] ?? '100% Fresh Daily'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="value1_desc">Description</label>
                        <textarea name="value1_desc" rows="5"><?php echo htmlspecialchars($settings['value1_desc'] ?? 'We source the freshest local vegetables, premium meats, and hand-picked herbs every morning to ensure quality you can taste.'); ?></textarea>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.03); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h4 style="font-size: 16px; margin-bottom: 10px; color: #42522B;">Value 2</h4>
                    <input type="hidden" name="value2_icon" id="value2_icon" value="<?php echo htmlspecialchars($settings['value2_icon'] ?? 'fas fa-scroll'); ?>">
                    <div class="icon-picker-label">Choose Icon</div>
                    <div class="icon-picker-grid" data-target="value2_icon"></div>
                    <div class="form-group">
                        <label for="value2_title">Title</label>
                        <input type="text" name="value2_title" value="<?php echo htmlspecialchars($settings['value2_title'] ?? 'Authentic Recipes'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="value2_desc">Description</label>
                        <textarea name="value2_desc" rows="5"><?php echo htmlspecialchars($settings['value2_desc'] ?? 'Our dishes are prepared using traditional Lebanese and Mediterranean methods, honoring culinary secrets preserved for decades.'); ?></textarea>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.03); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h4 style="font-size: 16px; margin-bottom: 10px; color: #42522B;">Value 3</h4>
                    <input type="hidden" name="value3_icon" id="value3_icon" value="<?php echo htmlspecialchars($settings['value3_icon'] ?? 'fas fa-heart'); ?>">
                    <div class="icon-picker-label">Choose Icon</div>
                    <div class="icon-picker-grid" data-target="value3_icon"></div>
                    <div class="form-group">
                        <label for="value3_title">Title</label>
                        <input type="text" name="value3_title" value="<?php echo htmlspecialchars($settings['value3_title'] ?? 'Prepared With Love'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="value3_desc">Description</label>
                        <textarea name="value3_desc" rows="5"><?php echo htmlspecialchars($settings['value3_desc'] ?? 'We believe that food should warm the soul. Every meal is cooked with the same passion and dedication as if it were for our own family.'); ?></textarea>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.03); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h4 style="font-size: 16px; margin-bottom: 10px; color: #42522B;">Value 4</h4>
                    <input type="hidden" name="value4_icon" id="value4_icon" value="<?php echo htmlspecialchars($settings['value4_icon'] ?? 'fas fa-hands-helping'); ?>">
                    <div class="icon-picker-label">Choose Icon</div>
                    <div class="icon-picker-grid" data-target="value4_icon"></div>
                    <div class="form-group">
                        <label for="value4_title">Title</label>
                        <input type="text" name="value4_title" value="<?php echo htmlspecialchars($settings['value4_title'] ?? 'Warm Hospitality'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="value4_desc">Description</label>
                        <textarea name="value4_desc" rows="5"><?php echo htmlspecialchars($settings['value4_desc'] ?? 'To us, every guest is family. We welcome you with open arms and strive to make your dining experience comfortable and memorable.'); ?></textarea>
                    </div>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Chef Showcase Details</h3>
                <div class="form-group">
                    <label for="about_chef_image">Chef / Kitchen Showcase Image</label>
                    <input type="file" name="about_chef_image" accept="image/*">
                    <?php if (!empty($settings['about_chef_image'])): ?>
                        <img src="../<?php echo htmlspecialchars($settings['about_chef_image']); ?>" class="current-logo" alt="Chef Image">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="about_chef_title">Chef Section Main Title</label>
                    <input type="text" name="about_chef_title" value="<?php echo htmlspecialchars($settings['about_chef_title'] ?? 'The Passion Behind the Plate'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_chef_subtitle">Chef Section Subtitle</label>
                    <input type="text" name="about_chef_subtitle" value="<?php echo htmlspecialchars($settings['about_chef_subtitle'] ?? 'Handcrafted Culinary Artistry'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_chef_name">Chef Name / Signature</label>
                    <input type="text" name="about_chef_name" value="<?php echo htmlspecialchars($settings['about_chef_name'] ?? 'Nabil'); ?>">
                </div>
                <div class="form-group">
                    <label for="about_chef_bio1">Chef Section Bio Paragraph 1</label>
                    <br>
                    <textarea name="about_chef_bio1" rows="5"><?php echo htmlspecialchars($settings['about_chef_bio1'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="about_chef_bio2">Chef Section Bio Paragraph 2</label>
                    <br>
                    <textarea name="about_chef_bio2" rows="5"><?php echo htmlspecialchars($settings['about_chef_bio2'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- TAB 5: TELEGRAM -->
            <div id="tab-telegram" class="tab-content">
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Telegram Integration</h3>
                <div class="form-group">
                    <label for="chat_id">Telegram Chat ID</label>
                    <input type="text" name="chat_id" value="<?php echo htmlspecialchars($settings['chat_id'] ?? ''); ?>" placeholder="e.g. -100123456789">
                </div>
                <div class="form-group">
                    <label for="bot_token">Telegram Bot Token</label>
                    <input type="text" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token'] ?? ''); ?>" placeholder="e.g. 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                </div>
            </div>

            <div id="tab-ai" class="tab-content">
                <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">AI Assistant</h3>
                <div class="form-group">
    <label for="deepseek_api_key">DeepSeek API Key</label>
    
    <!-- Wrapper to handle absolute positioning for the toggle icon -->
    <div style="position: relative; display: flex; align-items: center;">
        <input 
            type="password" 
            id="deepseek_api_key"
            name="deepseek_api_key" 
            value="<?php echo htmlspecialchars($settings['deepseek_api_key'] ?? ''); ?>" 
            placeholder="sk-..."
            style="width: 100%; padding-right: 40px;"
        >
        
        <!-- Toggle Button with Eye Icon (SVG) -->
        <button 
            type="button" 
            onclick="togglePasswordVisibility('deepseek_api_key', this)"
            aria-label="Toggle password visibility"
            style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; color: #666;"
        >
            <!-- Eye Icon (Visible state default) -->
            <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </button>
    </div>

    <small style="color:#666; font-size:12px; display:block; margin-top:5px;">
        Powers the Telegram bot's AI assistant — reading/answering questions, updating orders, and managing the menu. Get a key at platform.deepseek.com. Leave blank to disable the assistant (the bot will just tell you it isn't configured).
    </small>
</div>
                <div class="form-group">
                    <label for="deepseek_model">DeepSeek Model</label>
                    <?php $currentModel = $settings['deepseek_model'] ?? 'deepseek-v4-flash'; ?>
                    <select name="deepseek_model">
                        <option value="deepseek-v4-pro" <?php echo $currentModel === 'deepseek-v4-pro' ? 'selected' : ''; ?>>DeepSeek V4 Pro — most capable, higher cost</option>
                        <option value="deepseek-v4-flash" <?php echo $currentModel === 'deepseek-v4-flash' ? 'selected' : ''; ?>>DeepSeek V4 Flash — fast & cheap (recommended)</option>
                        <option value="deepseek-reasoner" <?php echo $currentModel === 'deepseek-reasoner' ? 'selected' : ''; ?>>DeepSeek Reasoner (legacy — stops working July 24, 2026)</option>
                        <option value="deepseek-chat" <?php echo $currentModel === 'deepseek-chat' ? 'selected' : ''; ?>>DeepSeek Chat (legacy — stops working July 24, 2026)</option>
                    </select>
                    <small style="color:#666; font-size:12px;">V4 Pro/Flash are the current models and won't expire. Reasoner/Chat are old names kept for now — switch off them before July 24, 2026.</small>
                </div>
            </div>

            <div style="margin-top: 30px;" id="save-settings-container">
                <button type="submit" name="update_settings" class="submit-btn">Save Settings</button>
            </div>
        </form>

        <!-- TAB 6: GALLERY -->
        <div id="tab-gallery" class="tab-content">
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; color: #42522B;">Manage Gallery</h3>
            <form action="editSettings" method="POST" enctype="multipart/form-data">
                <?php echo csrf_input(); ?>
                <div class="form-group">
                    <label for="photo">Upload Vibe Photo(s)</label>
                    <input type="file" name="photos[]" id="photo" multiple required>
                </div>
                <button type="submit" name="upload_gallery" class="submit-btn" style="background-color: #42522B;">Upload to Gallery</button>
            </form>

            <div class="gallery-grid">
                <?php while($row = $galleryItems->fetch_assoc()): ?>
                    <div class="gallery-item">
                        <img src="../<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Gallery Image">
                        <form method="POST" action="editSettings" style="display:inline;" onsubmit="return confirm('Delete this photo?')">
                            <?php echo csrf_input(); ?>
                            <input type="hidden" name="delete_gallery" value="<?php echo (int)$row['id']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const svg = btn.querySelector('svg');
    
    if (input.type === 'password') {
        input.type = 'text';
        // Swaps eye to eye-off (slashed eye) when visible
        svg.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        `;
    } else {
        input.type = 'password';
        // Swaps back to normal eye when hidden
        svg.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        `;
    }
}
</script>
    <script src="../assets/js/editSettings.js"></script>
</body>
</html>