<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? 0;
$data = $args['data'] ?? [];

if (!$user_id) {
    printf('<div>Invalid user!</div>');
    return;
}

$form_handler = \STA\Inc\FormHandleInvoker::user_work_details_form_handler();
$errors = $form_handler->get_errors();
$global_errors = $form_handler->get_global_errors();
$post_data = $form_handler->get_post_data();

$data = $post_data + $data;

// printf('<pre>%s</pre>', var_export([
//     '$errors' => $errors,
//     '$post_data' => $post_data,
//     '$data' => $data,
// ], true));
?>
<form class="sta-form-user-work-details row" action="#sta-user-details-work" method="post">
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
        <label for="workCompanyName" class="form-label"><?php _e('Company Name *', 'sta'); ?></label>
        <input id="workCompanyName" class="form-control" type="text" name="work_company_name" value="<?php echo esc_attr($data['work_company_name'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('work_company_name', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="workCompanyType" class="form-label"><?php _e('Company Type *', 'sta'); ?></label>
        <select id="workCompanyType" class="form-select" name="work_company_type">
            <?php $option_list = \STA\Inc\UserDashboard::company_type_options();
            $selected = $data['work_company_type'] ?? '';
            foreach ($option_list as $value => $label) {
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
        <label for="workContactNumber" class="form-label"><?php _e('Contact Number', 'sta'); ?></label>
        <input id="workContactNumber" class="form-control" type="text" name="work_contact_number" value="<?php echo esc_attr($data['work_contact_number'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('work_contact_number', $errors); ?>
    </div>
    <div class="col-12 col-md-6 mb-24 mb-lg-32">
        <label for="workWebsite" class="form-label"><?php _e('Website', 'sta'); ?></label>
        <input id="workWebsite" class="form-control" type="text" name="work_website" value="<?php echo esc_attr($data['work_website'] ?? ''); ?>">
        <?php \STA\Inc\FormHelpers::field_error('work_website', $errors); ?>
    </div>
    <div class="col-12 mb-30 mb-lg-40">
        <div class="border-y py-30 py-lg-40">
            <div class="row">
                <div class="col-12 col-md-6 mb-24 mb-lg-32">
                    <label for="workStreetAddress" class="form-label"><?php _e('Street Address *', 'sta'); ?></label>
                    <input id="workStreetAddress" class="form-control" type="text" name="work_street_address" value="<?php echo esc_attr($data['work_street_address'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_street_address', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-24 mb-lg-32">
                    <label for="workAddress2" class="form-label"><?php _e('Apartment / Unit / Suite Number', 'sta'); ?></label>
                    <input id="workAddress2" class="form-control" type="text" name="work_apartment_unit_suite_number" value="<?php echo esc_attr($data['work_apartment_unit_suite_number'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_apartment_unit_suite_number', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-24 mb-lg-32">
                    <label for="workTownCity" class="form-label"><?php _e('Town / City *', 'sta'); ?></label>
                    <input id="workTownCity" class="form-control" type="text" name="work_town_city" value="<?php echo esc_attr($data['work_town_city'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_town_city', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-24 mb-lg-32">
                    <label for="workStateRegion" class="form-label"><?php _e('State / Region *', 'sta'); ?></label>
                    <input id="workStateRegion" class="form-control" type="text" name="work_state_region" value="<?php echo esc_attr($data['work_state_region'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_state_region', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-24 mb-lg-0">
                    <label for="workPostcode" class="form-label"><?php _e('Postcode *', 'sta'); ?></label>
                    <input id="workPostcode" class="form-control" type="text" name="work_postcode" value="<?php echo esc_attr($data['work_postcode'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_postcode', $errors); ?>
                </div>
                <div class="col-12 col-md-6">
                    <label for="workCountry" class="form-label"><?php _e('Country *', 'sta'); ?></label>
                    <select id="workCountry" class="form-select" name="work_country">
                        <?php $country_options = \STA\Inc\UserDashboard::country_options();
                        $selected = $data['work_country'] ?? '';
                        foreach ($country_options as $value => $label) {
                            printf(
                                '<option value="%1$s" %2$s>%3$s</option>',
                                $value,
                                selected($selected, $value, false),
                                $label
                            );
                        } ?>
                    </select>
                    <?php \STA\Inc\FormHelpers::field_error('work_country', $errors); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_work_details"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
