<?php

// default page template
get_header();

if (have_posts()) {
    the_post();
    get_template_part('template-parts/content/content');
} else {
    // If no content, include the "No posts found" template.
    get_template_part('template-parts/content/content-none');
}

get_footer();
