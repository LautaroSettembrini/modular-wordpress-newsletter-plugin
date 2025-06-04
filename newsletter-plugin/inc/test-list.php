<?php
/**
 * Manages the test subscribers list.
 */
function newsletter_test_subscribers() {
    global $wpdb;
    $table_name = $wpdb->prefix.'newsletter_test_subscribers';

    // Delete single
    if (isset($_GET['action'], $_GET['id']) && $_GET['action']==='delete'){
        check_admin_referer('delete_test_subscriber_action','delete_test_subscriber_nonce');
        $wpdb->delete($table_name,[ 'ID'=>intval($_GET['id']) ],[ '%d' ]);
    }

    // Add new
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['new_test_subscriber'])){
        check_admin_referer('new_test_subscriber_action','new_test_subscriber_nonce');
        $email = sanitize_email($_POST['email']);
        if(is_email($email)){
            $wpdb->insert($table_name,[ 'email'=>$email,'added_at'=>current_time('mysql') ],[ '%s','%s' ]);
        }
    }

    // Delete all
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_all_test_subscribers'])){
        check_admin_referer('delete_all_test_subscribers_action','delete_all_test_subscribers_nonce');
        $wpdb->query("TRUNCATE TABLE $table_name");
    }

    // Output
    echo '<h1>Test Subscribers</h1>';

    // New
    echo '<form method="POST" style="margin-bottom:20px;">';
    wp_nonce_field('new_test_subscriber_action','new_test_subscriber_nonce');
    echo '<input type="email" name="email" required placeholder="test@example.com" style="padding:5px;width:300px;margin-right:10px;">';
    echo '<button type="submit" name="new_test_subscriber" class="button">Add Test Subscriber</button>';
    echo '</form>';

    // Delete all
    echo '<form method="POST" style="display:inline-block;margin-bottom:20px;">';
    wp_nonce_field('delete_all_test_subscribers_action','delete_all_test_subscribers_nonce');
    echo '<button type="submit" name="delete_all_test_subscribers" class="button" style="background:#e74c3c;color:#fff;">Delete All</button>';
    echo '</form>';

    // List
    $subs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY added_at DESC");
    echo '<table class="widefat striped" style="width:100%;"><thead><tr><th>Email</th><th>Date</th><th></th></tr></thead><tbody>';
    if($subs){
        foreach($subs as $s){
            $del = wp_nonce_url("?page=newsletter-test-subscribers&action=delete&id={$s->ID}",'delete_test_subscriber_action','delete_test_subscriber_nonce');
            echo "<tr><td>{$s->email}</td><td>{$s->added_at}</td><td><a href='{$del}' class='button'>Delete</a></td></tr>";
        }
    } else {
        echo '<tr><td colspan="3">Empty.</td></tr>';
    }
    echo '</tbody></table>';
}
