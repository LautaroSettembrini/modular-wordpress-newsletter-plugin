<?php
/**
 * Registers admin menus and sub‑menus for the plugin.
 */
function newsletter_admin_menu() {
    add_menu_page('Newsletter','Newsletter','manage_options','newsletter','newsletter_subscribers','dashicons-email',6);

    add_submenu_page('newsletter','Subscriber List','Subscriber List','manage_options','newsletter-subscribers','newsletter_subscribers');
    add_submenu_page('newsletter','Test List','Test List','manage_options','newsletter-test-subscribers','newsletter_test_subscribers');
    add_submenu_page('newsletter','New Campaign','New Campaign','manage_options','post-new.php?post_type=campaign');
    add_submenu_page('newsletter','Settings','Settings','manage_options','newsletter-settings','newsletter_settings_page');
}
add_action('admin_menu','newsletter_admin_menu');
