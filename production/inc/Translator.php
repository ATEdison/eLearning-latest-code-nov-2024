<?php

namespace STA\Inc;

class Translator {

    public static function points($value) {
        return sprintf(_n('%s point', '%s points', $value, 'sta'), $value);
    }

    public static function modules($value) {
        return sprintf(_n('%s module', '%s modules', $value, 'sta'), $value);
    }

    public static function achievements($value) {
        return sprintf(_n('%s achievement', '%s achievements', $value, 'sta'), $value);
    }

    public static function user_unverified() {
        return __('Your account has not been verified yet!', 'sta');
    }

    public static function new_notifications($value) {
        return sprintf(_n('%s new notification', '%s new notifications', $value, 'sta'), $value);
    }

}
