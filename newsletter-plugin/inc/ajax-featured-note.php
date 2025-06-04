<?php
/**
 * AJAX helpers for featured note snippet and updating campaign content.
 */
add_action('wp_ajax_get_featured_note','get_featured_note');
function get_featured_note(){
    check_ajax_referer('get_featured_note_nonce','security');
    $link=esc_url_raw($_POST['link'] ?? '');
    if(!$link) wp_send_json_error('Empty URL');

    $post_id=url_to_postid($link);
    if(!$post_id) wp_send_json_error('Post not found');

    $title  = get_the_title($post_id);
    $thumb  = get_the_post_thumbnail_url($post_id,'full') ?: 'https://via.placeholder.com/600x300?text=No+Image';
    $excerpt= wp_trim_words(strip_tags(get_post_field('post_excerpt',$post_id) ?: get_post_field('post_content',$post_id)),50,'...');

    $snippet = '<div><h2>Featured Note</h2><a href="'.$link.'"><img src="'.$thumb.'" alt="'.esc_attr($title).'" style="max-width:100%;height:auto;"></a><h3><a href="'.$link.'">'.$title.'</a></h3><p>'.$excerpt.'</p></div>';
    wp_send_json_success($snippet);
}

add_action('wp_ajax_newsletter_ajax_update_campaign_content','newsletter_ajax_update_campaign_content');
function newsletter_ajax_update_campaign_content(){
    check_ajax_referer('update_campaign_nonce','security');
    $post_id = intval($_POST['camp_id']);
    $snippet = $_POST['snippet'] ?? '';
    if(get_post_type($post_id)!=='campaign' || !$snippet) wp_send_json_error('Invalid');

    $content = get_post_field('post_content',$post_id);
    $marker  = '<!-- Lo mas leido -->';
    if(strpos($content,$marker)!==false){
        $new_content = str_replace($marker,$snippet."
".$marker,$content);
    } else {
        $new_content = $content."
".$snippet;
    }
    wp_update_post([ 'ID'=>$post_id,'post_content'=>$new_content ]);
    wp_send_json_success($new_content);
}
