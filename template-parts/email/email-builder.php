<?php

/**
 * @var array $args
 */

$placeholders = $args['placeholders'];
$builder = $args['builder'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <title>STA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0 " />
    <meta name="format-detection" content="telephone=no" />
</head>
<body style="margin:0px auto; padding:0px; background-color:#f2f2f2;" bgcolor="#f2f2f2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f2f2f2" style="table-layout:fixed;background-color: #f2f2f2;font-family: Helvetica, Arial, sans-serif;font-size: 16px;line-height: 1.75;">
        <tr>
            <td align="center" valign="top">
                <table align="center" width="600" border="0" cellspacing="0" cellpadding="0" class="em_main_table" bgcolor="#FFFFFF" style="background-color:#FFFFFF; width:600px; table-layout:fixed;">
                    <tr>
                        <td align="center" valign="top">
                            <!-- padding top -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f2f2f2" style="table-layout:fixed;background-color: #f2f2f2;">
                                <tr>
                                    <td width="100%" height="42"></td>
                                </tr>
                            </table>

                            <!-- header -->
                            <?php get_template_part('template-parts/email/email-header'); ?>

                            <!-- horizontal line -->
                            <?php if ($builder[0]['_type'] != 'image') {
                                \STA\Inc\EmailSettings::horizontal_line();
                                \STA\Inc\EmailSettings::vertical_padding(50);
                            } ?>

                            <!-- main content -->
                            <?php foreach ($builder as $item) {
                                get_template_part('template-parts/email/builder', $item['_type'], [
                                    'placeholders' => $placeholders,
                                    'data' => $item,
                                ]);
                            } ?>

                            <!-- footer -->
                            <?php get_template_part('template-parts/email/email-footer'); ?>

                            <!-- padding top -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f2f2f2" style="table-layout:fixed;background-color: #f2f2f2;">
                                <tr>
                                    <td width="100%" height="80"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
