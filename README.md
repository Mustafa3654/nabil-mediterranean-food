# Nabil Mediterranean Food — Restaurant Website & Menu Management System

The official website and menu management platform for **Nabil Mediterranean Food** (Warrensville Heights, Ohio) — a full PHP/MySQL application with a public storefront, online ordering, and a centralized admin dashboard.

It features an elegant **Olive Green & Cream White theme**, a persistent **Dark Mode** toggle, active category border glow animations, a customer-facing cart with WhatsApp/SMS checkout, and an **AI-powered Telegram assistant** that lets the owner run the restaurant from their phone.

---

## 🌟 Features

### 🍽️ Public Features
- **Modern Landing Page**: Responsive hero section with dynamic background media, customizable headers, highlights, a photo gallery slider, and restaurant opening hours.
- **Interactive About Page**: The restaurant's legacy, a configurable principles/values card grid, and a chef showcase segment.
- **Interactive Menu**: Multi-category menu view with active tab transitions, an items grid with details, and an integrated shopping cart.
- **Flexible Cart & Checkout System**:
  - Persistent shopping cart stored via `localStorage`.
  - Interactive item controls directly on the menu cards.
  - Checkout routed through **WhatsApp**, **SMS**, or saved directly as an order — based on admin configuration.
  - Automated combo warning notifications (combo items require an hour of preparation time).
- **Online Ordering**: Orders submitted from the cart are persisted to the database and pushed instantly to the owner's Telegram chat.
- **Persistent Dark Mode**: Sun/moon toggle in the navigation bar, remembered across visits via `localStorage`.
- **Olive Green Design System**: Olive Green (`#42522B`), Cream White (`#F7F5EA`), Dark Charcoal (`#2B2B2A`), and Light Khaki (`#CBB58B`) accents.
- **Glowing Hover Effects**: Interactive glowing border animations for active categories, buttons, and links.
- **Item Details**: Dedicated view showing detailed ingredients, description, and pricing structure.
- **Interactive Contact Page**: Contact form, location map, phone, email, and social handles. Submissions are stored and can trigger Telegram alerts.
- **SEO Ready**: Canonical URLs, structured data (`Restaurant` JSON-LD), `sitemap.xml`, and `robots.txt`.
- **Fluid Responsiveness**: Works across desktops, tablets, and smartphones.

### 🛡️ Admin Features
- **Secure Authentication**: Session-based admin login with CSRF protection and automatic on-the-fly password hashing upgrade for legacy accounts.
- **Central Dashboard**: Overview for editing categories, items, and platform parameters.
- **Category Management**: Add, edit, delete, and re-order categories, with custom images and icons.
- **Menu Item Management**: Full CRUD, image upload, pricing configuration, descriptions, and custom sort ordering.
- **Order Management** (`admin/viewOrders.php`): Review incoming orders, customer details, line items, and status.
- **Contact Inbox** (`admin/viewContacts.php`): Read and manage messages submitted through the contact form.
- **CSV Import & Export**: Export all menu items (with Excel UTF-8 BOM support) and bulk import/update items via CSV, including category validation.
- **Automatic WebP Conversion**: Uploaded JPEG/PNG/GIF images are converted to WebP via GD for faster page loads. A one-off `admin/migrate_to_webp.php` script converts legacy images.
- **System Settings Configuration**:
  - Restaurant branding, name, description, and logo.
  - Contact details (address, email, phone, maps embed).
  - Background media and banners per view (Home, Menu, Contact, About).
  - Landing page sliders/gallery, about text, chef bios, and values grid.
  - Custom opening hour displays.
  - Telegram bot credentials for routing inquiries and orders to a Telegram chat.

### 🤖 Telegram AI Assistant
`telegram_webhook.php` turns the owner's Telegram chat into a control panel, backed by an LLM:
- **Order status by natural language** — "15 ready", "cancel order 12".
- **Questions answered** — "list pending orders", "how many cancelled today".
- **Menu management** — add items, add categories, change prices.
- **Destructive actions are guarded** — deleting an item or category always requires an explicit `YES` confirmation reply.
- **One-tap customer texting** — when an order is marked *Ready for Pickup*, the bot attaches a "Text customer" button that opens the phone's Messages app with a pre-filled message (via the `sms_redirect.php` https → `sms:` bridge, since Telegram only permits https links on inline buttons).

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.2+ (MySQLi prepared statements)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3 (custom properties), Vanilla JavaScript
- **Images**: GD library for WebP conversion
- **Icons**: Font Awesome v5/v6 CDN
- **Integrations**: Telegram Bot API, DeepSeek (AI assistant)
- **Server Environment**: Apache with `mod_rewrite` (works locally on XAMPP/WampServer via `.htaccess`)

---

## 📋 System Requirements

- PHP 7.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server with `mod_rewrite` enabled
- PHP extensions: `mysqli`, `gd`, `curl`, sessions and file uploads enabled

---

## 🚀 Installation & Setup

### Step 1: Copy Files to Web Root

- **XAMPP**: `C:\xampp\htdocs\rest_menu\`
- **WampServer**: `C:\wamp64\www\rest_menu\`
- **Linux/Mac**: `/var/www/html/rest_menu/`

The project resolves its own base URL dynamically, so the folder can be renamed freely.

### Step 2: Database Setup

1. Start Apache and MySQL.
2. Open phpMyAdmin: <http://localhost/phpmyadmin>
3. Create a database named `nabil_menu` (UTF-8 Unicode collation recommended).
4. Go to **Import** and select either:
   - `Databases/nabil_menu.sql` — full schema with the live menu content, or
   - `Databases/empty_nabil_menu.sql` — schema only, for a fresh start.

### Step 3: Database Connection Configuration

Edit [includes/connection.php](includes/connection.php) to match your server credentials:

```php
$dbHost     = 'localhost';
$dbUsername = 'root';        // Your MySQL username
$dbPassword = '';            // Your MySQL password
$dbName     = 'nabil_menu';  // Database name
```

### Step 4: Configure Write Permissions

Ensure the upload directories are writable:
- `assets/images/admin/bgs/` — logo and background images
- `assets/images/admin/pics/` — gallery and vibe images
- `assets/images/items/` — menu item thumbnails

On Linux or Mac:

```bash
chmod 755 assets/images/admin/bgs/ assets/images/admin/pics/ assets/images/items/
```

### Step 5: Admin Credentials

The SQL dumps in this repository ship with a placeholder admin account:

- **Username**: `admin`
- **Password**: `CHANGE_ME`

> [!WARNING]
> Set a real password before exposing the site. Log in once with a value you set in the `users` table and the login system will transparently upgrade it to a bcrypt hash.

### Step 6: Configure Integrations (optional)

Credentials are **not** stored in this repository — set them from the admin panel after install:

- **Telegram**: create a bot with [@BotFather](https://t.me/BotFather), then enter the bot token and your chat ID under `admin/editTelegram.php`.
- **Webhook**: point the bot at the deployed webhook so the AI assistant receives messages:

```bash
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://your-domain.com/telegram_webhook.php"
```

- **AI assistant**: add a DeepSeek API key in the settings page to enable natural-language order and menu management.

---

## 📂 Project Structure

```text
rest_menu/
├── .htaccess               # Apache configuration for security and clean URL redirections
├── README.md               # System documentation
├── robots.txt              # Crawler directives
├── sitemap.xml             # SEO sitemap
├── favicon.png             # Site icon
├── index.php               # Public homepage / landing page
├── about.php               # Public legacy & story page
├── menu.php                # Public interactive menu page with cart system
├── ingredients.php         # Dedicated menu item details page
├── contact.php             # Contact and location page with form and Telegram notifications
├── save_order.php          # Order submission endpoint (persists order + notifies Telegram)
├── telegram_webhook.php    # AI-powered Telegram bot webhook (orders, menu, Q&A)
├── sms_redirect.php        # https -> sms: bridge for the bot's "Text customer" button
├── login.php               # Secure admin login gateway
├── logout.php              # Session destroyer script
│
├── admin/                  # Admin panel modules
│   ├── dashboard.php       # Central admin panel landing page
│   ├── addCategory.php     # Create new categories
│   ├── editCategory.php    # Update existing categories
│   ├── deleteCategory.php  # Delete categories
│   ├── viewCategories.php  # Category listing and management
│   ├── addItem.php         # Create new menu items
│   ├── editItem.php        # Update existing menu items
│   ├── deleteItem.php      # Delete menu items
│   ├── viewItems.php       # Menu items listing, CSV triggers, and management
│   ├── viewOrders.php      # Incoming customer orders and statuses
│   ├── viewContacts.php    # Contact form inbox
│   ├── editSettings.php    # General website parameters, banners, values, & branding setup
│   ├── editTelegram.php    # Telegram bot configuration management
│   ├── exportItems.php     # Export menu items to CSV format
│   ├── importItems.php     # Bulk import/update menu items from CSV format
│   ├── manageGallery.php   # Gallery and slider settings
│   └── migrate_to_webp.php # One-off conversion of legacy images to WebP
│
├── assets/                 # Static assets
│   ├── css/                # Modular styling system
│   │   ├── theme.css       # Core design system tokens (colors, dark mode, glows)
│   │   ├── index.css       # Home/landing page styling rules
│   │   ├── about.css       # About page styling rules
│   │   ├── menu.css        # Interactive menu listing & cart styling
│   │   ├── dashboard.css   # Core administrator panel styling
│   │   ├── login.css       # Admin authentication styling
│   │   ├── editSettings.css# Admin edit settings view styling
│   │   ├── add.css         # Page addition component styles
│   │   ├── admin-shared.css# Shared styling components for admin views
│   │   ├── admin_form.css  # Styling for category & item editing forms
│   │   ├── contact.css     # Location & contact information styling
│   │   ├── footer.css      # Public footer styling
│   │   ├── header.css      # Navigation header styling
│   │   └── view.css        # View listing layout styling
│   ├── js/                 # Interactive scripts
│   │   ├── theme.js        # Persistent Light/Dark mode toggler logic
│   │   ├── index.js        # Home page gallery slider interactions
│   │   ├── menu.js         # Menu category filters and active styling
│   │   ├── cart.js         # Client-side order/cart logic & WhatsApp/SMS checkout
│   │   ├── editSettings.js # Admin dashboard settings validation/handlers
│   │   └── login.js        # Admin authentication helpers
│   └── images/
│       ├── items/          # Uploaded menu item images
│       └── admin/
│           ├── bgs/        # Logo and background images
│           └── pics/       # Gallery and vibe pictures
│
├── includes/               # Common shared templates & logic
│   ├── auth.php            # Admin session authentication and CSRF security handlers
│   ├── connection.php      # MySQL connection configuration and dynamic base URL setup
│   ├── webp_helper.php     # Reusable GD-based WebP conversion helpers
│   ├── header.php          # Site layout top navigation header component
│   └── footer.php          # Site layout footer component
│
└── Databases/              # Database SQL scripts
    ├── nabil_menu.sql          # Schema with live menu data (secrets redacted)
    └── empty_nabil_menu.sql    # Fresh schema structure only
```

---

## 🎨 Styling & Color Customization

The system features a centralized palette configuration. Rather than chasing colors through individual page stylesheets, global tokens are set inside [assets/css/theme.css](assets/css/theme.css):

### Active theme variables (`:root` light theme)

```css
:root {
    --olive-green: #42522B;      /* Brand accent color */
    --cream-white: #F7F5EA;      /* Cozy soft background */
    --dark-charcoal: #2B2B2A;    /* Primary text */
    --light-khaki: #CBB58B;      /* Delicate borders & shadows */
}
```

### Persistent dark mode variables

```css
body.dark-mode {
    --bg-color: #1a1f11;         /* Deep olive-infused dark base */
    --card-bg: #2a2f1a;          /* Contrasted card panels */
    --text-color: #f7f5ea;       /* High-readability light cream text */
    --border-color: #42522b;
    --accent-blue: #cbb58b;      /* Warm golden accent highlight */
}
```

---

## 🔒 Security Best Practices

1. **No secrets in the repo**: the SQL dumps ship with the Telegram bot token, AI API key, and admin password redacted. Configure real values through the admin panel after install, and never commit them back.
2. **CSRF protection**: state-changing POST requests in the admin panel are verified against a per-session random token.
3. **On-the-fly password hashing**: the login system upgrades plain-text credentials to bcrypt (`PASSWORD_DEFAULT`) on successful login.
4. **SQL injection safety**: use MySQLi prepared statements with bound parameters for every dynamic value, in both public and admin files.
5. **Upload filtering**: MIME-type checks and size limits are enforced on image uploads in `admin/addItem.php`, `admin/addCategory.php`, and `admin/editSettings.php`.
6. **Guarded bot actions**: the Telegram assistant never deletes an item or category without an explicit `YES` reply.

---

## 🔍 Troubleshooting

### ❌ Database connection failure
- Verify host, user, password, and database name in [includes/connection.php](includes/connection.php).
- Ensure the MySQL service is running in your control panel.

### ❌ Uploaded images do not render
- Check permissions on the `assets/images/` subdirectories (`admin/bgs/`, `admin/pics/`, `items/`).
- Ensure `upload_max_filesize` and `post_max_size` in `php.ini` are large enough.
- WebP conversion requires the `gd` extension with WebP support — confirm via `phpinfo()`.

### ❌ Telegram bot is silent
- Confirm the webhook is registered: `https://api.telegram.org/bot<TOKEN>/getWebhookInfo`.
- The webhook URL must be publicly reachable over HTTPS — it will not work from `localhost`.
- Verify the bot token and chat ID saved in `admin/editTelegram.php`.

### ❌ Dark mode state resets
- Ensure the browser allows `localStorage`. The state lives under the key `theme`.
