<?php
/**
 * Subscribers CRUD (import, add, edit, delete) & paginated list.
 */
function newsletter_subscribers() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletter_subscribers';

    $items_per_page = 50;
    $current_page   = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset         = ($current_page - 1) * $items_per_page;

    // --- Actions ---
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('delete_subscriber_action','delete_subscriber_nonce');
        $wpdb->delete($table_name, [ 'ID' => intval($_GET['id']) ], [ '%d' ]);
    }

    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit_subscriber'])) {
        check_admin_referer('edit_subscriber_action','edit_subscriber_nonce');
        $id    = intval($_POST['id']);
        $email = sanitize_email($_POST['email']);
        if (is_email($email)) {
            $wpdb->update($table_name,[ 'email'=>$email ],[ 'ID'=>$id ],[ '%s' ],[ '%d' ]);
        }
    }

    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['new_subscriber'])) {
        check_admin_referer('new_subscriber_action','new_subscriber_nonce');
        $email = sanitize_email($_POST['email']);
        if (is_email($email)) {
            $wpdb->insert($table_name,[ 'email'=>$email,'added_at'=>current_time('mysql') ],[ '%s','%s' ]);
        }
    }

    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['import_subscribers'])) {
        check_admin_referer('import_subscribers_action','import_subscribers_nonce');
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            $file = fopen($_FILES['csv_file']['tmp_name'],'r');
            while (($row=fgetcsv($file,1000,','))!==false) {
                $email = sanitize_email($row[0]);
                if (is_email($email)) {
                    $wpdb->insert($table_name,[ 'email'=>$email,'added_at'=>current_time('mysql') ],[ '%s','%s' ]);
                }
            }
            fclose($file);
        }
    }

    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_all_subscribers'])) {
        check_admin_referer('delete_all_subscribers_action','delete_all_subscribers_nonce');
        $wpdb->query("TRUNCATE TABLE $table_name");
    }

    // --- List output ---
    echo '<h1>Subscribers</h1>';

    // Import form
    echo '<form method="POST" enctype="multipart/form-data" style="margin-bottom:20px;">';
    wp_nonce_field('import_subscribers_action','import_subscribers_nonce');
    echo '<input type="file" name="csv_file" accept=".csv" required style="margin-right:10px;">';
    echo '<button type="submit" name="import_subscribers" class="button button-primary">Import CSV</button>';
    echo '</form>';

    // New subscriber form
    echo '<form method="POST" style="margin-bottom:20px;">';
    wp_nonce_field('new_subscriber_action','new_subscriber_nonce');
    echo '<input type="email" name="email" required placeholder="email@example.com" style="padding:5px;width:300px;margin-right:10px;">';
    echo '<button type="submit" name="new_subscriber" class="button">Add Subscriber</button>';
    echo '</form>';

    // Delete all form
    echo '<form method="POST" style="display:inline-block;margin-bottom:20px;">';
    wp_nonce_field('delete_all_subscribers_action','delete_all_subscribers_nonce');
    echo '<button type="submit" name="delete_all_subscribers" class="button" style="background:#e74c3c;color:#fff;">Delete All</button>';
    echo '</form>';

    // Query & pagination
    $subscribers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY added_at DESC LIMIT %d OFFSET %d",$items_per_page,$offset));
    $total_items = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items/$items_per_page);

    echo '<table class="widefat striped" style="width:100%;">';
    echo '<thead><tr><th>Email</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
    if ($subscribers) {
        foreach ($subscribers as $s) {
            $delete_link = wp_nonce_url("?page=newsletter-subscribers&action=delete&id={$s->ID}",'delete_subscriber_action','delete_subscriber_nonce');
            echo "<tr>
                    <td>{$s->email}</td>
                    <td>{$s->added_at}</td>
                    <td><a href='{$delete_link}' class='button'>Delete</a></td>
                  </tr>";
        }
    } else {
        echo '<tr><td colspan="3">No subscribers found.</td></tr>';
    }
    echo '</tbody></table>';

    newsletter_render_pagination($total_pages,$current_page);
}

/**
 * Simple numeric pagination helper (shared with tests list).
 */
function newsletter_render_pagination($total_pages,$current_page){
    if($total_pages<=1) return;
    echo '<div class="tablenav-pages" style="margin:1em 0;">';
    if($current_page>1){
        $prev=add_query_arg('paged',$current_page-1);
        echo '<a class="button" href="'.esc_url($prev).'">&laquo;</a> ';
    }
    for($i=1;$i<=$total_pages;$i++){
        if($i==$current_page){
            echo '<span class="button" style="font-weight:bold;">'.$i.'</span> ';
        } else {
            echo '<a class="button" href="'.esc_url(add_query_arg('paged',$i)).'">'.$i.'</a> ';
        }
    }
    if($current_page<$total_pages){
        $next=add_query_arg('paged',$current_page+1);
        echo '<a class="button" href="'.esc_url($next).'">&raquo;</a>';
    }
    echo '</div>';
}
