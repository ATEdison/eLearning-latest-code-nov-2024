<?php

namespace STA\Inc;

use STA\Inc\CarbonFields\ThemeOptions;

class UserRedirect {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('template_redirect', [$this, 'template_redirect']);
    }

    public function template_redirect() {
        $user_id = is_user_logged_in() ? get_current_user_id() : null;
        $post_id = get_the_ID();

        // If user is logged out and they try to access homepage-agent, redirect them to homepage
        if (!$user_id && $post_id == ThemeOptions::get_user_homepage_id()) {
            wp_safe_redirect(home_url());
            exit();
        }

        // If user is logged in and they try to access the homepage, redirect them to homepage-agent
        if ($user_id && is_front_page() && !self::is_staff($user_id)) {
            wp_safe_redirect(get_permalink(ThemeOptions::get_user_homepage_id()));
            exit();
        }

        if (is_post_type_archive(CptCourse::$post_type)) {
            wp_safe_redirect(home_url('/training'));
            exit();
        }


        
        $user = get_userdata($user_id);
        // Get all the user roles for this user as an array.
        $user_roles = $user->roles;
        //print_r($user_roles);
        $request_uri = $_SERVER['REQUEST_URI'] ?? null;

        $uris = [];

        $uris = explode("/",$request_uri);  
        if(in_array('stauser', $user_roles, true)) { 

             if(in_array('training',  $uris) || in_array('my-dashboard',  $uris) || in_array('training-achievement',  $uris) || in_array('homepage-agent',  $uris)){

                 wp_safe_redirect(home_url('/tourism-knowledge-program'));              
              }

        }
        else {

            if(in_array('tourism-knowledge-program',  $uris)){

                 wp_safe_redirect(home_url('/training'));              
            }
        } 
        

    }

    /**
     * @param int $user_id
     * @return bool
     */
    public static function is_staff($user_id) {
        if (user_can($user_id, 'administrator')) {
            return true;
        }
        if (user_can($user_id, 'editor')) {
            return true;
        }
        if (user_can($user_id, 'author')) {
            return true;
        }
        return false;
    }


    
}
