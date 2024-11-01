<?php

// vendor
require_once('vendor/autoload.php');

if (defined('WP_MODE') && WP_MODE == 'local' && file_exists(get_theme_file_path('/libs/mailhog-dev-mode.php'))) {
    require_once 'libs/mailhog-dev-mode.php';
}


// remove "Private: " from titles
function remove_private_prefix($title) {
    $title = str_replace('Private: ', '', $title);
    return $title;
}
add_filter('the_title', 'remove_private_prefix');


// theme setup
\STA\Inc\ThemeSetup::instance();
\STA\Inc\Scripts::instance();
\STA\Inc\UserAuth::instance();
\STA\Inc\UserMenu::instance();
\STA\Inc\UserDashboard::instance();
\STA\Inc\FormHandleInvoker::instance();
\STA\Inc\AdminSaudiMap::instance();
\STA\Inc\PointsSystem::instance();
\STA\Inc\UserWelcome::instance();
\STA\Inc\PDFConverter::instance();
\STA\Inc\GoogleTextToSpeech::instance();
\STA\Inc\EmailSettings::instance();
\STA\Inc\RankingSystem::instance();
\STA\Inc\CarbonFields\ThemeOptions::instance();
\STA\Inc\NotificationSystem::instance();
\STA\Inc\LearndashCompletionRedirect::instance();
\STA\Inc\CarbonFields::instance();
\STA\Inc\CptCourse::instance();
\STA\Inc\CptCourseTopic::instance();
\STA\Inc\UserRedirect::instance();
\STA\Inc\Shortcodes\ShortcodeYoutube::instance();
\STA\Inc\CustomApi::instance();
\STA\Inc\CustomCourseApi::instance();
\STA\Inc\CustomCourseApiFilter::instance();

