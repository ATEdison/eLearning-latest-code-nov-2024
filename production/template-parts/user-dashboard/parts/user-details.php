<?php

/**
 * @var array $args
 */

$data = $args['data'];
$field_list = $args['field_list'];

foreach ($field_list as $field => $label):
    $value = isset($data[$field]) ? $data[$field] : '';

    switch ($field) {
        case 'country':
            $value = \STA\Inc\UserDashboard::get_country_label($value);
            $value = esc_attr($value);
            break;
        case 'profile_image':
            // $value = null;
            $value = sprintf('<div class="user-profile-placeholder-wrapper">%s</div>', \STA\Inc\UserDashboard::profile_image($value));
            break;
        case 'work_address':
            $work_street_address = $data['work_street_address'] ?? '';
            $work_apartment_unit_suite_number = $data['work_apartment_unit_suite_number'] ?? '';
            $work_town_city = $data['work_town_city'] ?? '';
            $work_state_region = $data['work_state_region'] ?? '';
            $work_postcode = $data['work_postcode'] ?? '';
            $work_country = $data['work_country'] ?? '';

            $line1 = $work_street_address;
            $line2 = $work_apartment_unit_suite_number;
            $line3 = '';
            if ($work_town_city && $work_postcode) {
                $line3 = $work_town_city . ', ' . $work_postcode;
            } else if ($work_town_city) {
                $line3 = $work_town_city;
            } else {
                $line3 = $work_postcode;
            }
            $line4 = $work_state_region;
            $line5 = \STA\Inc\UserDashboard::get_country_label($work_country);

            $value = esc_attr($line1);

            if ($line2) {
                $value .= ($value ? '<br>' : '') . esc_attr($line2);
            }

            if ($line3) {
                $value .= ($value ? '<br>' : '') . esc_attr($line3);
            }

            if ($line4) {
                $value .= ($value ? '<br>' : '') . esc_attr($line4);
            }

            if ($line5) {
                $value .= ($value ? '<br>' : '') . esc_attr($line5);
            }
            break;
        default:
            $value = esc_attr($value);
            break;
    } ?>
    <div class="sta-user-details-content-item <?php echo $field; ?> row mb-20 mb-lg-25">
        <div class="col-12 col-md-4 col-lg-5 col-xl-4 col-xxl-3 fs-14 fw-500 mb-8 mb-md-0"><?php echo $label; ?></div>
        <div class="col-12 col-md-8 col-lg-7 col-xl-8 col-xxl-9"><?php echo $value; ?></div>
    </div>
<?php endforeach; ?>
