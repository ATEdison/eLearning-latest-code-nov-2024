<?php

/**
 * @var array $args
 */

$form_handler = \STA\Inc\FormHandleInvoker::user_password_form_handler();
$errors = $form_handler->get_errors();
$global_errors = $form_handler->get_global_errors();
$reset_password_page_id = \STA\Inc\CarbonFields\ThemeOptions::get_reset_password_page_id();
$success = $form_handler->is_success();
?>

<form class="sta-form-password row" action="" method="post">
    <?php wp_nonce_field('sta_form'); ?>

    <?php if ($success): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-success mb-0">
                <?php _e('Your password has been changed!', 'sta'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_array($global_errors) && !empty($global_errors)): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-danger mb-0">
                <ul>
                    <?php foreach ($global_errors as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12 col-xxl-6">
        <div class="mb-24 mb-lg-32">
            <label for="securityPasswordCurrent" class="form-label"><?php _e('Current Password *', 'sta'); ?></label>
            <input id="securityPasswordCurrent" class="form-control" type="password" name="password_current" required>
            <?php \STA\Inc\FormHelpers::field_error('password_current', $errors); ?>
            <div class="mt-16 fs-14"><a href="<?php echo get_permalink($reset_password_page_id); ?>"><?php _e('Forgotten your password?', 'sta'); ?></a></div>
        </div>
        <div class="mb-24 mb-lg-32">
            <?php get_template_part('template-parts/form/field-password', '', [
                'label' => __('New Password *', 'sta'),
                'field_name' => 'password_new',
                'errors' => $errors,
            ]); ?>
        </div>
        <div class="mb-24 mb-lg-32">
            <label for="securityPasswordRepeat" class="form-label"><?php _e('Confirm New Password *', 'sta'); ?></label>
            <input id="securityPasswordRepeat" class="form-control" type="password" name="password_confirm" required>
            <?php \STA\Inc\FormHelpers::field_error('password_confirm', $errors); ?>
        </div>
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_password"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
