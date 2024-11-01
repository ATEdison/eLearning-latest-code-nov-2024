<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? 0;
$data = $args['data'] ?? [];

$user = new WP_User($user_id);

?>

<form class="sta-form-contact-channels row" action="" method="post">
    <?php wp_nonce_field('sta_form'); ?>

    <div class="col-12 mb-24 mb-lg-32 d-flex align-items-center">
        <input id="newsletterSaudiExpert" class="form-check-input mt-0 me-16" type="checkbox" name="newsletter_saudi_expert" value="yes" <?php checked(true, ($data['newsletter_saudi_expert'] ?? '') == 'yes'); ?>>
        <label for="newsletterSaudiExpert" class="form-check-label">
            <span class="fw-500 fs-20 d-block lh-1 mb-6"><?php _e('Saudi Expert newsletter', 'sta'); ?></span>
            <span><?php _e('General updates on the Saudi Expert program', 'sta'); ?></span>
        </label>
    </div>
    <div class="col-12 mb-24 mb-lg-32 d-flex align-items-center">
        <input id="newsletterNewsEvents" class="form-check-input mt-0 me-16" type="checkbox" name="newsletter_news_events" value="yes" <?php checked(true, ($data['newsletter_news_events'] ?? '') == 'yes'); ?>>
        <label for="newsletterNewsEvents" class="form-check-label">
            <span class="fw-500 fs-20 d-block lh-1 mb-6"><?php _e('News & Events newsletter', 'sta'); ?></span>
            <span><?php _e('Latest news and events from Saudi Tourism', 'sta'); ?></span>
        </label>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_newsletters_settings"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
