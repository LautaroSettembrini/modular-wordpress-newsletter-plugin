<?php
/**
 * Renders and saves the SMTP & batch settings page.
 */
function newsletter_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Save settings
    if (isset($_POST['newsletter_settings_submit']) && check_admin_referer('newsletter_settings_nonce_action','newsletter_settings_nonce_field')) {
        update_option('newsletter_batch_size',      intval($_POST['newsletter_batch_size']));
        update_option('newsletter_sender_name',     sanitize_text_field($_POST['newsletter_sender_name']));
        update_option('newsletter_sender_email',    sanitize_email($_POST['newsletter_sender_email']));
        update_option('newsletter_reply_to',        sanitize_email($_POST['newsletter_reply_to']));
        update_option('newsletter_smtp_host',       sanitize_text_field($_POST['newsletter_smtp_host']));
        update_option('newsletter_smtp_port',       intval($_POST['newsletter_smtp_port']));
        update_option('newsletter_smtp_encryption', sanitize_text_field($_POST['newsletter_smtp_encryption']));
        update_option('newsletter_smtp_user',       sanitize_text_field($_POST['newsletter_smtp_user']));
        update_option('newsletter_smtp_pass',       sanitize_text_field($_POST['newsletter_smtp_pass']));

        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }

    // Current values
    $batch_size   = get_option('newsletter_batch_size', 4);
    $sender_name  = get_option('newsletter_sender_name', 'My Site');
    $sender_email = get_option('newsletter_sender_email', 'noreply@example.com');
    $reply_to     = get_option('newsletter_reply_to', 'info@example.com');
    $smtp_host    = get_option('newsletter_smtp_host', '');
    $smtp_port    = get_option('newsletter_smtp_port', 587);
    $smtp_enc     = get_option('newsletter_smtp_encryption', 'tls');
    $smtp_user    = get_option('newsletter_smtp_user', '');
    $smtp_pass    = get_option('newsletter_smtp_pass', '');
    ?>
    <div class="wrap">
      <h1>Newsletter Settings</h1>
      <form method="POST" action="">
        <?php wp_nonce_field('newsletter_settings_nonce_action','newsletter_settings_nonce_field'); ?>
        <table class="form-table">
          <tr>
            <th scope="row"><label for="newsletter_batch_size">Batch size</label></th>
            <td>
              <input type="number" min="1" name="newsletter_batch_size" value="<?php echo esc_attr($batch_size); ?>" />
              <p class="description">Number of emails per batch.</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_sender_name">Sender name</label></th>
            <td><input type="text" name="newsletter_sender_name" value="<?php echo esc_attr($sender_name); ?>" class="regular-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_sender_email">Sender email</label></th>
            <td><input type="email" name="newsletter_sender_email" value="<?php echo esc_attr($sender_email); ?>" class="regular-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_reply_to">Reply‑to</label></th>
            <td><input type="email" name="newsletter_reply_to" value="<?php echo esc_attr($reply_to); ?>" class="regular-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_smtp_host">SMTP host</label></th>
            <td><input type="text" name="newsletter_smtp_host" value="<?php echo esc_attr($smtp_host); ?>" class="regular-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_smtp_port">SMTP port</label></th>
            <td><input type="number" name="newsletter_smtp_port" value="<?php echo esc_attr($smtp_port); ?>" class="small-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_smtp_encryption">Encryption</label></th>
            <td>
              <select name="newsletter_smtp_encryption">
                <option value=""   <?php selected($smtp_enc,'');   ?>>None</option>
                <option value="ssl"<?php selected($smtp_enc,'ssl'); ?>>SSL</option>
                <option value="tls"<?php selected($smtp_enc,'tls'); ?>>TLS</option>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_smtp_user">SMTP user</label></th>
            <td><input type="text" name="newsletter_smtp_user" value="<?php echo esc_attr($smtp_user); ?>" class="regular-text" /></td>
          </tr>
          <tr>
            <th scope="row"><label for="newsletter_smtp_pass">SMTP password</label></th>
            <td><input type="password" name="newsletter_smtp_pass" value="<?php echo esc_attr($smtp_pass); ?>" class="regular-text" /></td>
          </tr>
        </table>
        <?php submit_button('Save settings','primary','newsletter_settings_submit'); ?>
      </form>
    </div>
    <?php
}
