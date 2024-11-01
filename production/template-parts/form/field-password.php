<?php

/**
 * @var array $args
 */

wp_enqueue_script('password-strength-meter');

$label = $args['label'];
$field_name = $args['password'] ?? 'password';
$errors = $args['errors'] ?? null;

?>

<div class="sta-password">
    <label class="form-label" for="rp_password"><?php echo $label; ?></label>
    <input class="form-control" type="password" id="rp_password" name="<?php echo $field_name; ?>" required>
    <?php \STA\Inc\FormHelpers::field_error($field_name, $errors); ?>
    <div class="sta-password-strength fs-14 p-24 p-lg-40">
        <div class="d-block mb-16 fw-500"><?php _e('Your password must:', 'sta'); ?></div>
        <ul class="mb-24">
            <li data-type="min_length"><?php _e('Be at least 6 characters long', 'sta'); ?></li>
            <li data-type="lowercase"><?php _e('Contain at least one lowercase letter', 'sta'); ?></li>
            <li data-type="uppercase"><?php _e('Contain at least one uppercase letter', 'sta'); ?></li>
            <li data-type="digit"><?php _e('Contain at least one digit', 'sta'); ?></li>
            <li data-type="special"><?php _e('One special character ~!@#$%^&*()_+', 'sta'); ?></li>
        </ul>
        <div class="progress mb-16">
            <div class="progress-bar" data-score="1" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="fw-500"><span><?php _e('Strength:', 'sta'); ?> </span><span class="sta-password-strength-desc"></span></div>
    </div>
</div>
