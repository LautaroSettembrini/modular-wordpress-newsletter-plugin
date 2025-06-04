<?php
/**
 * UI page to send a test campaign to the test list.
 */
function newsletter_add_test_send_page(){
    add_submenu_page(null,'Send Test Campaign','Send Test Campaign','manage_options','newsletter-test-send','newsletter_test_send_page_callback');
}
add_action('admin_menu','newsletter_add_test_send_page');

function newsletter_test_send_page_callback(){
    if(!current_user_can('manage_options')) wp_die('No permission');
    if(!isset($_GET['camp_id'])){ echo '<h1>Error</h1>'; return; }

    $post_id=intval($_GET['camp_id']);
    if(get_post_type($post_id)!=='campaign'){ echo '<h1>Invalid</h1>'; return; }

    $status=get_post_meta($post_id,'_newsletter_test_status',true) ?: 'Not sent';
    $total =(int)get_post_meta($post_id,'_newsletter_test_total',true);
    $sent  =(int)get_post_meta($post_id,'_newsletter_test_sent',true);

    echo '<div class="wrap"><h1>Send Test: '.esc_html(get_the_title($post_id)).'</h1>';
    echo "<p><strong>Status:</strong> {$status}</p><p><strong>Progress:</strong> {$sent}/{$total}</p>";
    echo '<button id="newsletter-init-test" class="button button-primary">Start / Restart</button> ';
    echo '<button id="newsletter-continue-test" class="button">Continue</button>';

    echo '<div id="newsletter-test-bar" style="margin-top:20px;border:1px solid #ccc;width:400px;height:24px;"><div style="background:#3498db;width:0;height:100%;" id="newsletter-test-bar-inner"></div></div>';
    echo '<p id="newsletter-test-text"></p>';

    $nonce = wp_create_nonce('newsletter_ajax_test_send_nonce');
    $ajax  = admin_url('admin-ajax.php');
    ?>
    <script>
    (function($){
        var camp = <?php echo $post_id; ?>;
        var ajaxUrl='<?php echo $ajax; ?>', sec='<?php echo $nonce; ?>';

        function upd(s,t){
            var pct = t>0?(s/t)*100:0;
            $('#newsletter-test-bar-inner').css('width',pct+'%');
            $('#newsletter-test-text').text(s+' / '+t+' emails');
        }

        function batch(){
            $.post(ajaxUrl,{action:'newsletter_ajax_send_batch_test',security:sec,camp_id:camp},function(r){
                if(!r.success){ alert(r.data||'Error');return; }
                upd(r.data.sent,r.data.total);
                if(r.data.status==='Processing') setTimeout(batch,4000);
                else if(r.data.status==='Completed') alert('Done!');
            });
        }

        $('#newsletter-init-test').click(function(){
            $.post(ajaxUrl,{action:'newsletter_ajax_init_test',security:sec,camp_id:camp},function(r){
                if(!r.success){alert(r.data||'Init error');return;}
                upd(r.data.sent,r.data.total); batch();
            });
        });

        $('#newsletter-continue-test').click(batch);
    })(jQuery);
    </script>
    <?php
    echo '</div>';
}
