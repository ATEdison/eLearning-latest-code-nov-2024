<?php
/**
 * Template Name: User Dashboard
 */

get_header(); ?>
    <div class="sta-user-dashboard py-56 py-lg-80">
        <div class="sta-user-dashboard-content">
            <?php $sta_subpage = get_query_var('sta_subpage');
            get_template_part('template-parts/user-dashboard/dashboard', $sta_subpage); ?>
        </div>
    </div>
<?php get_footer();
