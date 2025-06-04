<?php
/**
 * Adds buttons to the campaign editor to insert the email template and a featured note.
 */
function add_template_button(){
    global $post;
    if(isset($post->post_type) && $post->post_type==='campaign'){
        echo '<button type="button" onclick="addTemplate()" class="button">Insert Template</button> ';
        echo '<button type="button" id="insertFeaturedNoteBtn" class="button">Insert Featured Note</button>';

        // Modal & JS (simplified) – uses same logic as original plugin
        ?>
        <div id="featuredNoteModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:20px;">
                <h2>Insert Featured Note</h2>
                <input type="text" id="featuredNoteURL" style="width:100%;" placeholder="https://example.com/article"/>
                <button id="confirmInsertFeaturedNote" class="button button-primary">Insert</button>
                <button id="closeFeaturedNoteModal" class="button">Close</button>
            </div>
        </div>
        <script>
        (function($){
            window.addTemplate=function(){
                fetch('<?php echo plugin_dir_url(dirname(__FILE__)); ?>templates/email-template.html')
                .then(r=>r.text()).then(t=>{
                    if(tinymce.get('content')) tinymce.get('content').setContent(t);
                });
            };

            $('#insertFeaturedNoteBtn').on('click',()=>$('#featuredNoteModal').show());
            $('#closeFeaturedNoteModal').on('click',()=>$('#featuredNoteModal').hide());

            $('#confirmInsertFeaturedNote').on('click',function(){
                var url=$('#featuredNoteURL').val().trim();
                if(!url){ alert('URL?'); return; }
                $.post(ajaxurl,{
                    action:'get_featured_note',
                    security:'<?php echo wp_create_nonce('get_featured_note_nonce'); ?>',
                    link:url
                },function(r){
                    if(!r.success){ alert(r.data||'Error'); return; }
                    var snippet=r.data;
                    $.post(ajaxurl,{
                        action:'newsletter_ajax_update_campaign_content',
                        security:'<?php echo wp_create_nonce('update_campaign_nonce'); ?>',
                        camp_id:<?php echo $post->ID; ?>,
                        snippet:snippet
                    },function(r2){
                        if(r2.success && tinymce.get('content')){
                            tinymce.get('content').setContent(r2.data);
                            $('#featuredNoteModal').hide(); $('#featuredNoteURL').val('');
                        }
                    });
                });
            });
        })(jQuery);
        </script>
        <?php
    }
}
add_action('edit_form_after_title','add_template_button');

/** Hide SEO metabox in campaign editor to keep UI clean */
add_action('admin_footer',function(){
    global $post_type;
    if($post_type==='campaign'){
        echo '<style>#postbox-container-2{display:none !important;}</style>';
    }
});
