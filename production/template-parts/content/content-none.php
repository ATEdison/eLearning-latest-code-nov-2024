<section class="no-results not-found">
    <div class="container">
        <header class="page-header alignwide">
            <?php if (is_search()): ?>
                <h1 class="page-title">
                    <?php printf(
                        'Results for "%s"',
                        '<span class="page-description search-term">' . esc_html(get_search_query()) . '</span>'
                    ); ?>
                </h1>

            <?php else : ?>
                <h1 class="page-title">Nothing here</h1>
            <?php endif; ?>
        </header><!-- .page-header -->

        <div class="page-content default-max-width">
            <?php if (is_home() && current_user_can('publish_posts')) : ?>
                <?php printf(
                    '<p>' . wp_kses('Ready to publish your first post? <a href="%1$s">Get started here</a>.', array('a' => array('href' => array(),),)) . '</p>',
                    esc_url(admin_url('post-new.php'))
                ); ?>
            <?php elseif (is_search()) : ?>
                <p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>
                <?php get_search_form(); ?>
            <?php else : ?>
                <p>It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.</p>
                <?php get_search_form(); ?>
            <?php endif; ?>
        </div><!-- .page-content -->
    </div>
</section><!-- .no-results -->
