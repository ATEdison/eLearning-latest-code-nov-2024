<?php
global $sta_is_template_hero_home_map_marker_desc_added;
if ($sta_is_template_hero_home_map_marker_desc_added) {
    return;
}
$sta_is_template_hero_home_map_marker_desc_added = true;
?>
<script type="text/html" id="tmpl-hero-home-map-marker-desc">
    <div>
        <div class="text-white fw-500 fs-lg-20 lh-1 mb-16">{{data.heading}}</div>
        <div class="hero-home-map-marker-desc-subheading">{{data.subheading}}</div>
    </div>
</script>
