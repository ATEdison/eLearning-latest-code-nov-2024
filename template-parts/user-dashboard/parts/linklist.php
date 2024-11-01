<?php
/**
 * @var array $args
 */
?>

<div class="sta-user-dashboard-linklist">
    <h4 class="mb-16 mb-lg-30"><?php echo $args['heading']; ?></h4>
    <ul>
        <?php foreach ($args['items'] as $item):
            $attrs = [];
            if (isset($item['attrs']) && is_array($item['attrs']) && !empty($item['attrs'])) {
                foreach ($item['attrs'] as $key => $value) {
                    $attrs[] = sprintf('data-%1$s="%2$s"', $key, htmlentities($value));
                }
            }

            $class = [
                sprintf('linklist-item-%s', $item['id']),
            ];

            if (isset($item['extra_class']) && $item['extra_class']) {
                $class[] = $item['extra_class'];
            }

            $attrs[] = sprintf('class="%s"', implode(' ', $class));

            $attrs_string = implode(' ', $attrs);
            $attrs_string = $attrs_string ? ' ' . $attrs_string : '';
            ?>
            <li <?php echo $attrs_string; ?>>
                <h5 class="mb-0"><a href="<?php echo $item['link']; ?>"><?php echo $item['heading']; ?></a></h5>
                <div><?php echo $item['desc']; ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
