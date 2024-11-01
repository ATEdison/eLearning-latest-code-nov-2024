<div class="header-top">
    <div class="container-xl d-flex justify-content-between">
        <div class="header-top-left">
            <button type="button" class="btn-toggle-header-top-menu d-lg-none"></button>
            <?php wp_nav_menu([
                'theme_location' => 'top',
                'container_class' => 'header-top-menu',
            ]); ?>
        </div>
        <?php get_template_part('template-parts/header/language-selector'); ?>
    </div>
</div>
