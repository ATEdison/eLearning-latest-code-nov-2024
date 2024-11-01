<div class="sta-spacing"></div>
<div class="download-visit-sta-app">
    <div class="container-xl">
        
        <div class="app-list">
            <h6>DOWNLOAD VISIT SAUDI APP</h6>
            <ul class="">
                <li>
                    <a href="https://play.google.com/store/apps/details?id=sa.gov.apps.sauditourism&pli=1" target="_blank">
                        <?php echo get_template_part('template-parts/icons/sta-google-play'); ?>
                    </a>
                </li>
                <li>
                <a href="https://apps.apple.com/us/app/visit-saudi-%D8%B1%D9%88%D8%AD-%D8%A7%D9%84%D8%B3%D8%B9%D9%88%D8%AF%D9%8A%D8%A9/id818179871" target="_blank">
                    <?php echo get_template_part('template-parts/icons/sta-appstore'); ?>
                </a>
                </li>
                <li>
                <a href="https://appgallery.huawei.com/app/C101699859?sharePrepath=ag&locale=en_US&source=appshare&subsource=C101699859&shareTo=com.Slack&shareFrom=appmarket&shareIds=71b67a2fd8684bdf933520b72c27743f_com.Slack&callType=SHARE" target="_blank">
                    <?php echo get_template_part('template-parts/icons/sta-store-badges'); ?>
                </a>
                </li>
            </ul>
        </div>
    
    </div>
</div>
<footer class="site-footer" role="contentinfo">
    <div class="footer-top">
        <div class="container-xl">
            <div class="row gx-64 footer-top-row pb-lg-0">
                <div class="footer-top-col col-12 col-md-6 col-lg-3 mb-40 padd-lr-20" style="padding: 0;">
                    <div class="footer-top-col-logo">
                     
                      <div class="site-footer-saudi-welcome-logo">                        
                              <?php 

                              $request_uri = $_SERVER['REQUEST_URI'] ?? null;       
                              $uris = [];
                              $uris = explode("/",$request_uri);  
          
                              if(in_array('ar', $uris)) {
                                  ?>
                                      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/welcome-arabia-logo-ar.png" alt="">
                                 <?php          
                              }
                              elseif (in_array('zh-hans', $uris)) {                               
                                  ?>
                                 
                                   <img src="<?php echo get_template_directory_uri(); ?>/assets/images/welcome-arabia-logo-zh.png" alt="" >
                                
                                 <?php            
                              }
                              else {
                                   ?>
                                      <?php 
                                      get_template_part('template-parts/icons/sta-logo-new'); 
                                    //   get_template_part('template-parts/icons/footer-saudi-logo'); 
                                      ?>
                                   <?php 
                               }

                              ?>
                        </div><br>
                        <div class="mb-30 site-footer-logo text-center">
                            <span style="color:#4B4B4B; ">Powered by</span><br>
                          <a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/sta-logo-footer-new.svg" alt="" ></a>
                      </div>
                      </div>
                      <!-- <div class="footer-top-col-bottom backToTopLabel d-flex d-md-none d-lg-none d-sm-none ">
                          <span class="footer-backtotop"><?php // _e('Back to Top', 'sta'); ?></span>
                           <span class="back-to-top-icon">
                               <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="16" fill="white" fill-opacity="0.15"></circle><path fill-rule="evenodd" clip-rule="evenodd" d="M15.6464 13.6464C15.824 13.4689 16.1017 13.4528 16.2975 13.598L16.3536 13.6464L20.3536 17.6464C20.5488 17.8417 20.5488 18.1583 20.3536 18.3536C20.176 18.5311 19.8983 18.5472 19.7025 18.402L19.6464 18.3536L16 14.7073L12.3536 18.3536C12.176 18.5311 11.8983 18.5472 11.7025 18.402L11.6464 18.3536C11.4689 18.176 11.4528 17.8983 11.598 17.7025L11.6464 17.6464L15.6464 13.6464Z" fill="white"></path></svg>
                           </span>
                      </div> -->
                </div>

                <div class="footer-top-col footer-widget-menu col-12 col-md-6 col-lg-3 mb-40 padd-lr-20">
                    <div class="footer-widget open">
                        <h3 class="footer-widget-title"><?php _e('Visit Saudi Partner', 'sta'); ?>
                            <span class="footer-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
<path d="M16.6923 12.99C16.6342 13.0482 16.5653 13.0943 16.4894 13.1257C16.4136 13.1572 16.3322 13.1733 16.2501 13.1733C16.168 13.1733 16.0866 13.1572 16.0108 13.1257C15.9349 13.0943 15.866 13.0482 15.8079 12.99L10.0001 7.18145L4.19229 12.99C4.07502 13.1073 3.91596 13.1732 3.7501 13.1732C3.58425 13.1732 3.42519 13.1073 3.30792 12.99C3.19064 12.8728 3.12476 12.7137 3.12476 12.5479C3.12476 12.382 3.19064 12.2229 3.30792 12.1057L9.55792 5.85567C9.61596 5.79756 9.68489 5.75146 9.76077 5.72001C9.83664 5.68855 9.91797 5.67236 10.0001 5.67236C10.0822 5.67236 10.1636 5.68855 10.2394 5.72001C10.3153 5.75146 10.3842 5.79756 10.4423 5.85567L16.6923 12.1057C16.7504 12.1637 16.7965 12.2326 16.828 12.3085C16.8594 12.3844 16.8756 12.4657 16.8756 12.5479C16.8756 12.63 16.8594 12.7113 16.828 12.7872C16.7965 12.8631 16.7504 12.932 16.6923 12.99Z" fill="black"/>
</svg>
                            </span>

                        </h3>
                        
                        <?php wp_nav_menu([
                            'theme_location' => 'business_and_partner',
                            'container' => '',
                            'menu_class' => 'footer-menu-2',
                        ]); ?>
                    </div>
                </div>
                
                <div class="footer-top-col footer-widget-menu col-12 col-md-6 col-lg-3 mb-40 padd-lr-20">
                    <div class="footer-widget">
                      <h3 class="footer-widget-title"><?php _e('Discover Saudi', 'sta'); ?>
                        <span class="footer-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
<path d="M16.6923 12.99C16.6342 13.0482 16.5653 13.0943 16.4894 13.1257C16.4136 13.1572 16.3322 13.1733 16.2501 13.1733C16.168 13.1733 16.0866 13.1572 16.0108 13.1257C15.9349 13.0943 15.866 13.0482 15.8079 12.99L10.0001 7.18145L4.19229 12.99C4.07502 13.1073 3.91596 13.1732 3.7501 13.1732C3.58425 13.1732 3.42519 13.1073 3.30792 12.99C3.19064 12.8728 3.12476 12.7137 3.12476 12.5479C3.12476 12.382 3.19064 12.2229 3.30792 12.1057L9.55792 5.85567C9.61596 5.79756 9.68489 5.75146 9.76077 5.72001C9.83664 5.68855 9.91797 5.67236 10.0001 5.67236C10.0822 5.67236 10.1636 5.68855 10.2394 5.72001C10.3153 5.75146 10.3842 5.79756 10.4423 5.85567L16.6923 12.1057C16.7504 12.1637 16.7965 12.2326 16.828 12.3085C16.8594 12.3844 16.8756 12.4657 16.8756 12.5479C16.8756 12.63 16.8594 12.7113 16.828 12.7872C16.7965 12.8631 16.7504 12.932 16.6923 12.99Z" fill="black"/>
</svg>
                         </span>
                      </h3>
                      
                      <?php wp_nav_menu([
                          'theme_location' => 'discover_saudi',
                          'container' => '',
                          'menu_class' => 'footer-menu',
                      ]); ?>
                    </div>
                </div>
                
              
                <div class="col-12 col-md-6 col-lg-3 padd-lr-20">
                    <div class="footer-top-col footer-widget-menu mb-md-40">
                        <div class="footer-widget">
                            <h3 class="footer-widget-title"><?php _e('Get Help', 'sta'); ?>
                                <span class="footer-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
<path d="M16.6923 12.99C16.6342 13.0482 16.5653 13.0943 16.4894 13.1257C16.4136 13.1572 16.3322 13.1733 16.2501 13.1733C16.168 13.1733 16.0866 13.1572 16.0108 13.1257C15.9349 13.0943 15.866 13.0482 15.8079 12.99L10.0001 7.18145L4.19229 12.99C4.07502 13.1073 3.91596 13.1732 3.7501 13.1732C3.58425 13.1732 3.42519 13.1073 3.30792 12.99C3.19064 12.8728 3.12476 12.7137 3.12476 12.5479C3.12476 12.382 3.19064 12.2229 3.30792 12.1057L9.55792 5.85567C9.61596 5.79756 9.68489 5.75146 9.76077 5.72001C9.83664 5.68855 9.91797 5.67236 10.0001 5.67236C10.0822 5.67236 10.1636 5.68855 10.2394 5.72001C10.3153 5.75146 10.3842 5.79756 10.4423 5.85567L16.6923 12.1057C16.7504 12.1637 16.7965 12.2326 16.828 12.3085C16.8594 12.3844 16.8756 12.4657 16.8756 12.5479C16.8756 12.63 16.8594 12.7113 16.828 12.7872C16.7965 12.8631 16.7504 12.932 16.6923 12.99Z" fill="black"/>
</svg>
                                </span>
                            </h3>
                        
                            
                            <?php wp_nav_menu([
                                'theme_location' => 'get_help',
                                'container' => '',
                                'menu_class' => 'footer-menu-2',
                            ]); ?>
                            
                        <!-- <div class="footer-widget footer-widget-call-center">
                            <h3 class="footer-widget-title"><?php // _e('Call Center', 'sta'); ?></h3>
                            <div class="fs-14 text-content"><?php // echo wpautop(\STA\Inc\CarbonFields\ThemeOptions::call_center_text()); ?></div>
                        </div> -->
                        
                        </div>
                    </div>

                    <div class="sta-social-icons">
                        <?php $social = \STA\Inc\CarbonFields\ThemeOptions::get_social(); ?>
                        <ul class="sta-social" id="sta-social">
                            <?php foreach ($social as $type => $url): ?>
                                <li>
                                    <a 
                                        target="_blank"
                                        href="<?php echo $url; ?>"
                                        class="sta-social-item sta-social-link sta-social-item-<?php echo $type; ?>"
                                        aria-label="<?php printf(__('Connect with us on %s', 'sta'), ucfirst($type)); ?>"
                                    ></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom ">
        <div class="container-xl">
        
            <div class="row justify-content-lg-between stasocialfoo" id="stasocialfoo">

                <div class="col-12 col-lg-auto order-xl-2 text-lg-end d-md-flex justify-content-center">
                    <div class="">
                      <?php wp_nav_menu([
                          'theme_location' => 'footer_bottom',
                          'container' => '',
                          'menu_class' => 'footer-bottom-menu',
                      ]); ?>
                    </div>
                </div>
                <div class="col-12 col-lg-auto order-xl-1 footer-copy"><?php printf(__('Copyrights @%s All rights reserved. Saudi Tourism Authority', 'sta'), date('Y')); ?>
				      	</div>
            </div>
        </div>
    </div>

    <div class="backToTopLabel">
        <!-- <span class="footer-backtotop" style="visibility: hidden;"><?php _e('Back to Top', 'sta'); ?></span> -->
        <span class="back-to-top-icon">
            <!-- <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="16" fill="white" fill-opacity="0.15"></circle><path fill-rule="evenodd" clip-rule="evenodd" d="M15.6464 13.6464C15.824 13.4689 16.1017 13.4528 16.2975 13.598L16.3536 13.6464L20.3536 17.6464C20.5488 17.8417 20.5488 18.1583 20.3536 18.3536C20.176 18.5311 19.8983 18.5472 19.7025 18.402L19.6464 18.3536L16 14.7073L12.3536 18.3536C12.176 18.5311 11.8983 18.5472 11.7025 18.402L11.6464 18.3536C11.4689 18.176 11.4528 17.8983 11.598 17.7025L11.6464 17.6464L15.6464 13.6464Z" fill="white"></path></svg> -->
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="33" viewBox="0 0 32 33" fill="none">
            <circle cx="16" cy="16.6299" r="15.5" fill="black" stroke="white"/>
            <path d="M16.3555 22.6748L16.3555 11.297" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11.8037 15.1104L16.3558 10.5865L20.9079 15.1104" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

        </span>
    </div>

</footer>

</div><!-- #page -->

<script type="text/javascript">

    /*-------------------------------------------*\
       Sociallinks GTM
   \*-------------------------------------------

   let socailloggedin = '<?php echo is_user_logged_in(); ?>';
    
    if(socailloggedin){
            let user_id = '<?php echo get_current_user_id(); ?>';
            let user_email = '<?php echo wp_get_current_user()->user_email; ?>';
            let hash = "<?php echo hash('sha256',wp_get_current_user()->user_email) ?>";

    let socialLinks = document.getElementById("sta-social").querySelectorAll('li');
    if (socialLinks) {
      socialLinks.forEach((el) =>
        el.addEventListener("click", function () { //alert(el)
          let loadJSON2; //console.log(el.childNodes[1].ariaLabel);

          let stasocialPlatform = el.childNodes[1].ariaLabel;
          let arr = stasocialPlatform.split(" ");
          let stasocialPlatformLable = arr[4];
          //console.log(stasocialPlatformfirstLine);        
          
          loadJSON2 = {
                event:'connect_with_us',
                eventCategory:'Share',
                eventAction:'Footer',
                eventLabel:stasocialPlatformLable,
                user_id:hash
          };
       
          window.dataLayer.push(loadJSON2);
        })
      );
    } 

    } else {

        let socialLinks = document.getElementById("sta-social").querySelectorAll('li');
        if (socialLinks) {
          socialLinks.forEach((el) =>
            el.addEventListener("click", function () { //alert(el)
              let loadJSON2; //console.log(el.childNodes[1].ariaLabel);

              let stasocialPlatform = el.childNodes[1].ariaLabel
              let arr = stasocialPlatform.split(" ");
              let stasocialPlatformLable = arr[4];
              //console.log(stasocialPlatformfirstLine);
              
              
              loadJSON2 = {
                    event:'connect_with_us',
                    eventCategory:'Share',
                    eventAction:'Footer',
                    eventLabel:stasocialPlatformLable,
                    user_id:'na'
              };
           
              window.dataLayer.push(loadJSON2);
            })
          );
        } 

    }

*/
</script>

 <script src="https://vjs.zencdn.net/7.17.0/video.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/videojsyoutube/2.6.1/Youtube.min.js"></script>


