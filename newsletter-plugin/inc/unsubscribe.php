<?php
/**
 * Automatic unsubscribe endpoint.
 */
add_action('init',function(){
    if(isset($_GET['newsletter_unsubscribe'])){
        global $wpdb;
        $id=base64_decode($_GET['newsletter_unsubscribe']);
        $wpdb->delete($wpdb->prefix.'newsletter_subscribers',[ 'ID'=>$id ],[ '%d' ]);
        wp_die('<h1>Unsubscribed</h1><p>You have been removed from the list.</p>','Unsubscribed',['response'=>200]);
    }
});
