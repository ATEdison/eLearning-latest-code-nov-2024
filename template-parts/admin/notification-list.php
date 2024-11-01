<?php
[$data, $page_count] = \STA\Inc\NotificationSystem::admin_list_notifications($_GET);
$page = isset($_GET['paged']) && is_numeric($_GET['paged']) ? intval($_GET['paged']) : 0;
$page = min($page, $page_count);
$page = max(1, $page);

$base_url = remove_query_arg(['sta_notification_edit', 'nonce', 'sta_delete_notification']);
?>

    <div class="sta-notification-list">
        <table>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Actions</td>
                    <td>Message</td>
                    <td>Global</td>
                    <td style="width: 70px;">Updated At</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $item):
                    $edit_url = add_query_arg(['sta_notification_edit' => $item['id']], $base_url);
                    $delete_url = add_query_arg([
                        'sta_notification_delete' => $item['id'],
                        'nonce' => wp_create_nonce('sta_delete_notification'),
                    ], $base_url);
                    ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <a href="<?php echo $edit_url; ?>">Edit</a>
                            <a class="sta-notification-list-delete" href="<?php echo $delete_url; ?>">Delete</a>
                        </td>
                        <td>
                            <h3><?php echo $item['title']; ?></h3>
                            <?php echo wpautop(stripslashes($item['message'])); ?>
                        </td>
                        <td><?php echo $item['global'] ? 'yes' : 'no'; ?></td>
                        <td><?php echo $item['updated_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($page_count > 1): ?>
            <div class="sta-notification-list-pagination">
                <?php for ($page_index = 1; $page_index <= $page_count; $page_index++) {
                    if ($page_index == $page) {
                        printf('<span>%s</span>', $page_index);
                        continue;
                    }
                    printf('<a href="%1$s">%2$s</a>', add_query_arg(['paged' => $page_index], $base_url), $page_index);
                } ?>
            </div>
        <?php endif; ?>
    </div>

<?php

function sta_admin_notification_list_script() {
    ?>
    <script type="text/javascript">
        (function ($) {
            $(document).ready(function () {
                initTab();
            });

            $(document).on('click', '.sta-notification-list-delete', function (e) {
                e.preventDefault();
                var $btn = $(this);
                confirmNotificationDeletion($btn);
            });

            function initTab() {
                var editId = qs('sta_notification_edit');
                if (editId) {
                    $('.cf-container__tabs-list button:contains("New notification")').trigger('click');
                }
            }

            function confirmNotificationDeletion($btn) {
                var ok = confirm('Are you sure to delete this notification?');
                if (ok) {
                    window.location.href = $btn.attr('href');
                }
            }

            function qs(key) {
                key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
                var match = location.search.match(new RegExp("[?&]" + key + "=([^&]+)(&|$)"));
                return match && decodeURIComponent(match[1].replace(/\+/g, " "));
            }
        })(jQuery);
    </script>
    <?php
}

add_action('admin_print_footer_scripts', 'sta_admin_notification_list_script', PHP_INT_MAX);
