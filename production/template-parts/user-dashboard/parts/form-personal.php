<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? 0;
$data = $args['data'] ?? [];

// profile image
$profile_image = $data['profile_image'] ?? null;

if (!$user_id) {
    printf('<div>Invalid user!</div>');
    return;
}

$form_handler = \STA\Inc\FormHandleInvoker::personal_form_handler();
$success = $form_handler->is_success();
$errors = $form_handler->get_errors();
$global_errors = $form_handler->get_global_errors();
$post_data = $form_handler->get_post_data();

$data = $post_data + $data;

// printf('<pre>%s</pre>', var_export([
//     '$global_errors' => $global_errors,
//     '$errors' => $errors,
//     '$post_data' => $post_data,
//     '$data' => $data,
// ], true));
?>
<form class="sta-form-personal row" action="" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('sta_form'); ?>

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

    <?php if (is_array($errors) && !empty($errors)): ?>
        <div class="col-12 mb-40">
            <?php get_template_part('template-parts/alert', '', [
                'message' => __('<strong>Updated failed!</strong> Please check your input.', 'sta'),
                'type' => 'danger',
                'dismissible' => true,
            ]); ?>
        </div>
    <?php endif; ?>

    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalFirstName" class="form-label"><?php _e('First Name *', 'sta'); ?></label>
        <input id="personalFirstName" class="form-control" type="text" name="first_name" value="<?php echo esc_attr($data['first_name'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('first_name', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalLastName" class="form-label"><?php _e('Last Name *', 'sta'); ?></label>
        <input id="personalLastName" class="form-control" type="text" name="last_name" value="<?php echo esc_attr($data['last_name'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('last_name', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalEmail" class="form-label"><?php _e('Email Address *', 'sta'); ?></label>
        <input id="personalEmail" class="form-control" type="email" value="<?php echo esc_attr($data['email'] ?? ''); ?>" readonly>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalCountry" class="form-label"><?php _e('Country *', 'sta'); ?></label>
        <select id="personalCountry" class="form-select" name="country">
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
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalPrimaryContactNumber" class="form-label"><?php _e('Primary Contact Number *', 'sta'); ?></label>
        <input id="personalPrimaryContactNumber" class="form-control" type="text" name="primary_contact_number" value="<?php echo esc_attr($data['primary_contact_number'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('primary_contact_number', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="personalMobileNumber" class="form-label"><?php _e('Mobile Number', 'sta'); ?></label>
        <input id="personalMobileNumber" class="form-control" type="text" name="mobile_number" value="<?php echo esc_attr($data['mobile_number'] ?? ''); ?>">
    </div>
    <div class="col-12 mb-30 mb-lg-40">
        <?php get_template_part('template-parts/form/field-image', '', [
            'field_name' => 'profile_image',
            'label' => __('Profile Picture', 'sta'),
            'image_id' => $profile_image,
            'errors' => $errors,
        ]); ?>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="personal"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
