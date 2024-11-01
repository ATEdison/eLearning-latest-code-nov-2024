<?php
$svg_style = [
    'display: inline-block',
    'width: 16px',
    'position: relative',
    is_admin() ? 'top: 3px' : 'top: -2px',
];
?>

<span>Help</span>
<svg style="<?php echo implode(';', $svg_style); ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
    <circle cx="12" cy="12" r="10"></circle>
    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
    <line x1="12" y1="17" x2="12" y2="17"></line>
</svg>
