# Newsletter Plugin (Modular Version)

This repository contains a modular, demonstration version of a WordPress plugin for sending batch newsletters via AJAX, SMTP configuration, template insertion, featured note injection, and subscriber/unsubscribe management. It is intended as a **portfolio example** only.

⚠️ **Disclaimer**  
This project is part of my personal development portfolio and is published for demonstration purposes only.

This is not the production version.  
It does not include any real credentials, access tokens, or private data.  
Subscriber lists and campaign data have been generalized or placeholderized.  
The production version exists and is actively deployed, but it is private and secured.  
Please do not reuse, distribute, or attempt to deploy this demo in real applications.

🚀 **Features (in this demo)**  
- **Batch sending via AJAX**  
  - Configurable batch size (default 4 emails per AJAX request)  
  - Real-time progress bar and “Continue” capability  
- **SMTP configuration**  
  - Sender name, sender email, reply-to  
  - Host, port, encryption (TLS/SSL), username/password (placeholder)  
- **Template insertion**  
  - One-click “Insert template” button populates a responsive HTML email  
  - Two placeholders: `[ultimas_5_notas]` (most‐read posts) and `[proximos_eventos]` (upcoming events)  
- **Featured note injection**  
  - “Insert featured note” button opens a modal  
  - Fetches a post’s featured image, title, and excerpt via AJAX, then injects a styled snippet before the “Most‐read” marker  
- **Custom Post Type: Campaign**  
  - “Campaign” CPT with columns: status, progress, send/test buttons  
  - Separate pages: normal send and test send, both using AJAX loops  
- **Subscriber management (admin)**  
  - Paginated list of real subscribers  
  - CSV import, manual add, edit, delete, truncate all  
- **Test subscriber list**  
  - Separate CSV‐style list for sending test emails  
- **Shortcode support (via AJAX)**  
  - `[ultimas_5_notas]` retrieves the top 5 popular posts from the last week (placeholder)  
  - `[proximos_eventos]` retrieves 3 upcoming events (placeholder)  
- **Automatic unsubscribe**  
  - One‐click “unsubscribe” link in the footer removes your email from the subscribers table  
- **Modular code organization**  
  - Each functional area is split into its own PHP file under `/inc`  
  - Main plugin bootstrap file (`newsletter-plugin.php`) simply requires each module  
  - HTML template resides under `/templates/email-template.html` with English‐commented markup  

---

## 🛠 Tech Stack
- **PHP** ≥ 7.4  
- **WordPress** ≥ 5.0  
- **MySQL** (for custom tables: `wp_newsletter_subscribers` and `wp_newsletter_test_subscribers`)  
- **AJAX** (jQuery) for batch sending and featured‐note insertion  
- **PHPMailer** (built into WordPress) configured with SMTP options  

---

## 🧪 Installation & Local Testing

1. **Clone or Download** this repository into your local WordPress site’s `wp-content/plugins/` directory.  
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   git clone https://github.com/yourusername/newsletter-plugin-modular.git