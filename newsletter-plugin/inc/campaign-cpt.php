<?php
/**
 * Registers the custom post type 'campaign' and custom admin columns.
 */
function register_newsletter_campaign_post_type() {
    $labels=[ 'name'=>'Campaigns', 'singular_name'=>'Campaign', 'menu_name'=>'Campaigns' ];
    $args=[
        'labels'=>$labels,
        'public'=>true,
        'publicly_queryable'=>false,
        'show_ui'=>true,
        'show_in_menu'=>'newsletter',
        'supports'=>['title','editor'],
        'has_archive'=>false,
    ];
    register_post_type('campaign',$args);
}
add_action('init','register_newsletter_campaign_post_type');

function set_custom_campaign_columns($columns){
    unset($columns['date']);
    $columns['status']='Status';
    $columns['progress']='Progress';
    $columns['send_button']='Actions';
    return $columns;
}
add_filter('manage_campaign_posts_columns','set_custom_campaign_columns');

function render_send_button_column($column,$post_id){
    if($column==='status'){
        echo esc_html(get_post_meta($post_id,'_newsletter_status',true) ?: 'Not sent');
    } elseif($column==='progress'){
        $total=(int)get_post_meta($post_id,'_newsletter_total',true);
        $sent =(int)get_post_meta($post_id,'_newsletter_sent',true);
        echo "{$sent}/{$total}";
    } elseif($column==='send_button'){
        $status=get_post_meta($post_id,'_newsletter_status',true);
        if($status==='Completed'){
            echo '<span>Completed</span>';
        } else {
            $label = ($status==='Processing' || $status==='Partial') ? 'Resume' : 'Send';
            $url   = admin_url('admin.php?page=newsletter-send&camp_id='.$post_id);
            echo "<a href='{$url}' class='button'>{$label}</a>";
        }
        $test_url = admin_url('admin.php?page=newsletter-test-send&camp_id='.$post_id);
        echo " <a href='{$test_url}' class='button button-secondary'>Send Test</a>";
    }
}
add_action('manage_campaign_posts_custom_column','render_send_button_column',10,2);
