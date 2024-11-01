<?php
/**
 * @var array $args
 */

$errors = $args['errors'] ?? null;

$option_list = [
    [
        'value' => 'international_travel_agent',
        'heading' => __('International Travel Agent', 'sta'),
        'desc' => __('I work as a travel agent based outside Saudi', 'sta'),
    ],
    [
        'value' => 'international_tour_operator',
        'heading' => __('International Tour Operator', 'sta'),
        'desc' => __('I work as a tour operator based outside Saudi', 'sta'),
    ],
    [
        'value' => 'domestic_dmc_dmo',
        'heading' => __('Domestic DMC/DMO', 'sta'),
        'desc' => __('I am a Saudi-based DMC or DMO representative', 'sta'),
    ],
];

$data = $_POST;
$checked = $data['your_role'] ?? '';
?>

<fieldset class="row gx-md-20 gx-xxl-40 mb-32 sta-label-black">
    <?php foreach ($option_list as $item):
        $item_id = sprintf('sta_reg_role_%s', md5($item['value'])) ?>
        <div class="col-12 col-md-4 d-flex align-items-stretch mb-16 mb-lg-0">
            <input class="form-check-input" id="<?php echo $item_id; ?>" type="radio" name="your_role" value="<?php echo esc_attr($item['value']); ?>" required <?php checked(true, $checked == $item['value']); ?>>
            <label class="form-check-label sta-registration-role-option p-24 ps-60 w-100" for="<?php echo $item_id; ?>">
                <span class="d-block fs-16 fw-500 lh-1 mb-8"><?php echo $item['heading']; ?></span>
                <span><?php echo $item['desc']; ?></span>
            </label>
        </div>
    <?php endforeach; ?>
    <?php \STA\Inc\FormHelpers::field_error('your_role', $errors); ?>
</fieldset>
