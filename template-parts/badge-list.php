<?php

/**
 * @var array $args
 */
$badge_list = $args['badge_list'] ?? [];
?>
<ul class="sta-badge-list row mx-n8">
    <?php foreach ($badge_list as $slug) {
        get_template_part('template-parts/badge', '', ['slug' => $slug]);
    } ?>
</ul>
