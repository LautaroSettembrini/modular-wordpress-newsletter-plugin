<?php
/**
 * Configures PHPMailer with SMTP settings from plugin options.
 */
add_action('phpmailer_init','newsletter_configure_phpmailer');
function newsletter_configure_phpmailer($phpmailer){
    $phpmailer->setFrom(get_option('newsletter_sender_email','noreply@example.com'), get_option('newsletter_sender_name','My Site'));
    $reply_to=get_option('newsletter_reply_to');
    if($reply_to) $phpmailer->addReplyTo($reply_to);

    $phpmailer->isHTML(true);

    $host=get_option('newsletter_smtp_host');
    if($host){
        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->Port       = get_option('newsletter_smtp_port',587);
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Username   = get_option('newsletter_smtp_user');
        $phpmailer->Password   = get_option('newsletter_smtp_pass');
        $enc = get_option('newsletter_smtp_encryption','tls');
        $phpmailer->SMTPSecure = $enc ?: '';
    }
}
