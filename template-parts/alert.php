<?php

/**
 * @var array $args
 */

$message = $args['message'] ?? '';
$type = $args['type'] ?? 'info'; // success, warning, info, danger
$dismissible = !!($args['dismissible'] ?? false);

$class = [
    'alert',
    sprintf('alert-%s', $type),
    'mb-0',
    $dismissible ? 'alert-dismissible fade show' : '',
];
?>

<div class="<?php echo implode(' ', $class); ?>" role="alert">
    <?php echo $message; ?>
    <?php if ($dismissible): ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <?php endif; ?>
</div>
