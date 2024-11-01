<?php

/**
 * @var array $args
 */
$post_title = $args['post_title'];
$post_link = $args['post_link'];
$post_content = $args['post_content'];

$debug = false;

if (!has_blocks($post_content)) {
    return;
}

$block_list = parse_blocks($post_content);
// printf('<pre>%s</pre>', var_export($block_list, true));
// die;
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="charset=utf-8" />
        <style>
            body {
                font-family: Arial, sans-serif;
            }
        </style>
    </head>
    <body>
        <?php if ($debug) {
            foreach ($block_list as $block) {
                switch ($block['blockName']) {
                    case 'carbon-fields/sta-youtube':
                        get_template_part('template-parts/pdf/block-youtube', [], $block);
                        break;
                    case 'carbon-fields/sta-download-resources':
                        get_template_part('template-parts/pdf/block-download-resources', [], $block);
                        break;
                    case 'core/image':
                        get_template_part('template-parts/pdf/block-core-image', [], $block);
                        break;
                    case '':
                    case 'core/paragraph':
                        echo render_block($block);
                        break;
                    default:
                        printf('<pre>%s</pre>', var_export($block, true));
                        break;
                }
            }
            die;
        }

        printf('<h1 style="text-align: center;"><a href="%1$s" style="text-decoration: none;color: #000;">%2$s</a></h1>', $post_link, $post_title);

        foreach ($block_list as $block) {
            // printf('<p>%s</p>', $block['blockName']);
            switch ($block['blockName']) {
                case 'carbon-fields/sta-youtube':
                    get_template_part('template-parts/pdf/block-youtube', [], $block);
                    break;
                case 'carbon-fields/sta-download-resources':
                    get_template_part('template-parts/pdf/block-download-resources', [], $block);
                    break;
                case 'core/image':
                    get_template_part('template-parts/pdf/block-core-image', [], $block);
                    break;
                // case 'core/image':
                // case 'core/paragraph':
                //     echo render_block($block);
                //     break;
                default:
                    // printf('<pre>%s</pre>', var_export($block, true));
                    echo render_block($block);
                    break;
            }
        }
        ?>
    </body>
</html>
