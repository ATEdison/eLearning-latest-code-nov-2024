<?php
/**
 * @var array $args
 */

$errors = $args['errors'] ?? null;
$data = $_POST;
?>

<div class="row">
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_first_name"><?php _e('First Name *', 'sta'); ?></label>
        <input class="form-control" id="sta_reg_first_name" type="text" name="first_name" value="<?php echo esc_attr($data['first_name'] ?? ''); ?>" required>
        <?php \STA\Inc\FormHelpers::field_error('first_name', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_last_name"><?php _e('Last Name *', 'sta'); ?></label>
        <input class="form-control" id="sta_reg_last_name" type="text" name="last_name" value="<?php echo esc_attr($data['last_name'] ?? ''); ?>" required>
        <?php \STA\Inc\FormHelpers::field_error('last_name', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_email"><?php _e('Email Address *', 'sta'); ?></label>
        <input class="form-control" id="sta_reg_email" type="email" name="user_login" value="<?php echo esc_attr($data['user_login'] ?? ''); ?>" required>
        <?php \STA\Inc\FormHelpers::field_error('user_login', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_country"><?php _e('Country *', 'sta'); ?></label>
        <select class="form-select" id="sta_reg_country" name="country" required>
            <?php $country_options = \STA\Inc\UserDashboard::country_options();
            $selected = $data['country'] ?? '';
            foreach ($country_options as $value => $label) {
                printf(
                    '<option value="%1$s" %2$s>%3$s</option>',
                    $value,
                    selected($selected, $value, false),
                    $label
                );
            } ?>
        </select>
        <?php \STA\Inc\FormHelpers::field_error('country', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label for="sta_reg_primary_contact" class="form-label"><?php _e('Primary Contact Number *', 'sta'); ?></label>
        <input id="sta_reg_primary_contact" class="form-control" type="text" name="primary_contact_number" value="<?php echo esc_attr($data['primary_contact_number'] ?? ''); ?>" required>
        <?php \STA\Inc\FormHelpers::field_error('primary_contact_number', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label for="sta_reg_mobile" class="form-label"><?php _e('Mobile Number', 'sta'); ?></label>
        <input id="sta_reg_mobile" class="form-control" type="text" name="mobile_number" value="<?php echo esc_attr($data['mobile_number'] ?? ''); ?>">
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_language"><?php _e('Preferred Language *', 'sta'); ?></label>
        <select class="form-select" id="sta_reg_language" name="preferred_language" required>
            <option value="en">English (EN)</option>
        </select>
        <?php \STA\Inc\FormHelpers::field_error('preferred_language', $errors); ?>
    </div>
    <div class="col-12 mb-32">
        <?php get_template_part('template-parts/form/field-image', '', [
            'field_name' => 'profile_image',
            'label' => __('Profile Picture', 'sta'),
            'image_id' => 0,
            'errors' => null,
        ]); ?>
    </div>
    <div class="col-12 col-md-6 mb-32 sta-password">
        <?php get_template_part('template-parts/form/field-password', '', [
            'label' => __('Set Password *', 'sta'),
            'errors' => $errors,
        ]); ?>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label for="sta_reg_password_confirm" class="form-label"><?php _e('Confirm Password *', 'sta'); ?></label>
        <input id="sta_reg_password_confirm" class="form-control" type="password" name="password_confirm" value="<?php echo esc_attr($data['password_confirm'] ?? ''); ?>" required>
        <?php \STA\Inc\FormHelpers::field_error('password_confirm', $errors); ?>
    </div>
</div>
