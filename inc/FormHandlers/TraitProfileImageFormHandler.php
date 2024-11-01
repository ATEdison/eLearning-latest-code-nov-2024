<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\PointLogs;

trait TraitProfileImageFormHandler {

    protected function upload_profile_image($user_id) {
        $profile_image = $this->upload_image('profile_image');
        if ($profile_image) {
            carbon_set_user_meta($user_id, 'profile_image', $profile_image);

            // add points
            PointLogs::upload_a_profile_photo($user_id);
        }
    }

}
