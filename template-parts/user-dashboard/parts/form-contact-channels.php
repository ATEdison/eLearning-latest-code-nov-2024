<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? 0;
$data = $args['data'] ?? [];

$user = new WP_User($user_id);
$primary_contact_number = \STA\Inc\UserDashboard::get_user_primary_contact_number($user_id);

?>

<form class="sta-form-contact-channels row" action="" method="post">
    <?php wp_nonce_field('sta_form'); ?>

    <div class="col-12 mb-24 mb-lg-32 d-flex align-items-center">
        <input id="userContactChannelEmail" class="form-check-input mt-0 me-16" type="checkbox" name="contact_channel_email" value="yes" <?php checked(true, ($data['contact_channel_email'] ?? '') == 'yes'); ?>>
        <label for="userContactChannelEmail" class="form-check-label">
            <span class="fw-500 fs-20 d-block lh-1 mb-6"><?php _e('Email', 'sta'); ?></span>
            <span><?php printf(__('Receive notifications at %s', 'sta'), $user->user_email); ?></span>
        </label>
    </div>
    <div class="col-12 mb-24 mb-lg-32 d-flex align-items-center">
        <input id="userContactChannelSMS" class="form-check-input mt-0 me-16" type="checkbox" name="contact_channel_sms" value="yes" <?php checked(true, ($data['contact_channel_sms'] ?? '') == 'yes'); ?>>
        <label for="userContactChannelSMS" class="form-check-label">
            <span class="fw-500 fs-20 d-block lh-1 mb-6"><?php _e('Text Messages', 'sta'); ?></span>
            <span><?php printf(__('Receive notifications on %s', 'sta'), $primary_contact_number); ?></span>
        </label>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_contact_channels"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
