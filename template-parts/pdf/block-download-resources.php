<?php

/**
 * @var array $args
 */

$block = $args;

$data = $block['attrs']['data'];

// printf('<pre>%s</pre>', var_export($block, true));

?>
<h2 style="margin-bottom: 0;"><?php echo $data['heading']; ?></h2>
<?php echo wpautop($data['desc']); ?>
<ul>
    <?php foreach ($data['resources'] as $item): ?>
        <li><a href="<?php echo wp_get_attachment_url($item['file']); ?>"><?php echo $item['title']; ?></a></li>
    <?php endforeach; ?>
</ul>
