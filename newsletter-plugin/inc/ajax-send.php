<?php
/**
 * AJAX handlers for batch sending (real list).
 */
add_action('wp_ajax_newsletter_ajax_init','newsletter_ajax_init_callback');
function newsletter_ajax_init_callback(){
    check_ajax_referer('newsletter_ajax_send_nonce','security');
    $post_id = intval($_POST['camp_id']);
    if(!$post_id || get_post_type($post_id)!=='campaign') wp_send_json_error('Invalid campaign');

    global $wpdb;
    $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter_subscribers");

    update_post_meta($post_id,'_newsletter_status','Processing');
    update_post_meta($post_id,'_newsletter_total',$total);
    update_post_meta($post_id,'_newsletter_sent',0);
    update_post_meta($post_id,'_newsletter_offset',0);

    wp_send_json_success([ 'status'=>'Processing','total'=>$total,'sent'=>0 ]);
}

add_action('wp_ajax_newsletter_ajax_send_batch','newsletter_ajax_send_batch_callback');
function newsletter_ajax_send_batch_callback(){
    check_ajax_referer('newsletter_ajax_send_nonce','security');
    $post_id = intval($_POST['camp_id']);
    if(!$post_id || get_post_type($post_id)!=='campaign') wp_send_json_error('Invalid campaign');

    global $wpdb;
    $status = get_post_meta($post_id,'_newsletter_status',true);
    $total  = (int)get_post_meta($post_id,'_newsletter_total',true);
    $sent   = (int)get_post_meta($post_id,'_newsletter_sent',true);
    $offset = (int)get_post_meta($post_id,'_newsletter_offset',true);

    if($status==='Completed' || $sent>=$total){
        update_post_meta($post_id,'_newsletter_status','Completed');
        wp_send_json_success([ 'status'=>'Completed','total'=>$total,'sent'=>$sent ]);
    }

    $batch  = (int)get_option('newsletter_batch_size',4);
    $subs   = $wpdb->get_results($wpdb->prepare("SELECT email,ID FROM {$wpdb->prefix}newsletter_subscribers ORDER BY ID ASC LIMIT %d OFFSET %d",$batch,$offset));

    if(!$subs){
        update_post_meta($post_id,'_newsletter_status','Partial');
        wp_send_json_success([ 'status'=>'Partial','total'=>$total,'sent'=>$sent ]);
    }

    $subject = get_the_title($post_id);
    $content_raw = get_post_field('post_content',$post_id);
    $content = wp_kses_post(do_shortcode($content_raw));

    foreach($subs as $s){
        $unsub = base64_encode($s->ID);
        $link  = add_query_arg([ 'newsletter_unsubscribe'=>$unsub ],get_home_url());
        $body  = str_replace('[unsubscribe_link]',esc_url($link),$content);

        wp_mail($s->email,$subject,$body);
    }

    $new_sent   = $sent + count($subs);
    $new_offset = $offset + count($subs);
    update_post_meta($post_id,'_newsletter_sent',$new_sent);
    update_post_meta($post_id,'_newsletter_offset',$new_offset);

    $new_status = ($new_sent >= $total) ? 'Completed' : 'Processing';
    update_post_meta($post_id,'_newsletter_status',$new_status);

    wp_send_json_success([ 'status'=>$new_status,'total'=>$total,'sent'=>$new_sent ]);
}
