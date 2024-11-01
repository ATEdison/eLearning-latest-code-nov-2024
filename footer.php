</main><!-- #main -->
</div><!-- #primary -->
</div><!-- #content -->

<?php get_template_part('template-parts/footer/site-footer'); ?>

<?php wp_footer(); ?>

<script type="text/javascript">

    let isloggedin = '<?php echo is_user_logged_in(); ?>';
	let userid = 'na';

	if (isloggedin) {
		var hash = "<?php echo hash('sha256',wp_get_current_user()->user_email) ?>";
		userid = hash;
	}

	window.dataLayer.push({
		event:'user_id',
		eventCategory:'User ID',
		eventAction: isloggedin ? 'login' : 'not login',
		eventLabel:userid,
		user_id: userid
	});

/*-------------------------------------------*\
       Footerlinks GTM
\*-------------------------------------------*/

	let footerLinks = document.querySelectorAll('.footer-menu li, .footer-menu-2 li, .footer-bottom-menu li');
		  
	if (footerLinks) {
	footerLinks.forEach((el) =>
		el.addEventListener("click", function () { //alert(el)
		let stafooterlink = el.childNodes[0].innerHTML;
		window.dataLayer.push({
				event:'footer_navigation',
				eventCategory:'Navigation',
				eventAction:'Footer',
				eventLabel:stafooterlink,
				user_id:userid
		});
		})
	);
	}


/*-------------------------------------------*\
       Hero Banner Register 
\*-------------------------------------------*/

	let herobannerLinks = document.querySelectorAll('.sta-hero-home-content a');
	if (herobannerLinks) {
		herobannerLinks.forEach((el) =>
		el.addEventListener("click", function () { 
			let herobannerlabel = el.childNodes[0].textContent;
		
			window.dataLayer.push({
				event:'register_now',
				eventCategory:'Navigation',
				eventAction:'Hero Banner',
				eventLabel:herobannerlabel,
				user_id:userid
			});
		})
		);
	}
  
 /*-------------------------------------------*\
       Home Training cards 
\*-------------------------------------------*/

	let trainingCards = document.querySelectorAll(".home .sta-course-grid .course-preview");
	if (trainingCards) {
		trainingCards.forEach((el) =>
		el.addEventListener("click", function () { 
			let cardTitle = el.querySelector('.course-preview-title')?.innerText;
			if (cardTitle) {
				window.dataLayer.push({
					event:'cards',
					eventCategory:'Navigation',
					eventAction:'Our Training Modules',
					eventLabel: cardTitle,
					user_id: userid
				});
			}
		})
		);
	}

/*-------------------------------------------*\
       Hero Banner Register 
\*-------------------------------------------*/

	let getstartedLinks = document.querySelectorAll('.sta-text-image .get-started-register-now');
	if (getstartedLinks) {
		getstartedLinks.forEach((el) =>
		el.addEventListener("click", function () { 
		
			window.dataLayer.push({
				event:'register_now',
				eventCategory:'Navigation',
				eventAction:'Get started today',
				eventLabel:'Register Now',
				user_id: userid
			});
		})
		);
	}

/*-------------------------------------------*\
       Get Started Today 
\*-------------------------------------------*/

	let getstartedToday = document.querySelectorAll('.sta-text-image .sta-text-image-text');
	if (getstartedToday) {
		getstartedToday.forEach((el) =>
		el.addEventListener("click", function () { 
		
			window.dataLayer.push({
				event:'register_now',
				eventCategory:'Navigation',
				eventAction:'Get started today',
				eventLabel:'Register Now',
				user_id: userid			
			});
		})
		);
	}


/*-------------------------------------------*\
       Header Link Helpers
\*-------------------------------------------*/

	const pushHeaderNavigation = (eventLabel, link) => {
		window.dataLayer.push({
			event:'header_navigation',
			eventCategory:'Navigation',
			eventAction:'Header',
			eventLabel,
			link,
			user_id: userid
		});
	}


/*-------------------------------------------*\
       Login Links
\*-------------------------------------------*/

	let loginLinks = document.querySelectorAll(".header-main-menu .menu-login");

	if (loginLinks) {
		loginLinks.forEach((el) =>
			el.addEventListener("click", function () { 
				let link = el.querySelector('a').href;
				pushHeaderNavigation('log in', link)
			})
		);
	}

/*-------------------------------------------*\
       Register Links
\*-------------------------------------------*/

	let registerLinks = document.querySelectorAll(".header-main-menu .menu-register");

	if (registerLinks) {
		registerLinks.forEach((el) =>
			el.addEventListener("click", function () { 
				let link = el.querySelector('a').href;
				pushHeaderNavigation('register', link)
			})
		);
	}

 /*-------------------------------------------*\
       Logo Links
\*-------------------------------------------*/
		
	let logoLinks = document.querySelectorAll(".header-main .site-logo");
		  
	if (logoLinks) {
		logoLinks.forEach((el) =>
			el.addEventListener("click", function () {
				let link = el.href;
				pushHeaderNavigation('logo', link);
			})
		);
	}

 /*-------------------------------------------*\
      Training Page Register 
\*-------------------------------------------*/

	let registerNowButtons = document.querySelectorAll('.sta-course-intro a');
		  
	if (registerNowButtons) {
		registerNowButtons.forEach((el) =>
			el.addEventListener("click", function () { 
			
				window.dataLayer.push({
					event:'register_now',
					eventCategory:'Navigation',
					eventAction:'Course Page',
					eventLabel:'Register Now',
					user_id: userid
				});
			})
		);
	}

/*-------------------------------------------*\
       Training Page Training cards 
\*-------------------------------------------*/

	let otherCourseCards = document.querySelectorAll(".sta-course-grid-members .course-preview");
		  
	if (otherCourseCards) {
		otherCourseCards.forEach((el) =>
			el.addEventListener("click", function () {
				let courseTitle = el.querySelector('.course-preview-title')?.innerText;
				if (courseTitle) {
					window.dataLayer.push({
						event:'cards',
						eventCategory:'Navigation',
						eventAction:'Other Training Modules',
						eventLabel: courseTitle,
						user_id: userid
					});
				}
			})
		);
	}
    
 /*-------------------------------------------*\
       Training Page Training cards logout
\*-------------------------------------------*/

let otherCourseCardsLogout = document.querySelectorAll(".single-sfwd-courses .sta-course-grid .course-preview");
		  
	if (otherCourseCardsLogout) {
		otherCourseCardsLogout.forEach((el) =>
			el.addEventListener("click", function () {
				let courseTitle = el.querySelector('.course-preview-title')?.innerText;
				if (courseTitle) {
					window.dataLayer.push({
						event:'cards',
						eventCategory:'Navigation',
						eventAction:'Other Training Modules',
						eventLabel: courseTitle,
						user_id: userid
					});
				}
			})
		);
	}

    
/*-------------------------------------------*\
       Social Events
\*-------------------------------------------*/
	let socialLinks = document.querySelectorAll(".sta-social li");
		  
	if (socialLinks) {
		socialLinks.forEach((el) =>
			el.addEventListener("click", function () {
				let stasocialPlatform = el.childNodes[1].ariaLabel;
				let arr = stasocialPlatform.split(" ");
          		let stasocialPlatformLable = arr[4];
				if (stasocialPlatform) {
					window.dataLayer.push({
						 event:'connect_with_us',
	                    eventCategory:'Share',
	                    eventAction:'Footer',
	                    eventLabel:stasocialPlatformLable,
                        user_id: userid
					});
				}
			})
		);
	}
 
 

</script>


</body>
</html>