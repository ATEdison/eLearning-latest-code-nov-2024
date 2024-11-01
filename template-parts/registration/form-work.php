<?php
/**
 * @var array $args
 */
// printf('<pre>%s</pre>', var_export($args, true));

$errors = $args['errors'] ?? null;
$data = $_POST;
?>
<div class="row">
    <div class="col-12 col-md-6 mb-32">
        <label for="sta_reg_work_company" class="form-label"><?php _e('Company Name *', 'sta'); ?></label>
        <input id="sta_reg_work_company" class="form-control" type="text" name="work_company_name" value="<?php echo esc_attr($data['work_company_name'] ?? ''); ?>" required>
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label class="form-label" for="sta_reg_work_company_type"><?php _e('Company Type *', 'sta'); ?></label>
        <select class="form-select" id="sta_reg_work_company_type" name="work_company_type" required>
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
    </div>
    <div class="col-12 col-md-6 mb-32">
        <label for="sta_reg_work_contact" class="form-label"><?php _e('Contact Number', 'sta'); ?></label>
        <input id="sta_reg_work_contact" class="form-control" type="text" name="work_contact_number" value="<?php echo esc_attr($data['work_contact_number'] ?? ''); ?>">
    </div>
    <div class="col-12 col-md-6 mb-56 mb-xl-80">
        <label for="sta_reg_work_website" class="form-label"><?php _e('Website', 'sta'); ?></label>
        <input id="sta_reg_work_website" class="form-control" type="text" name="work_website" value="<?php echo esc_attr($data['work_website'] ?? ''); ?>">
    </div>
    <div class="col-12">
        <div class="border-top pt-56 pt-xl-80">
            <div class="row">
                <div class="col-12 col-md-6 mb-32">
                    <label for="workStreetAddress" class="form-label"><?php _e('Street Address *', 'sta'); ?></label>
                    <input id="workStreetAddress" class="form-control" type="text" name="work_street_address" value="<?php echo esc_attr($data['work_street_address'] ?? ''); ?>" required>
                    <?php \STA\Inc\FormHelpers::field_error('work_street_address', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-32">
                    <label for="workAddress2" class="form-label"><?php _e('Apartment / Unit / Suite Number', 'sta'); ?></label>
                    <input id="workAddress2" class="form-control" type="text" name="work_apartment_unit_suite_number" value="<?php echo esc_attr($data['work_apartment_unit_suite_number'] ?? ''); ?>">
                    <?php \STA\Inc\FormHelpers::field_error('work_apartment_unit_suite_number', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-32">
                    <label for="workTownCity" class="form-label"><?php _e('Town / City *', 'sta'); ?></label>
                    <input id="workTownCity" class="form-control" type="text" name="work_town_city" value="<?php echo esc_attr($data['work_town_city'] ?? ''); ?>" required>
                    <?php \STA\Inc\FormHelpers::field_error('work_town_city', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-32">
                    <label for="workStateRegion" class="form-label"><?php _e('State / Region *', 'sta'); ?></label>
                    <input id="workStateRegion" class="form-control" type="text" name="work_state_region" value="<?php echo esc_attr($data['work_state_region'] ?? ''); ?>" required>
                    <?php \STA\Inc\FormHelpers::field_error('work_state_region', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-32">
                    <label for="workPostcode" class="form-label"><?php _e('Postcode *', 'sta'); ?></label>
                    <input id="workPostcode" class="form-control" type="text" name="work_postcode" value="<?php echo esc_attr($data['work_postcode'] ?? ''); ?>" required>
                    <?php \STA\Inc\FormHelpers::field_error('work_postcode', $errors); ?>
                </div>
                <div class="col-12 col-md-6 mb-32">
                    <label for="workCountry" class="form-label"><?php _e('Country *', 'sta'); ?></label>
                    <select id="workCountry" class="form-select" name="work_country" required>
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
</div>
