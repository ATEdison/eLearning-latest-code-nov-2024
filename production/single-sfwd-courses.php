<?php

get_header();

if (have_posts()) {
    // remove learndash content
    remove_filter('the_content', [SFWD_CPT_Instance::$instances[\STA\Inc\CptCourse::$post_type], 'template_content'], LEARNDASH_FILTER_PRIORITY_THE_CONTENT);

    // public content
    if (!is_user_logged_in()) {
        the_post();
        the_content();
    } else {
        // private content
        the_post();
        // the_content();
        get_template_part('template-parts/content/content-single-course');
    }
} else {
    // If no content, include the "No posts found" template.
    get_template_part('template-parts/content/content-none');
}

get_footer();
