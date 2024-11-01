<?php

namespace STA\Inc;

class FormHelpers {

    public static function field_error($field, $errors) {
        $error = $errors[$field] ?? null;
        if (!$error) {
            return;
        }
        printf('<div class="is-invalid mt-5 fs-14">%s</div>', $error);
    }

}
