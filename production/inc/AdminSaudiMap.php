<?php

namespace STA\Inc;

class AdminSaudiMap {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_head', [$this, 'admin_head'], PHP_INT_MAX);
        add_action('admin_print_footer_scripts', [$this, 'map_script'], PHP_INT_MAX);
    }

    public function map_script() {
        global $current_screen;
        // printf('<pre style="position: absolute;background-color: #000;padding: 20px;color: #fff;">%s</pre>', var_export($current_screen, true));
        if ($current_screen->parent_file != 'edit.php?post_type=page') {
            return;
        }
        ?>
        <script type="text/html" id="tmpl-sta-admin-saudi-map-svg">
            <?php echo file_get_contents(get_theme_file_path('/assets/images/saudi-map.svg')); ?>
        </script>
        <script type="text/javascript">
            (function ($, _) {
                'use strict';

                var el = wp.element.createElement;
                var templateMapSVG = document.getElementById('tmpl-sta-admin-saudi-map-svg').innerHTML.trim();

                // block content
                wp.hooks.addFilter('carbon-fields.html.block', 'sta/saudi_map', function (callback) {
                    return function (field) {
                        if (field.name !== 'sta_admin_saudi_map') {
                            return callback(field);
                        }
                        return SaudiMap(field);
                    }
                });

                // on save
                // wp.hooks.addFilter('blocks.getSaveElement', 'sta/saudi_map', function (element, blockType, attrs) {
                //     console.log(blockType.name, blockType.name === 'carbon-fields/sta-hero-home');
                //     if (blockType.name !== 'carbon-fields\/sta-hero-home') {
                //         return element;
                //     }
                //     // console.log('blocks.getSaveElement', element, blockType, attrs);
                //     return element;
                // }, 99);

                $('.sta-admin-saudi-map-map-marker').draggable({});

                function SaudiMap(props) {
                    // console.log(props);
                    return el('div', null, [
                        SaudiMapMap(props),
                        el('div', { class: 'sta-admin-saudi-map-hint' }, 'Move the marker to correct position'),
                    ]);
                }

                function SaudiMapMap(props) {
                    // console.log(props);
                    // var Button = wp.components.Button;
                    // el(Button, { class: 'download-button' }, 'Download')
                    var isGrabbing = false;
                    var mapEl = null, mapX1, mapY1, mapWidth, mapHeight;
                    var markerEl = null, markerX, markerY;
                    var value = props.value || {};
                    markerX = value.left;
                    markerY = value.top;

                    function onMouseDown(e) {
                        startDragging(e);
                    }

                    function updateMarkerPosition(e) {
                        markerX = Math.max(Math.min(e.clientX - mapX1, mapWidth - 10), 0) / mapWidth * 100;
                        markerY = Math.max(Math.min(e.clientY - mapY1, mapHeight - 10), 0) / mapHeight * 100;

                        markerEl.style.top = markerPositionWithUnit(markerY);
                        markerEl.style.left = markerPositionWithUnit(markerX);

                        // update block data
                        // props.onChange(props.id, { top: newY, left: newX });
                    }

                    function onMouseMoveMarker(e) {
                        if (!isGrabbing) {
                            return;
                        }
                        // console.log('onMouseMoveMarker', e);
                        updateMarkerPosition(e);
                    }

                    function onMouseUp(e) {
                        stopDragging(e);
                    }

                    function onMouseMoveMap(e) {
                        if (!isGrabbing) {
                            return;
                        }
                        updateMarkerPosition(e);
                    }

                    function onMouseLeaveMap(e) {
                        stopDragging(e);
                    }

                    function stopDragging(e) {
                        isGrabbing = false;
                        mapEl.classList.remove('dragging');

                        // update props
                        props.onChange(props.id, { top: markerY, left: markerX });
                    }

                    function startDragging(e) {
                        isGrabbing = true;
                        markerEl = e.target;
                        mapEl = markerEl.parentNode;

                        var mapClientRect = mapEl.getBoundingClientRect();
                        mapX1 = mapClientRect.left;
                        mapY1 = mapClientRect.top;
                        mapWidth = mapEl.offsetWidth;
                        mapHeight = mapEl.offsetHeight;

                        // console.log({ mapX1, mapY1, mapX2, mapY2 });
                        mapEl.classList.add('dragging');
                    }

                    return el('div', {
                        class: 'sta-admin-saudi-map-map',
                        onMouseMove: onMouseMoveMap,
                        // onMouseLeave: onMouseLeaveMap,
                    }, [
                        rawHTML(templateMapSVG),
                        el('div', {
                            class: 'sta-admin-saudi-map-map-marker',
                            onMouseDown: onMouseDown,
                            onMouseMove: onMouseMoveMarker,
                            onMouseUp: onMouseUp,
                            // initial position
                            style: {
                                top: markerPositionWithUnit(markerY),
                                left: markerPositionWithUnit(markerX),
                            },
                        }, 'drag'),
                        el('input', {
                            type: 'hidden',
                            name: props.name,
                            value: '1',
                        }),
                    ]);
                }

                function markerPositionWithUnit(value) {
                    if (!value) {
                        return null;
                    }
                    return value + '%';
                }

                function rawHTML(html) {
                    return wp.element.RawHTML({ children: html });
                }
            })(jQuery, _);
        </script>
        <?php
    }

    public function admin_head() {
        wp_enqueue_style('sta-admin', get_template_directory_uri() . '/assets/css/admin.css');
    }
}
