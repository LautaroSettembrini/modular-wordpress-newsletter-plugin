<?php
/**
 * AJAX handlers for batch sending to the TEST list.
 */
add_action('wp_ajax_newsletter_ajax_init_test','newsletter_ajax_init_test_callback');
function newsletter_ajax_init_test_callback(){
    check_ajax_referer('newsletter_ajax_test_send_nonce','security');
    $post_id=intval($_POST['camp_id']);
    if(!$post_id || get_post_type($post_id)!=='campaign') wp_send_json_error('Invalid');

    global $wpdb;
    $total=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_test_subscribers");

    update_post_meta($post_id,'_newsletter_test_status','Processing');
    update_post_meta($post_id,'_newsletter_test_total',$total);
    update_post_meta($post_id,'_newsletter_test_sent',0);
    update_post_meta($post_id,'_newsletter_test_offset',0);

    wp_send_json_success([ 'status'=>'Processing','total'=>$total,'sent'=>0 ]);
}

add_action('wp_ajax_newsletter_ajax_send_batch_test','newsletter_ajax_send_batch_test_callback');
function newsletter_ajax_send_batch_test_callback(){
    check_ajax_referer('newsletter_ajax_test_send_nonce','security');
    $post_id=intval($_POST['camp_id']);
    if(!$post_id || get_post_type($post_id)!=='campaign') wp_send_json_error('Invalid');

    global $wpdb;
    $status=get_post_meta($post_id,'_newsletter_test_status',true);
    $total =(int)get_post_meta($post_id,'_newsletter_test_total',true);
    $sent  =(int)get_post_meta($post_id,'_newsletter_test_sent',true);
    $offset=(int)get_post_meta($post_id,'_newsletter_test_offset',true);

    if($status==='Completed' || $sent>=$total){
        update_post_meta($post_id,'_newsletter_test_status','Completed');
        wp_send_json_success([ 'status'=>'Completed','total'=>$total,'sent'=>$sent ]);
    }

    $batch =(int)get_option('newsletter_batch_size',4);
    $subs  =$wpdb->get_results($wpdb->prepare("SELECT email,ID FROM {$wpdb->prefix}newsletter_test_subscribers ORDER BY ID ASC LIMIT %d OFFSET %d",$batch,$offset));

    if(!$subs){
        update_post_meta($post_id,'_newsletter_test_status','Partial');
        wp_send_json_success([ 'status'=>'Partial','total'=>$total,'sent'=>$sent ]);
    }

    $subject=get_the_title($post_id);
    $content=wp_kses_post(do_shortcode(get_post_field('post_content',$post_id)));

    foreach($subs as $s){
        $unsub=base64_encode($s->ID);
        $link =add_query_arg([ 'newsletter_unsubscribe'=>$unsub ],get_home_url());
        $body =str_replace('[unsubscribe_link]',esc_url($link),$content);
        wp_mail($s->email,$subject,$body);
    }

    $new_sent=$sent+count($subs);
    $new_off =$offset+count($subs);
    update_post_meta($post_id,'_newsletter_test_sent',$new_sent);
    update_post_meta($post_id,'_newsletter_test_offset',$new_off);

    $new_status=($new_sent>=$total)?'Completed':'Processing';
    update_post_meta($post_id,'_newsletter_test_status',$new_status);

    wp_send_json_success([ 'status'=>$new_status,'total'=>$total,'sent'=>$new_sent ]);
}
