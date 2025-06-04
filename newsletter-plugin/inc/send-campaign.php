<?php
/**
 * Admin page UI for sending a campaign in batches.
 */
function newsletter_add_send_page(){
    add_submenu_page(null,'Send Campaign','Send Campaign','manage_options','newsletter-send','newsletter_send_page_callback');
}
add_action('admin_menu','newsletter_add_send_page');

function newsletter_send_page_callback(){
    if(!current_user_can('manage_options')) wp_die('No permission');
    if(!isset($_GET['camp_id'])) { echo '<h1>Error: no campaign</h1>'; return; }

    $post_id = intval($_GET['camp_id']);
    if(get_post_type($post_id)!=='campaign'){ echo '<h1>Invalid campaign</h1>'; return; }

    $status = get_post_meta($post_id,'_newsletter_status',true) ?: 'Not sent';
    $total  = (int)get_post_meta($post_id,'_newsletter_total',true);
    $sent   = (int)get_post_meta($post_id,'_newsletter_sent',true);

    echo '<div class="wrap"><h1>Send Campaign: '.esc_html(get_the_title($post_id)).'</h1>';
    echo "<p><strong>Status:</strong> {$status}</p><p><strong>Progress:</strong> {$sent}/{$total}</p>";
    echo '<button id="newsletter-init-send" class="button button-primary">Start / Restart</button> ';
    echo '<button id="newsletter-continue-send" class="button">Continue</button>';

    echo '<div id="newsletter-progress" style="margin-top:20px;border:1px solid #ccc;width:400px;height:24px;position:relative;">';
    echo '<div id="newsletter-progress-bar" style="background:#2ecc71;height:100%;width:0%;"></div></div>';
    echo '<p id="newsletter-progress-text"></p>';

    $nonce = wp_create_nonce('newsletter_ajax_send_nonce');
    $ajax  = admin_url('admin-ajax.php');
    ?>
    <script>
    (function($){
        var camp_id = <?php echo $post_id; ?>;
        var ajaxUrl = '<?php echo $ajax; ?>';
        var security = '<?php echo $nonce; ?>';

        function updateBar(sent,total){
            var pct = total>0 ? (sent/total)*100 : 0;
            $('#newsletter-progress-bar').css('width',pct+'%');
            $('#newsletter-progress-text').text(sent+' / '+total+' emails');
        }

        function sendBatch(){
            $.post(ajaxUrl,{action:'newsletter_ajax_send_batch',security:security,camp_id:camp_id},function(resp){
                if(!resp.success){ alert(resp.data || 'Error'); return; }
                updateBar(resp.data.sent,resp.data.total);
                if(resp.data.status==='Processing') setTimeout(sendBatch,4000);
                else if(resp.data.status==='Completed') alert('Done!');
            });
        }

        $('#newsletter-init-send').click(function(){
            $.post(ajaxUrl,{action:'newsletter_ajax_init',security:security,camp_id:camp_id},function(resp){
                if(!resp.success){alert(resp.data || 'Init error');return;}
                updateBar(resp.data.sent,resp.data.total);
                sendBatch();
            });
        });

        $('#newsletter-continue-send').click(sendBatch);
    })(jQuery);
    </script>
    <?php
    echo '</div>';
}
