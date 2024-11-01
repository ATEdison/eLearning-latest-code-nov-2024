<?php

/**
 * mailhog for dev mode
 * also install this plugin https://wordpress.org/plugins/wp-mail-smtp/
 */
function mailhog_phpmailer_init($phpmailer) {
    $phpmailer->Host = 'mailhog';
    $phpmailer->Port = 1025;
    $phpmailer->IsSMTP();
}

add_action('phpmailer_init', 'mailhog_phpmailer_init');