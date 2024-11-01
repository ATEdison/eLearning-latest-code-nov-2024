<ul class="d-flex align-items-center">
    <?php if (class_exists('SitePress')): ?>
        <li><?php get_template_part('template-parts/header/language-selector'); ?></li>
    <?php endif; ?>

    <?php get_template_part('template-parts/header/user-menu-item'); ?>
</ul>
