<?php
/**
 * Handles DB table creation on plugin activation.
 */
function newsletter_activate_plugin() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $subscribers_table      = $wpdb->prefix . 'newsletter_subscribers';
    $test_subscribers_table = $wpdb->prefix . 'newsletter_test_subscribers';

    $sql1 = "CREATE TABLE IF NOT EXISTS $subscribers_table (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        added_at datetime NOT NULL,
        PRIMARY KEY  (ID)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE IF NOT EXISTS $test_subscribers_table (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        added_at datetime NOT NULL,
        PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql1);
    dbDelta($sql2);
}
