<?php

global $post;

use STA\Inc\UserDashboard;

$is_absolute = false;
$is_login_page = false;

$site_logo_link = "https://partner.visitsaudi.com/en/home.html";

if (is_page_template('page-templates/homepage-agent.php')) {
    $is_absolute = is_user_logged_in();
} else {
    $post_content = $post->post_content;
    if (is_singular(\STA\Inc\CptCourseLesson::$post_type)) {
        $course_id = learndash_get_course_id($post->ID);
        $post_content = get_post_field('post_content', $course_id);
    }

    if (has_blocks($post_content)) {
        $blocks = parse_blocks($post_content);

        // is absolute
        $header_absolute_blocks = ['carbon-fields/sta-hero-slider', 'carbon-fields/sta-hero-banner', 'carbon-fields/sta-hero-home'];
        $is_absolute = in_array($blocks[0]['blockName'], $header_absolute_blocks);

        // is login
        $login_blocks = ['carbon-fields/sta-login', 'carbon-fields/sta-reset-password'];
        $is_login_page = in_array($blocks[0]['blockName'], $login_blocks);
    }
}

$is_adminbar_menu = false;

$header_class = 'site-header';
if ($is_absolute) {
    $header_class .= ' site-header-absolute';
    if (is_admin_bar_showing()) {
        $header_class .= ' admin_bar_menu';
    }
}

if ($is_login_page) {
    $header_class .= ' site-header-login';
}


?>



<header class="<?php echo $header_class; ?>" role="banner">
    <?php // get_template_part('template-parts/header/site-header-top'); ?>
    <div class="header-main">
        <div class="container-xl" >
            <div class="row justify-content-between gx-0">
                

                <?php if (!is_user_logged_in()): ?>

                    <div class="col-auto col-lg-2 d-flex align-items-center py-16 py-md-24 sta-left-header">
                    <button type="button" aria-label="Open sidebar menu" class="btn btn-sidebar p-0"><?php  get_template_part('template-parts/icons/sidebar'); ?></button>
                        <a href="<?php echo $site_logo_link; ?>" target="_blank" class="site-logo">
                         <!-- Desktop Logo -->
                         <span class="logo_dsk d-none d-md-block d-lg-block d-xl-block">
                        <?php
                            echo get_template_part('template-parts/icons/sta-partner-logo-v1'); 
                            // echo get_template_part('template-parts/icons/logo-white'); 
                            // echo get_template_part('template-parts/icons/logo-green'); 
                        ?>
                         </span>
                          <!-- Mobile Logo -->
                        <span class="logo_mobile d-block d-sm-block d-md-none">
                        <?php
                            echo get_template_part('template-parts/icons/sta-moblie-logo'); 
                        ?>
                        </span>
                        </a>
                    </div>
                    
                    <div class="col-auto col-lg-7 header-main-menu header-main-menu-guests d-flex align-items-center justify-content-center">
                    <?php 
                                $request_uri = $_SERVER['REQUEST_URI'] ?? null;
                                $uris = [];
                                $uris = explode("/",$request_uri); 
                                if(in_array('maintenance',  $uris)){
                                    wp_nav_menu([
                                        'theme_location' => 'maintenance_page',
                                        'container_class' => 'header-main-menu header-main-menu-members d-lg-flex align-items-lg-stretch justify-content-lg-center w-100',
                                        'menu_class' => 'd-lg-flex',
                                    ]);          
                               } 
                               ?>
                    </div>

                    <div class="col-auto col-lg-3 header-main-menu header-main-menu-guests d-flex align-items-center justify-content-end">
                        <ul>
                            <?php if (class_exists('SitePress')): ?>
                                <li class="menu-language-selector d-none d-sm-flex ">
                                    <?php get_template_part('template-parts/header/language-selector'); ?>
                                </li>
                            <?php endif; ?>
                            <!-- <li class="menu-register d-none d-sm-flex sta-reg-login-btn">
                                <a  href="<?php // echo \STA\Inc\CarbonFields\ThemeOptions::get_oauth_registration_url(); ?>" target="_blank"><?php // _e('Register', 'sta'); ?></a> 
                            </li> -->
                            <li class="menu-login sta-reg-login-btn">
                                <a href="<?php echo home_url('/login'); ?>"><?php _e('Log In / Sign Up', 'sta'); ?></a>
                            </li> 
                            <!-- <li class="menu-login d-block d-sm-block d-md-none">
                                <a href="<?php // echo home_url('/login'); ?>"><?php  // _e('Log In', 'sta'); ?></a>
                            </li> -->
                            <!-- <li class="menu-btn-reg-sign d-none  d-md-block d-lg-block d-xl-block">
                                <a style="color:#fff !important" href="<?php//  echo home_url('/login'); ?>"><?php _e('Log In', 'sta'); ?></a> /  <a style="color:#fff !important" href="<?php // echo \STA\Inc\CarbonFields\ThemeOptions::get_oauth_registration_url(); ?>" target="_blank"><?php _e('Sign Up', 'sta'); ?></a>
                            </li> -->
                            <!-- <li class="menu-login">
                                <a href="<?php // echo home_url('/new-login'); ?>"><?php _e('New Login', 'sta'); ?></a>
                            </li> -->
                        </ul>
                    </div>

                <?php else: ?>

                    <div class="col-auto col-lg-2 d-flex align-items-center py-16 py-md-24 sta-left-header">
                        <button type="button" aria-label="Open sidebar menu" class="btn btn-sidebar p-0"><?php  get_template_part('template-parts/icons/sidebar'); ?></button>
                        <a href="<?php echo $site_logo_link; ?>" class="site-logo">
                        
                        <!-- Desktop Logo -->
                        <span class="logo_dsk d-none d-md-block d-lg-block d-xl-block">
                        <?php
                            $menu_cls_ext = "";
                            if ($is_absolute) {
                            $menu_cls_ext = "menu_color_white";
                                echo get_template_part('template-parts/icons/sta-partner-logo'); 
                            }else{
                                echo get_template_part('template-parts/icons/sta-partner-logo-blue'); 

                            }
                        ?>
                        </span>
                        
                        <!-- Mobile Logo -->
                        <span class="logo_mobile d-block d-sm-block d-md-none">
                        <?php
                            $menu_cls_ext = "";
                            if ($is_absolute) {
                            $menu_cls_ext = "menu_color_white";
                                echo get_template_part('template-parts/icons/sta-moblie-logo'); 
                            }else{
                                echo get_template_part('template-parts/icons/sta-moblie-logo-blue'); 

                            }
                        ?>
                        </span>
                        </a>
                    </div>

                    <div class="col-auto col-lg-6 d-flex align-items-center align-items-lg-stretch">
                        <div class="d-lg-flex align-items-lg-stretch w-100">
                            <div class="mobile-right-menu d-flex align-items-center d-lg-none">
                                <?php if (class_exists('SitePress')): ?>
                                    <div class="me-16 d-none d-sm-block">
                                        <?php get_template_part('template-parts/header/language-selector'); ?>
                                    </div>
                                <?php endif; ?>
                                <div><button type="button" class="btn-toggle-burger-menu"><span class="icon"><span></span></span></button></div>
                            </div>
                           
 			                <?php 
                                $request_uri = $_SERVER['REQUEST_URI'] ?? null;
                                $uris = [];
                                $uris = explode("/",$request_uri); 
                                if(in_array('maintenance',  $uris)){
                                    wp_nav_menu([
                                        'theme_location' => 'maintenance_page',
                                        'container_class' => 'header-main-menu header-main-menu-members d-lg-flex align-items-lg-stretch justify-content-lg-center w-100',
                                        'menu_class' => 'd-lg-flex ' .$menu_cls_ext,
                                    ]);          
                               }else{
                                    wp_nav_menu([
                                        'theme_location' => 'primary_member',
                                        'container_class' => 'header-main-menu header-main-menu-members d-lg-flex align-items-lg-stretch justify-content-lg-center w-100',
                                        'menu_class' => 'd-lg-flex ' .$menu_cls_ext,
                                    ]); 
                                }                          
			                ?>
                        </div>
                    </div>
                    <div class="site-header-right-menu col-lg-3 d-none d-lg-flex align-items-center justify-content-end">
                        <?php get_template_part('template-parts/header/site-header-right-menu'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<div class="sidebar-menu-wrapper">
    <div class="sidebar-menu d-flex flex-column">
        <div class="sidebar-menu-header py-20">
            <div class="container">
                <div class="position-relative">
                    <!-- <a href="<?php echo home_url(); ?>"><?php echo get_template_part('template-parts/icons/logo-green'); ?></a> -->
                    <button type="button" class="btn btn-close-sidebar" aria-label="Close sidebar menu"></button>
                </div>
            </div>
        </div>
        <div class="sidebar-menu-content flex-grow-1">
            <div class="container">
                <ul class="mb-10">
                    <?php if (!is_user_logged_in()): ?>
                        <li><a id="sta-login" href="<?php echo home_url('/login'); ?>"><?php _e('Log In / Sign Up', 'sta'); ?></a></li>
                        <!-- <li><a id="sta-register" href="<?php // echo \STA\Inc\CarbonFields\ThemeOptions::get_oauth_registration_url(); ?>" target="_blank"><?php // _e('Register', 'sta'); ?></a></li> -->
                    <?php endif; ?>
                </ul>
                <?php echo wp_nav_menu([
                    'theme_location' => 'sidebar_menu',
                    'container' => '',
                ]); ?>

 		<div class="d-md-none d-flex justify-content-between chg-lang-mobile">
                    <div class="">
                        <span><?php _e('Select Language', 'sta'); ?></span>
                    </div>
                    <div class="language-selector">
                        <?php if (class_exists('SitePress')): ?>
                            <div class="me-16 d-sm-block">
                                <?php get_template_part('template-parts/header/language-selector'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>


<style>
    .chg-lang-mobile{
        border-top: 1px solid #00000014;
        margin-top: 2em;
        padding-top: 10px;
    }
    
    .chg-lang-mobile .language-selector .sta-language-selector .sta-language-selector-list ul li{
        padding: 10px 0;
    }
    
</style>




<script type="text/javascript">

    let isAuthenticated = '<?php echo is_user_logged_in(); ?>';
	let user_id = 'na';

	if (isAuthenticated) {
		var hash = "<?php echo hash('sha256',wp_get_current_user()->user_email) ?>";
		user_id = hash;
	}

/*-------------------------------------------*\
       Sidebar links GTM
\*-------------------------------------------*/

	let sidebarLinks = document.querySelectorAll('.sidebar-menu li.menu-item:not(.nn-sidebar-menu-heading)') 
          
	if (sidebarLinks) {
		sidebarLinks.forEach((el) =>
			el.addEventListener("click", function () {
				let anchor = el.querySelector('a');
				let link = anchor.href;
				let text = anchor.innerHTML;
					
				window.dataLayer.push({
					event:'header_navigation',
					eventCategory:'Navigation',
					eventAction:'Header',
					eventLabel: text,
					link,
					user_id
				});
			})
		);
	}

/*-------------------------------------------*\
       Language links GTM
\*-------------------------------------------*/

	let languageLinks = document.querySelectorAll('.sta-language-selector-list li');
          
	if (languageLinks) {
		languageLinks.forEach((el) =>
			el.addEventListener("click", function () { 
				let selectedLanguage = el.childNodes[0].getAttribute('onclick');
				
				window.dataLayer.push({
					event:'language_change',
					eventCategory:'Language',
					eventAction:'Header',
					eventLabel:selectedLanguage,
					user_id
				});
			})
		);
	}

/*-------------------------------------------*\
       Header links GTM
\*-------------------------------------------*/

	let headernavLinks = document.querySelectorAll('.header-main-menu-members li.menu-item, .header-main-menu-members li.menu-item-dashboard, .user-menu-dropdown-menu li.menu-item, .user-menu-dropdown-menu li.sta-logout'); 
          
	if (headernavLinks) {
		headernavLinks.forEach((el) =>
			el.addEventListener("click", function () { 
				let anchor = el.querySelector('a');
				let link = anchor.href;
				let text = anchor.innerHTML?.trim();
					
				window.dataLayer.push({
					event:'header_navigation',
					eventCategory:'Navigation',
					eventAction:'Header',
					eventLabel: text,
					link,
					user_id
				});
			})
		);
	}

</script>