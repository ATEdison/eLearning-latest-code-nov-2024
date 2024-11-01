<?php

/**
 * @var array $args
 */

$form_handler = \STA\Inc\FormHandleInvoker::user_invitations_form_handler();
$errors = $form_handler->get_errors();
$global_errors = $form_handler->get_global_errors();
$post_data = $form_handler->get_post_data();
$success = $form_handler->is_success();

$ref_url = esc_attr('https://saudiexpert.gov.sa/adamsmith');
?>

<form class="sta-form-invitations row" action="" method="post">
    <?php wp_nonce_field('sta_form'); ?>

    <?php if ($success): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-success mb-0">
                <?php _e('Thank you, the invitations have been sent!', 'sta'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_array($global_errors) && !empty($global_errors)): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-danger mb-0">
                <ul class="mb-0">
                    <?php foreach ($global_errors as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <div class="mb-40">Invite friends and colleagues to complete their Saudi Expert accreditation. For each successful referral, you'll receive... lorem ipsum dolor sit amet, consectetur adipiscing elit. For more details, see the referral incentives page.</div>
        <div class="mb-24">
            <label for="invitationAddresses" class="form-label"><?php _e('Email Addresses', 'sta'); ?></label>
            <div class="d-md-flex align-items-md-start">
                <input id="invitationAddresses" class="form-control mb-16 mb-md-0 me-md-16" type="text" name="email_addresses" value="<?php echo implode(',', $post_data['email_addresses'] ?? []); ?>">
                <button type="submit" class="btn btn-outline-green w-100 w-md-auto text-nowrap" name="sta_form" value="user_invitations"><?php _e('Send invitations', 'sta'); ?></button>
            </div>
            <?php \STA\Inc\FormHelpers::field_error('email_addresses', $errors); ?>
        </div>
        <div class="sta-copy p-24 p-md-32">
            <div class="mb-24">Or copy and share your personal referral link:</div>
            <div class="d-flex">
                <div class="text-secondary fw-500 me-16 text-wrap d-flex align-items-center"><?php echo $ref_url; ?></div>
                <button type="button" class="btn btn-copy js-copy-on-click" aria-label="<?php _e('Copied!', 'sta'); ?>" data-clipboard-text="<?php echo htmlentities($ref_url); ?>">
                    <span class="icon"></span>
                </button>
            </div>
        </div>
    </div>
</form>
