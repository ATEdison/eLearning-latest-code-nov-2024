<?php
/**
 * @var array $args
 */
$user_id = $args['user_id'];
$date = $args['last_time_pass_a_core_course'];

$user = get_user_by('ID', $user_id);

$user_display_name = trim($user->display_name);
if (!$user_display_name) {
    $user_display_name = trim(sprintf('%s %s', $user->first_name, $user->last_name));
}
if (!$user_display_name) {
    $user_display_name = trim($user->user_login);
}
if (!$user_display_name) {
    $user_display_name = trim($user->user_email);
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="charset=utf-8" />
        <style>
            @page {
                margin: 0;
            }

            body {
                font-family: 'Displace 2.0', Arial, sans-serif;
                margin: 0;
                padding: 0;
            }

            .border-bot,
            .border-top {
                position: absolute;
                left: 0;
                width: 100%;
                height: 40px;
                background-image: url(<?php echo \STA\Inc\PDFConverter::image_file_to_base64_url(get_template_directory() . '/assets/images/certificate-horizontal.png'); ?>);
                background-size: contain;
                background-repeat: repeat-x;
                background-position: top left;
                background-color: #fff;
                z-index: 2;
            }

            .border-top {
                top: 0;
            }

            .border-bot {
                bottom: 0;
            }

            .border-right,
            .border-left {
                position: absolute;
                top: 0;
                width: 40px;
                height: 100%;
                background-image: url('<?php echo \STA\Inc\PDFConverter::image_file_to_base64_url(get_template_directory() . '/assets/images/certificate-vertical.png'); ?>');
                background-size: contain;
                background-repeat: repeat-y;
                background-position: top left;
                background-color: #fff;
                z-index: 1;
            }

            .border-left {
                left: 0;
            }

            .border-right {
                right: 0;
            }

            .content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10;
            }
        </style>
    </head>
    <body>
        <div class="border-top"></div>
        <div class="border-left"></div>
        <div class="border-right"></div>
        <div class="border-bot"></div>
        <div class="content">
            <table style="width: 100%;height:100%;border: 0;table-layout: fixed;">
                <tr>
                    <td style="height: 100%;vertical-align: middle;">
                        <table style="border:0;width:100%;table-layout: fixed;">
                            <tr>
                                <td style="padding: 80px 120px;text-align:center;">
                                    <div style="margin-bottom: 50px;"><img width="120" src="<?php echo \STA\Inc\PDFConverter::image_file_to_base64_url(get_template_directory() . '/assets/images/logo-green.png'); ?>" /></div>
                                    <div style="font-size: 40px;margin-bottom: 10px;">Certificate of Achievement</div>
                                    <div style="margin-bottom: 30px;">This acknowledges that</div>
                                    <div style="margin-bottom: 30px; font-size: 30px;"><strong><?php echo $user_display_name; ?></strong></div>
                                    <div style="margin-bottom: 80px;">has successfully completed the core modules and demonstrated <br />an understanding of Saudi Arabian Geography, weather & Locations, Accommodation and Culture & Heritage.</div>
                                    <div>Completed on: <strong><?php echo date('F d, Y', strtotime($date)); ?></strong></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>

