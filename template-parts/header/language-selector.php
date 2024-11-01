<?php

/**
 * @see do_action('wpml_add_language_selector');
 * @var SitePress $sitepress
 */

if (!class_exists('SitePress')) {
    return;
}

global $sitepress;

$language_list = $sitepress->get_ls_languages(['skip_missing' => true]);
$current_language = $sitepress->get_current_language();

// \STA\Inc\Helpers::log($language_list);
?>

<div class="sta-language-selector d-flex align-items-stretch">
    <span class="sta-language-selector-current-language d-flex align-items-center"><?php echo $current_language; ?></span>
    <div class="sta-language-selector-list">
        <ul>
            <?php foreach ($language_list as $code => $settings) {
                if ($current_language == $code) {
                    printf('<li class="active"><span>%1$s (%2$s)</span></li>', $settings['native_name'], strtoupper($code));
                } else {
                    printf('<li><a href="%3$s">%1$s (%2$s)</a></li>', $settings['native_name'], strtoupper($code), $settings['url']);
                }
            } ?>
        </ul>
    </div>
</div>
