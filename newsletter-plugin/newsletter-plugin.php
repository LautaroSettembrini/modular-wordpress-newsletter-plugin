<?php
/**
 * Plugin Name: Newsletter Plugin
 * Description: Custom plugin to send newsletters in batches via AJAX, SMTP configuration, template insertion, featured notes and full subscribe / unsubscribe flow.
 * Version: 2.5
 * Author: Lautaro Settembrini
 */

defined('ABSPATH') || exit;

// --- Load modules ---
require_once __DIR__ . '/inc/database.php';
require_once __DIR__ . '/inc/admin-menu.php';
require_once __DIR__ . '/inc/settings-page.php';
require_once __DIR__ . '/inc/subscribers-list.php';
require_once __DIR__ . '/inc/test-list.php';
require_once __DIR__ . '/inc/campaign-cpt.php';
require_once __DIR__ . '/inc/send-campaign.php';
require_once __DIR__ . '/inc/ajax-send.php';
require_once __DIR__ . '/inc/send-test.php';
require_once __DIR__ . '/inc/ajax-send-test.php';
require_once __DIR__ . '/inc/phpmailer-config.php';
require_once __DIR__ . '/inc/template-insert.php';
require_once __DIR__ . '/inc/ajax-featured-note.php';
require_once __DIR__ . '/inc/shortcodes.php';
require_once __DIR__ . '/inc/unsubscribe.php';

// Activation hook (creates DB tables)
register_activation_hook(__FILE__, 'newsletter_activate_plugin');
