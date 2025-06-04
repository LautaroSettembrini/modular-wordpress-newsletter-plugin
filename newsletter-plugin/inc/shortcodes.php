<?php
/**
 * Ajax helpers and generator for [ultimas_5_notas] & [proximos_eventos]
 */
function get_latest_notes(){
    $one_week_ago = date('Y-m-d H:i:s',strtotime('-1 week'));
    $args=[ 'date_query'=>[['after'=>$one_week_ago]], 'posts_per_page'=>8, 'meta_key'=>'post_views_count', 'orderby'=>'meta_value_num', 'order'=>'DESC' ];
    $posts=get_posts($args);
    $html='';
    foreach($posts as $p){
        $excerpt = wp_trim_words(strip_tags($p->post_content),25,'...');
        $thumb   = get_the_post_thumbnail_url($p->ID,'thumbnail');
        $html.='<div style="margin-bottom:20px;overflow:hidden;">';
        if($thumb){
            $html.='<div style="float:left;margin-right:10px;width:120px;"><a href="'.get_permalink($p->ID).'"><img src="'.esc_url($thumb).'" style="max-width:100%;height:auto;"></a></div>';
        }
        $html.='<h3 style="margin:0 0 5px 0;"><a href="'.get_permalink($p->ID).'"><strong>'.esc_html($p->post_title).'</strong></a></h3>';
        $html.='<p style="margin:0;">'.$excerpt.'</p></div>';
    }
    return $html;
}

add_action('wp_ajax_get_latest_posts','ajax_get_latest_posts');
function ajax_get_latest_posts(){ echo get_latest_notes(); wp_die(); }

add_action('wp_ajax_get_proximos_eventos','ajax_get_proximos_eventos');
add_action('wp_ajax_nopriv_get_proximos_eventos','ajax_get_proximos_eventos');
function ajax_get_proximos_eventos(){ echo do_shortcode('[proximos_eventos]'); wp_die(); }
