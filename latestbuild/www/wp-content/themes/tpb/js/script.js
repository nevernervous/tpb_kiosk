/* =============================================================================
   Site
   ========================================================================== */

var site = (function() {
	var activeObject = false;
	var recognitionDelay = 0;
	var virtualKeyboard = true;

	/**
	 * Init
	 */
	var init = function() {
		siteBackground.init();

		bindEvents();

		setTimeout(introCycle, 4000);

		// Fancybox
		$(".fancybox").fancybox({
			openEffect  : 'none',
			closeEffect : 'none',
			helpers : {
				media : {}
			}
		});

		// Update files asynchronously
		//updateFiles();
	}


	/**
	 * Bind events
	 */
	var bindEvents = function() {
		var userEvent = 'touchstart';
		if (window.location.href.indexOf('click') !== -1)
			userEvent = 'click';

		$('.screen-intro').on(userEvent, hideScreenIntro);
		$('body').on(userEvent, '.btn-cart-back, .btn-checkout-back, .btn-catalogue, .link-catalogue', showScreenCatalogue);
		$('body').on(userEvent+(userEvent=='touchstart'?' touchmove touchend':''), '.link-product', showScreenProduct);
		$('body').on(userEvent, '.btn-add-to-cart', showScreenAddToCart);
		$('body').on(userEvent, '.btn-cart-checkout, .toggle-cart', showScreenCheckout);
		$('body').on(userEvent, '.btn-checkout-next', checkoutNextStep);
		$('body').on(userEvent, '.btn-reset-session', resetSession);
		$('body').on(userEvent, '.btn-reset-session-confirm', showScreenResetSession);
		$('body').on(userEvent, '.btn-reset-cancel', hideScreenResetSession);
		$('body').on(userEvent, '.btn-tab', switchScreenTabs);
		$('body').on(userEvent, '.link-video', openVideo);
		$('body').on(userEvent, '.fancybox-overlay, .fancybox-close', closeVideo);
		$('body').on(userEvent, '.link-back', linkBack);

		$('body').on('submit', '.form-add-to-cart', addToCart);
		$('body').on(userEvent, '.btn-del', removeFromCart);
		$('body').on(userEvent, '.btn-qty', changeCartQty);
		$('body').on('change', '.input-qty, .select-amount', changeProductPrice);
		$('body').on(userEvent, '.user-select .option', userSelect);
		$('body').on('change', '.list-order', listOrder);
		$('body').on('change', '.list-filter', listFilter);
		$('body').on(userEvent, 'label', checkInput);
		$('body').on(userEvent, '.btn-submit', submitForm);
		// $('body').on(userEvent, '.input-text', inputTextFocus);

		if (userEvent == 'touchstart')
			$('body').on(userEvent, touchFeedback);

		// Causes bug on linux/chromium install
		// if (virtualKeyboard)
		// 	$('body').on(userEvent, '#osk-container li', keyboardLiTouch);

		$('.site-sidebar').on('stateactive stateinactive stateinfo statecheckout', switchSidebar);
		$('.site-sidebar .sidebar-areas').on('touchstart touchmove touchend', touchesHandler);
		$('.site-sidebar .sidebar-areas').on('no_object_recognized', areaHandler);
		$('.site-sidebar .sidebar-areas').on('object_recognized', areaHandler);

		$('body').on(userEvent, inactivityHandler);
		$('body').on(userEvent, '.btn-inactive-cancel', hideScreenInactive);
		$('body').on(userEvent, '.btn-inactive-reset', resetSession);

		$('body').on('touchstart touchmove touchend', '.scroller', touchScroll);
	}


	/**
	 * Check if user is inactive for too long
	 */
	var inactivityHandler = function() {
		clearTimeout(inactivityTimeout);

		inactivityTimeout = setTimeout(showScreenInactive, inactivityDelay);
	}
	var inactivityTimeout = null;
	var inactivityDelay = 60*1000;


	/**
	 * Cycle through intro tips
	 */
	 var introCycle = function() {
		var screen = $('.screen-intro');
		if (screen.length == 0 || stopIntroCycle)
			return;
		var tips = screen.find('.tip');
		var activeTip = tips.filter('.is-active');
		var nextTip = activeTip.next();
		if (nextTip.length == 0)
			nextTip = tips.first();

		// Animation
		var tl = new TimelineLite();
		tl.pause();

		tl.staggerFromTo(
			activeTip.find('.svg, .text'),
			0.8,
			{
				alpha: 1,
				x: 0
			},
			{
				alpha: 0,
				x: 100,
				ease: Power3.easeIn
			},
			0.1,
			0
		);

		tl.call(function() {
			activeTip.css({opacity: ''});
			nextTip.find('.svg, .text').css({opacity: 0});

			activeTip.removeClass('is-active');
			nextTip.addClass('is-active');
		});

		tl.staggerFromTo(
			nextTip.find('.svg, .text'),
			0.8,
			{
				alpha: 0,
				x: -100
			},
			{
				alpha: 1,
				x: 0,
				ease: Power3.easeOut
			},
			0.1,
			0.8
		);

		tl.call(introCycle, null, null, '+=4');

		tl.play();
	}
	var stopIntroCycle = false;


	/**
	 * Hide screen intro
	 */
	var hideScreenIntro = function(e) {
		var container = $('.site-container');
		var screen = $('.screen-intro');
		var elements = screen.find('.phrase, .phrase-small');
		var tips = screen.find('.tip');
		var background = $('.site-background');
		var ui = $('.site-ui, .site-main');

		// Get coordinates
		if (e.pageX != undefined && e.pageY != undefined) {
			var x = e.pageX;
			var y = e.pageY;
		} else {
			var x = e.touches[0].screenX;
			var y = e.touches[0].screenY;
		}

		// Before animation
		ui.removeClass('is-hidden');
		screen.css({pointerEvents: 'none'});
		stopIntroCycle = true;

		// Animation
		var tl = new TimelineLite();
		tl.pause();

		tl.fromTo(
			elements,
			0.6,
			{
				alpha: 1,
				scale: 1
			},
			{
				alpha: 0,
				scale: 0.9,
				ease: Power3.easeIn
			},
			0
		);

		tl.staggerFromTo(
			tips,
			0.6,
			{
				alpha: 1,
				y: 0
			},
			{
				alpha: 0,
				y: -20,
				ease: Power3.easeIn
			},
			0.1,
			0
		);

		tl.call(function() {
			siteBackground.speedUp();
		}, null, null, 0.3)

		tl.fromTo(
			$('.area-info'),
			1.2,
			{
				alpha: 0,
				scale: 0.9
			},
			{
				alpha: 1,
				scale: 1,
				ease: Power3.easeOut
			},
			1
		);

		tl.fromTo(
			$('.site-ui .logo'),
			1.2,
			{
				alpha: 0
			},
			{
				alpha: 1
			},
			1.2
		);

		tl.fromTo(
			$('.screen-select .phrase'),
			1.2,
			{
				alpha: 0,
				x: 25
			},
			{
				alpha: 1,
				x: 0,
				ease: Power3.easeOut
			},
			1.6
		);

		tl.fromTo(
			$('.site-topbar'),
			1.2,
			{
				alpha: 0
			},
			{
				alpha: 1
			},
			1.9
		);

		tl.call(function() {
			ui.css({opacity: ''});
			background.css({transform: '', opacity: ''});
			$('.screen-select .phrase, .site-ui .logo, .site-topbar').css({opacity: ''});
			screen.remove();
		});

		tl.play();
	}


	/**
	 * Load screen
	 */
	var loadScreen = function(url, tabIndex) {
		if (loadScreenWait)
			return;
		loadScreenWait = true;

		// Load screen
		$.get(url, function(data) {
			switchScreen(data, tabIndex);

			currentScreenUrl = url;
		});
	}
	var currentScreenUrl = null;
	var loadScreenWait = false;


	/**
	 * Show screen catalogue
	 */
	var showScreenCatalogue = function() {
		// Selectors
		var link = $('.btn-catalogue');

		if ($('.site-main .screen').hasClass('screen-catalogue')) {
			$('.site-main .screen .nav-tabs .btn-tab').first().trigger('touchstart');
		} else {
			// Load screen
			var url = link.attr('data-url');
			loadScreen(url, false);
		}
	}


	/**
	 * Show screen checkout
	 */
	var showScreenCheckout = function() {
		// Selectors
		var button = $('.toggle-cart');
		var url = button.attr('data-url');

		// Load screen
		loadScreen(url, false);
	}


	/**
	 * Show screen product
	 */
	var showScreenProduct = function(e) {
		// Selectors
		var link = $(this);
		var url = link.attr('data-url');

		if (e.type != 'click') {
			if (e.type == 'touchstart') {
				touchMoved = false;
			} else if (e.type == 'touchmove') {
				touchMoved = true;
			} else if (e.type == 'touchend') {
				if (!touchMoved) {
					// Load screen
					loadScreen(url, false);
				}

				touchMoved = false;
			}
		} else {
			// Load screen
			loadScreen(url, false);
		}
	}
	var touchMoved = false;


	/**
	 * Show screen object
	 */
	var showScreenObject = function() {
		// Selectors
		var url = OBJECTS[activeObject].url;

		// Load screen
		loadScreen(url, false);
	}


	/**
	 * Show screen add to cart
	 */
	var showScreenAddToCart = function() {
		var activeScreen = $('.site-main .screen').first();

		if (!$(this).is('.btn-add-to-cart') && activeObject && activeScreen.is('.screen-product') && activeScreen.attr('data-pattern') != activeObject) {
			var url = WRK.host+'/confirm-add-to-cart';
			url += '?table_product_id='+OBJECTS[activeObject].ID+'&catalogue_product_id='+activeScreen.attr('data-product-id');
		} else {
			var url = WRK.host+'/add-to-cart';
			if ($(this).is('.btn-add-to-cart')) {
				var id = $(this).attr('data-id');
				url += '?product_id='+id;
			} else {
				url += '?product_id='+OBJECTS[activeObject].ID;
			}
			if ($(this).is('.price')) {
				var price = $(this).attr('data-value');
				url += '&price='+price;
			}
		}

		// Load screen
		loadScreen(url, false);
	}


	/**
	 * Show screen reset session
	 */
	var showScreenResetSession = function() {
		// Selectors
		var screen = $('.screen-reset');
		toggleScreenUser(screen);
	}


	/**
	 * Show screen inactive
	 */
	var showScreenInactive = function() {
		// Selectors
		var screen = $('.screen-inactive');
		if (!$('.screen-reset').is(':visible')) {
			toggleScreenUser(screen);

			resetSessionTimeout = setTimeout(resetSession, inactivityDelay);
		}
	}
	var resetSessionTimeout = null;


	/**
	 * Hide screen inactive
	 */
	var hideScreenInactive = function() {
		// Selectors
		var screen = $('.screen-inactive');
		toggleScreenUser(screen);

		clearTimeout(resetSessionTimeout);
	}


	/**
	 * Hide screen reset session
	 */
	var hideScreenResetSession = function() {
		// Selectors
		var screen = $('.screen-reset');
		toggleScreenUser(screen);
	}


	/**
	 * Toggle user screen
	 */
	var toggleScreenUser = function(screen) {
		if (screenUserWait)
			return;

		screenUserWait = true;

		var ui = $('.site-ui, .site-main, #osk-container');

		if (screen.hasClass('is-visible')) {
			// Before animation
			ui.css({display: 'block', opacity: 0});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.fromTo(
				screen,
				0.5,
				{
					alpha: 1,
					scale: 1
				},
				{
					alpha: 0,
					scale: 0.95,
					ese: Power3.easeIn
				},
				0
			);

			tl.fromTo(
				ui,
				0.5,
				{
					alpha: 0,
					scale: 1.05
				},
				{
					alpha: 1,
					scale: 1,
					ese: Power3.easeOut
				},
				0.15
			);

			tl.call(function() {
				ui.css({display: '', opacity: '', transform: ''});
				screen.css({opacity: '', transform: ''});

				screen.removeClass('is-visible');
				ui.removeClass('is-hidden');

				screenUserWait = false;
			});

			tl.play();
		} else {
			// Before animation
			screen.css({display: 'block', opacity: 0});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.fromTo(
				ui,
				0.5,
				{
					alpha: 1,
					scale: 1
				},
				{
					alpha: 0,
					scale: 1.05,
					ese: Power3.easeIn
				},
				0
			);

			tl.fromTo(
				screen,
				0.5,
				{
					alpha: 0,
					scale: 0.95
				},
				{
					alpha: 1,
					scale: 1,
					ese: Power3.easeOut
				},
				0.15
			);

			tl.call(function() {
				screen.css({display: '', opacity: '', transform: ''});
				ui.css({opacity: '', transform: ''});

				screen.addClass('is-visible');
				ui.addClass('is-hidden');

				screenUserWait = false;
			});

			tl.play();
		}
	}
	var screenUserWait = false;


	/**
	 * Switch screen
	 */
	var switchScreen = function(data, tabIndex) {
		// Selectors
		var html = $('<div/>').html(data);
		var newScreen = html.find('.screen');
		var activeScreen = $('.site-main .screen').first();

		if (newScreen.attr('data-screen') == activeScreen.attr('data-screen')) {
			loadScreenWait = false;
			return;
		}

		// Save previous page
		if (activeScreen.find('.screen-title').length == 1 && !historyBack) {
			pageHistory.push([currentScreenUrl, activeScreen.find('.screen-title').text(), activeScreen.find('.nav-tabs .tab.is-active').index()]);
		}

		if (pageHistory.length > 0) {
			var previousPage = pageHistory[pageHistory.length-1];
			$('.link-back').addClass('is-visible').find('.output').html(previousPage[1]);
		} else {
			$('.link-back').removeClass('is-visible');
		}

		historyBack = false;

		// Product screen
		if (newScreen.hasClass('screen-product')) {
			if (newScreen.attr('data-pattern') === activeObject) {
				$('.sidebar-areas .area-checkout .product-prices').remove();
				newScreen.find('.product-prices').clone().appendTo($('.sidebar-areas .area-checkout .area-text'));
				// newScreen.find('.product-add-to-cart').remove();
			}
		} else {
			switchTabWait = false;
		}

		// Tabs screen
		if (newScreen.find('.nav-tabs').length == 1) {
			newScreen.find('.screen-tabs .tab').removeClass('is-active').eq(tabIndex).addClass('is-active');
			newScreen.find('.nav-tabs .tab').removeClass('is-active').eq(tabIndex).addClass('is-active');
		}

		// Confirm screen
		// if (newScreen.hasClass('screen-confirm-add-to-cart')) {
		// 	if ($('.site-sidebar').hasClass('is-info')) {
		// 		newScreen.addClass('is-expanded');
		// 	}
		// }

		// Switch screen
		newScreen.css({opacity: 0});
		$('.site-main').prepend(newScreen);

		outroScreen(activeScreen);
		setTimeout(introScreen, 500, newScreen);

		// Virtual keyboard
		if (virtualKeyboard) {
			if (newScreen.hasClass('screen-checkout')) {
				$('.site-main .input-text').onScreenKeyboard({
			        'rewireReturn': 'Continue'
				});
			}

			// Hide keyboard
			$('#osk-container:visible .osk-hide').click();
		}

		// Slider
		slider.init();
	}
	var pageHistory = [];


	/**
	 * Intro screen
	 */
	var introScreen = function(screen) {
		var tl = new TimelineLite();
		tl.pause();

		if (screen.hasClass('screen-catalogue')) {

			// Selectors
			var tab = screen.find('.screen-tabs .tab.is-active');
			var title = tab.find('.tab-title');
			var body = tab.find('.catalogue-home, .filters, .scroller');
			var tabs = screen.find('.nav-tabs .tab');

			// Before animation
			screen.css({opacity: ''});
			title.css({opacity: 0});
			body.css({opacity: 0});
			tabs.css({opacity: 0});

			// Animation
			tl.fromTo(
				title,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0
			);

			tl.staggerFromTo(
				tabs,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0.1,
				0
			);

			tl.fromTo(
				body,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0.15
			);

			tl.call(function() {
				title.css({opacity: '', transform: ''});
				body.css({opacity: '', transform: ''});
				tabs.css({opacity: '', transform: ''});
			});

		} else if (screen.hasClass('screen-product')) {

			// Selectors
			var elements = screen.find('.product-header, .product-image, .product-add-to-cart');
			var body = screen.find('.product-body');
			var tabs = screen.find('.nav-tabs .tab');
			var activeTab = tabs.filter('.is-active');

			if (activeTab.find('.btn-tab-similar').length == 1) {
				var tab = screen.find('.product-similars');
			} else {
				var tab = screen.find('.screen-tabs .tab').eq(activeTab.index());
			}

			if (tab.hasClass('product-similars')) {
				// Before animation
				body.addClass('is-hidden');

				introTab(tab);
			} else {
				// Before animation
				screen.css({opacity: ''});
				elements.css({opacity: 0});
				tabs.css({opacity: 0});
				tab.css({opacity: 0});

				// Animation
				tl.staggerFromTo(
					elements,
					0.3,
					{
						alpha: 0,
						y: 20
					},
					{
						alpha: 1,
						y: 0,
						ease: Power3.easeOut
					},
					0.1,
					0
				);

				tl.staggerFromTo(
					tabs,
					0.3,
					{
						alpha: 0,
						y: 20
					},
					{
						alpha: 1,
						y: 0,
						ease: Power3.easeOut
					},
					0.1,
					0
				);

				tl.call(function() {
					productIntroTab(tab);
				}, null, null, 0.15);

				tl.call(function() {
					elements.css({opacity: '', transform: ''});
					tabs.css({opacity: '', transform: ''});
				});
			}

		} else if (screen.hasClass('screen-add-to-cart')) {

			// Selectors
			var elements = screen.find('.title, .product-image, .product .block, .actions');

			// Before animation
			screen.css({opacity: ''});
			elements.css({opacity: 0});

			// Animation
			tl.staggerFromTo(
				elements,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0.1,
				0
			);

			tl.call(function() {
				elements.css({opacity: '', transform: ''});
			});


		} else if (screen.hasClass('screen-confirm-add-to-cart')) {

			// Selectors
			var elements = screen.find('.title, .product, .tip');

			// Before animation
			screen.css({opacity: ''});
			elements.css({opacity: 0});

			// Animation
			tl.staggerFromTo(
				elements,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0.1,
				0
			);

			tl.call(function() {
				elements.css({opacity: '', transform: ''});
			});

		} else if (screen.hasClass('screen-checkout')) {

			// Selectors
			var elements = screen.find('.step-review').find('.title, .order-head, .item-line, .order-total, .cta .btn-text');

			// Before animation
			screen.css({opacity: ''});
			elements.css({opacity: 0});

			// Animation
			tl.staggerFromTo(
				elements,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power3.easeOut
				},
				0.1,
				0
			);

			tl.call(function() {
				elements.css({opacity: '', transform: ''});
			});

		} else {

			tl.to(
				screen,
				0.5,
				{
					alpha: 1
				},
				0
			);

		}

		tl.call(function() {
			screen.css({opacity: ''});

			loadScreenWait = false;
		});

		tl.play();
	}


	/**
	 * Outro screen
	 */
	var outroScreen = function(screen) {
		var tl = new TimelineLite();
		tl.pause();

		if (screen.hasClass('screen-select')) {
			tl.call(function() {
				$('.site-background').addClass('is-blurred');
				$('.site-sidebar').addClass('is-visible');
			}, null, null, 0);

			tl.fromTo(
				screen.find('.phrase'),
				0.6,
				{
					alpha: 1,
					scale: 1
				},
				{
					alpha: 0,
					scale: 0.85,
					ease: Power1.easeInOut
				},
				0
			);

			tl.to(
				screen,
				0.8,
				{
					alpha: 0
				},
				0
			);
		} else if (screen.hasClass('screen-product')) {

			tl.staggerFromTo(
				screen.find('.nav-tabs .tab').reverse(),
				0.3,
				{
					alpha: 1,
					y: 0
				},
				{
					alpha: 0,
					y: 20,
					ease: Power2.easeIn
				},
				0.1,
				0
			);

			tl.fromTo(
				screen.find('.product-body:visible, .product-similars:visible').first(),
				0.4,
				{
					alpha: 1,
					y: 0
				},
				{
					alpha: 0,
					y: 20,
					ease: Power2.easeIn
				},
				0.1
			);

		} else if (screen.hasClass('screen-catalogue')) {

			tl.staggerFromTo(
				screen.find('.nav-tabs .tab').reverse(),
				0.3,
				{
					alpha: 1,
					y: 0
				},
				{
					alpha: 0,
					y: 20,
					ease: Power2.easeIn
				},
				0.1,
				0
			);

			tl.fromTo(
				screen.find('.screen-tabs'),
				0.4,
				{
					alpha: 1,
					y: 0
				},
				{
					alpha: 0,
					y: 20,
					ease: Power2.easeIn
				},
				0.1
			);

		} else {
			tl.fromTo(
				screen,
				0.5,
				{
					alpha: 1,
					y: 0
				},
				{
					alpha: 0,
					y: 20,
					ease: Power3.easeIn
				},
				0
			);
		}

		tl.call(function() {
			screen.remove();
		});

		tl.play();
	}


	/**
	 * Switch sidebar
	 */
	 var changing = 'no';
	var switchSidebar = function(e) {
		var background = $('.site-background');
		var sidebar = $('.site-sidebar');
		var head = sidebar.find('.sidebar-head');
		var separator = sidebar.find('.separator');
		var dots = separator.find('.dot');
		var areaCheckout = sidebar.find('.area-checkout');
		var areaInfo = sidebar.find('.area-info');

		// Activate sidebar
		if (e.type == 'stateactive') {
			// Before animation
			areaCheckout.find('> *').css({transition: 'none'});
			areaInfo.find('> *').css({transition: 'none'});
			dots.css({animation: 'none'});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.call(function() {
				background.addClass('is-blurred');
			}, null, null, 0);

			tl.to(
				head,
				1,
				{
					top: 0,
					ease: Power3.easeInOut
				},
				0
			);

			tl.set(
				separator,
				{
					display: 'block'
				},
				0
			);

			tl.staggerTo(
				dots.reverse(),
				0.5,
				{
					alpha: 1
				},
				0.05,
				0.2
			);

			tl.to(
				areaCheckout.find('.area-border'),
				0.5,
				{
					alpha: 1,
				},
				0.35
			);

			tl.fromTo(
				areaCheckout.find('.area-text'),
				0.5,
				{
					alpha: 0,
					scale: 0.9
				},
				{
					alpha: 1,
					scale: 1,
					ease: Power3.easeOut
				},
				0.6
			);

			tl.call(function() {
				head.css({top: ''});
				separator.css({display: ''});
				dots.css({animation: ''});
				areaCheckout.find('> *').css({transition: 'none', opacity: '', transform: ''});
				areaInfo.find('> *').css({transition: 'none', opacity: '', transform: ''});

				sidebar.addClass('is-active');
				background.addClass('is-blurred');
			});

			tl.call(function() {
				dots.css({opacity: ''});
			}, null, null, '+=0.6');

			tl.play();
		} else if (e.type == 'stateinactive') {
			// Before animation
			areaCheckout.find('> *').css({transition: 'none'});
			areaInfo.find('> *').css({transition: 'none'});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.to(
				areaCheckout.find('.area-border, .area-text, .area-arrows'),
				0.5,
				{
					alpha: 0,
				},
				0
			);

			tl.to(
				separator,
				0.5,
				{
					alpha: 0
				},
				0
			);
			tl.to(
				head,
				1,
				{
					top: 450,
					ease: Power3.easeInOut
				},
				0.4
			);

			tl.fromTo(
				areaInfo.find('.area-text'),
				0.5,
				{
					alpha: 1,
					scale: 1
				},
				{
					alpha: 0,
					scale: 0.9
				},
				0
			);

			tl.to(
				areaInfo.find('.area-arrows'),
				0.5,
				{
					alpha: 1,
				},
				0.9
			);

			tl.call(function() {
				areaInfo.find('.tip-off').css({display: 'block'});
				areaInfo.find('.tip-back').css({display: 'none'});
			}, null, null, 0.9);

			tl.fromTo(
				areaInfo.find('.area-text'),
				0.5,
				{
					alpha: 0,
					scale: 0.9
				},
				{
					alpha: 1,
					scale: 1,
					ease: Power3.easeInOut
				},
				0.9
			);

			tl.call(function() {
				head.css({top: ''});
				separator.css({opacity: ''});
				areaCheckout.find('> *').css({transition: 'none', opacity: '', transform: ''});
				areaInfo.find('> *').css({transition: 'none', opacity: '', transform: ''});
				areaInfo.find('.tip').css({display: ''});

				sidebar.removeClass('is-active is-checkout is-info');
				$('body').removeClass('sidebar-info sidebar-checkout');
			});

			tl.play();
		}

		if (e.type == 'stateinfo') {
			if(changing =='no') {
				$(window).trigger('markerProduct');
				changing = 'yes';
			}
			setTimeout(function(){
			  changing = 'no';
			}, 1000);
			//console.log('marker: loaded product')
			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.to(
				areaCheckout.find('.area-text, .area-arrows'),
				0.5,
				{
					alpha: 1,
				},
				0
			);

			tl.to(
				areaInfo.find('.area-text, .area-arrows'),
				0.5,
				{
					alpha: 0,
				},
				0
			);

			tl.call(function() {
				sidebar.addClass('is-info');
				sidebar.removeClass('is-checkout');

				$('body').addClass('sidebar-info');
				$('body').removeClass('sidebar-checkout');
			});

			tl.play();

			sidebar.data('state', 'info');
		} else if (e.type == 'statecheckout') {
			if(changing =='no') {
				$(window).trigger('markerCart');
				changing = 'yes';
			}
			setTimeout(function(){
			  changing = 'no';
			}, 1000);
			// Before animation
			areaInfo.find('.tip-off').css({display: 'none'});
			areaInfo.find('.tip-back').css({display: 'block'});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.to(
				areaInfo.find('.area-text, .area-arrows'),
				0.5,
				{
					alpha: 1,
				},
				0
			);

			tl.to(
				areaCheckout.find('.area-text, .area-arrows'),
				0.5,
				{
					alpha: 0,
				},
				0
			);

			tl.call(function() {
				areaInfo.find('.tip').css({display: ''});

				sidebar.addClass('is-checkout');
				sidebar.removeClass('is-info');

				$('body').removeClass('sidebar-info');
				$('body').addClass('sidebar-checkout');

			});

			tl.play();

			sidebar.data('state', 'checkout');
		}
	}


	/**
	 * Touches handler
	 */
	var touchesHandler = function(e) {
		// Selectors
		var area = $('.site-sidebar .sidebar-areas');
		var sidebar = $('.site-sidebar');

		// Bounds
		var boundXLeft = area.offset().left;
		var boundXRight = boundXLeft+area.width();
		var boundYTop = area.offset().top;
		var boundYBottom = boundYTop+area.height();

		var state = null;

		if (e.touches.length > 1) {
			// Get touches vertical center
			var verticalCenter = 0;
			var markerTop = markerBottom = e.touches[0].screenY;

			for (i=0, l=e.touches.length; i < l; i++){
				var touchX = e.touches[i].screenX;
				var touchY = e.touches[i].screenY;

				if (touchX > boundXLeft && touchX < boundXRight && touchY > boundYTop && touchY < boundYBottom) {
					markerTop = Math.min(markerTop, touchY);
					markerBottom = Math.max(markerBottom, touchY);
				}
			}
			verticalCenter = (markerTop+markerBottom)/2;

			if (verticalCenter > $(window).height()/2 && !$('.site-sidebar').hasClass('is-info')) {
				state = 'info';
				$('.site-sidebar').data('state', state);
			} else if (verticalCenter <= $(window).height()/2 && !$('.site-sidebar').hasClass('is-checkout')) {
				state = 'checkout';
				$('.site-sidebar').data('state', state);
			}
		}

		if (sidebar.hasClass('is-active') && e.touches.length > 1) {
			if (state == 'info') {
				if ($('.site-main .screen').hasClass('screen-confirm-add-to-cart')) {
					//console.debug('back');
					linkBack();
				} else {
					//console.debug('object');
					showScreenObject();
				}
				$('.site-sidebar').trigger('stateinfo');
			} else if (state == 'checkout') {
				showScreenAddToCart();
				$('.site-sidebar').trigger('statecheckout');
			}
		}

		// Process touches with OOR
		process_touches(e)

		inactivityHandler();

		e.preventDefault && e.preventDefault();
		e.stopPropagation && e.stopPropagation();
	}


	/**
	 * Area handler
	 */
	var areaHandler = function(e) {
		// Selectors
		var area = $(this);
		var sidebar = $('.site-sidebar');

		// Test dev
		if (window.location.href.indexOf('click') !== -1) {
			e.type = 'object_recognized';
			e.detail.pattern = 'CCE';
		}

		if (e.type == 'object_recognized' && activeObject === false) {
			console.debug('New objet recognized ('+e.detail.pattern+'), waiting for init in '+recognitionDelay+'ms');

			clearTimeout(objectOnTimeout);
			objectOnTimeout = undefined;

			// New object placed on screen, prepare to do corresponding actions after delay
			objectOnTimeout = setTimeout(function() {
				activeObject = e.detail.pattern;
				objectOn();
				objectOnTimeout = undefined;
				console.debug('New objet inited ('+e.detail.pattern+')');
			}, recognitionDelay );

			clearTimeout(objectOffTimeout);
			objectOffTimeout = undefined;
		} else if (e.type == 'object_recognized' && activeObject == e.detail.pattern) {
			// Active object has been replaced on screen within timeout
			clearTimeout(objectOffTimeout);
			objectOffTimeout = undefined;

			console.debug('Active objet recognized again ('+e.detail.pattern+'), doing nothing');
		} else if (e.type == 'no_object_recognized' && e.detail.touch_map.length == 0 && activeObject !== false && objectOffTimeout == undefined) {
			console.debug('Objet pulled off, waiting for clearing screen in '+recognitionDelay+'ms');

			// Object has been removed, prepare to do corresponding actions after delay
			objectOffTimeout = setTimeout(function() {
				activeObject = false;
				objectOff();
				objectOffTimeout = undefined;

				console.debug('Objet off');
			}, recognitionDelay );
		} else if (e.type == 'no_object_recognized' && objectOffTimeout == undefined && objectOnTimeout != undefined) {
			clearTimeout(objectOnTimeout);
			objectOnTimeout = undefined;

			console.debug('No object recognized, clear object initialization');
		}
	}
	var objectOffTimeout = objectOnTimeout = undefined;


	/**
	 * Object has been placed on screen
	 */
	var objectOn = function() {
		// Switch on sidebar
		$('.site-sidebar').trigger('stateactive');
		if ($('.site-sidebar').data('state') == 'info') {
			// Show object screen
			var url = OBJECTS[activeObject].url;

			// Load screen
			loadScreen(url, false);
		} else {
			showScreenAddToCart();
		}
	}


	/**
	 * Object has been removed on screen
	 */
	var objectOff = function() {
		// Switch on sidebar
		$('.site-sidebar').trigger('stateinactive');
	}


	/**
	 * Change cart quantity
	 */
	var changeCartQty = function(e) {
		// Selectors
		var button = $(this);
		var input = button.siblings('.qty').find('input');

		// Change value
		var value = Number(input.val());
		if (button.hasClass('btn-minus'))
			value = Math.max(1, value-1);
		else
			value = value+1;

		input.val(value).trigger('change');
	}


	/**
	 * Add product to cart
	 */
	var addToCart = function(e) {
		// Selectors
		var form = $('.form-add-to-cart');
		var product_id = form.find('input[name="product_id"]').val();
		var amount = form.find('.select-amount .option.is-selected').attr('data-value');
		var qty = form.find('input[name="qty"]').val();
		var buttons = form.find('.actions .btn-text');

		// Call function
		$.post(
			WRK.ajax_url,
			{
				'action': 'tpb_add_to_cart',
				'product_id': product_id,
				'amount': amount,
				'qty': qty,
			},
			function(response) {
				//console.debug(response);
				response = jQuery.parseJSON(response);

				if (response.success == 1) {
					// Before animation
					var button = buttons.filter('.btn-add-to-cart');
					var otherButtons = buttons.filter('.btn-cart-back, .btn-cart-checkout');
					var textOn = button.find('.text-on');
					var textOff = button.find('.text-off');

					textOn.css({display: 'block', width: textOn.width()});
					var fromWidth = button.outerWidth();
					button.addClass('is-added');
					var toWidth = button.outerWidth();

					button.css({width: fromWidth});
					textOff.css({display: 'none'});

					// Animation
					var tl = new TimelineLite();
					tl.pause();

					tl.fromTo(
						textOn,
						0.3,
						{
							alpha: 1,
							y: 0
						},
						{
							alpha: 0,
							y: 10,
							ease: Power3.easeInOut
						},
						0
					);

					tl.set(
						textOn,
						{
							display: 'none'
						}
					);

					tl.set(
						textOff,
						{
							display: 'block'
						}
					);

					tl.fromTo(
						textOff,
						0.3,
						{
							alpha: 0,
							y: 10
						},
						{
							alpha: 1,
							y: 0,
							ease: Power3.easeInOut
						},
						0.3
					);

					tl.to(
						button,
						0.4,
						{
							width: toWidth,
							ease: Power3.easeInOut
						},
						0.2
					);

					tl.fromTo(
						button,
						0.3,
						{
							alpha: 1,
							y: 0
						},
						{
							alpha: 0,
							y: 20,
							ease: Power3.easeIn
						},
						1.5
					);

					tl.call(function() {
						otherButtons.css({opacity: 0});
						otherButtons.removeClass('is-hidden');
						button.remove();
					});

					tl.staggerFromTo(
						otherButtons,
						0.3,
						{
							alpha: 0,
							y: 20
						},
						{
							alpha: 1,
							y: 0,
							ease: Power3.easeOut
						},
						0.1
					);

					tl.play();

					updateCartButton(response.count_cart);
				} else {
					alert('Error while adding to cart, please try again.');
				}
			}
		);

		e.preventDefault();
	}


	/**
	 * Remove item from cart
	 */
	var removeFromCart = function() {
		$(this).closest('.item-line').remove();
		changeCartPrice();
	}


	/**
	 * Update cart button
	 */
	var updateCartButton = function(count) {
		var button = $('.site-topbar .toggle-cart');
		button.find('.counter').html(count);

		if (count > 0)
			button.addClass('is-filled');
		else
			button.removeClass('is-filled');
	}


	/**
	 * Change product price
	 */
	var changeProductPrice = function(e) {
		if ( $('.screen-checkout').length == 1) {
			var line = $(this).closest('.qty-ancestor');
			var output = line.find('.line-price');
			var qty = Number(line.find('[name="qty"]').val());
			var unitPrice = Number(line.find('.select-amount .option.is-selected').attr('data-price'));

			// Change price
			var price = unitPrice*qty;
			output.text('$'+price);

			changeCartPrice();
		} else if ( $('.screen-add-to-cart').length == 1) {
			var form = $('.form-add-to-cart');
			var output = form.find('.qty-price');
			var qty = Number(form.find('[name="qty"]').val());
			var unitPrice = Number(form.find('.select-amount .option.is-selected').attr('data-price'));

			// Change price
			var price = unitPrice*qty;
			output.text('$'+price);
		}
	}

	/**
	 * Change cart price
	 */
	var changeCartPrice = function(e) {
		// Selectors
		var table = $('.order-lines');
		if (!table.length == 1)
			return;

		// Calculate price
		var prices = table.find('.line-price');
		var total = 0;

		prices.each(function() {
			total += Number($(this).text().replace('$', ''));
		});

		$('.order-total .total').text('$'+(Math.round(total*100)/100));

		// Update cart session
		clearTimeout(updateCartTimeout);
		updateCartTimeout = setTimeout(updateCart, 250);
	}


	/**
	 * Update cart session
	 */
	var updateCart = function() {
		// Selectors
		var table = $('.order-lines');
		var lines = table.find('.item-line');

		var products = [];
		lines.each(function() {
			var line = $(this);
			var product_id = line.attr('data-product-id');
			var qty = line.find('[name="qty"]').val();
			var amount = line.find('.select-amount .option.is-selected').attr('data-value');

			products.push({'product_id': product_id, 'amount': amount, 'qty': qty});
		});

		// Call function
		$.post(
			WRK.ajax_url,
			{
				'action': 'tpb_update_cart',
				'products': products
			},
			function(response) {
				//console.debug(response);
				response = jQuery.parseJSON(response);

				if (response.success == 1) {
					updateCartButton(response.count_cart);
				} else {
					alert('Error while updating cart, please try again.');
				}
			}
		);
	}
	var updateCartTimeout = null;


	/**
	 * User select
	 */
	var userSelect = function() {
		// Selector
		var option = $(this);
		var select = option.closest('.user-select');
		var options = select.find('.option');
		var previousOption = options.filter('.is-selected');

		if (options.length < 2)
			return;

		if (select.hasClass('is-opened') && select.hasClass('select-amount') && !previousOption.is(option)) {
				select.closest('.qty-ancestor').find('[name="qty"]').val(1);
			}

		if (select.hasClass('in-line')) {
			previousOption.removeClass('is-selected');
			option.addClass('is-selected');
			select.trigger('change');
		} else {
			if (select.hasClass('is-opened')) {
				// Before animation
				var fromHeight = select.outerHeight();
				select.removeClass('is-opened');
				var toHeight = select.outerHeight();
				options.css({display: 'block'});

				previousOption.removeClass('is-selected');
				option.addClass('is-selected');
				select.trigger('change');

				// Animation
				var tl = new TimelineLite();
				tl.pause();

				tl.fromTo(
					select,
					0.5,
					{
						height: fromHeight
					},
					{
						height: toHeight,
						ease: Power3.easeInOut
					},
					0
				);

				tl.staggerFromTo(
					options.not('.is-selected'),
					0.3,
					{
						alpha: 1,
						y: 0
					},
					{
						alpha: 0,
						y: 10,
						ease: Power3.easeIn
					},
					0.1,
					0
				);

				tl.call(function() {
					select.css({height: ''});
					options.css({display: ''});
					options.not('.is-selected').css({opacity: '', transform: ''});

					select.removeClass('is-opened');
				})

				tl.play();

			} else {
				// Before animation
				var fromHeight = select.outerHeight();
				select.addClass('is-opened');
				var toHeight = select.outerHeight();

				// Animation
				var tl = new TimelineLite();
				tl.pause();

				tl.fromTo(
					select,
					0.5,
					{
						height: fromHeight
					},
					{
						height: toHeight,
						ease: Power3.easeInOut
					},
					0
				);

				tl.staggerFromTo(
					options.not('.is-selected'),
					0.3,
					{
						alpha: 0,
						y: 10
					},
					{
						alpha: 1,
						y: 0,
						ease: Power3.easeOut
					},
					0.1,
					0.2
				);

				tl.call(function() {
					select.css({height: ''});
					options.not('.is-selected').css({opacity: '', transform: ''});
				})

				tl.play();

			}
		}
	}


	/**
	 * List order
	 */
	var listOrder = function() {
		var select = $(this);
		var container = select.closest('.products-list').find('.products');
		var products = container.find('.product');

		var choice = select.find('.option.is-selected').attr('data-value');
		var parts = choice.split('-');

		sortMeBy('data-'+parts[0], container, products, parts[1]);
	}


	/**
	 * List filter
	 */
	var listFilter = function() {
		var select = $(this);
		var container = select.closest('.products-list').find('.products');
		var products = container.find('.product');

		var choice = select.find('.option.is-selected').attr('data-value');
		if (choice == 'all') {
			products.show();
		} else {
			var selectedProducts = products.filter('[data-type="'+choice+'"]');

			products.not(selectedProducts).hide();
			selectedProducts.show();
		}
	}


	/**
	 * Check input
	 */
	var checkInput = function(e) {
		var label = $(this);
		var input = label.siblings('input');

		input.prop('checked', true).trigger('change');

		e.preventDefault();
	}


	/**
	 * Submit form
	 */
	var submitForm = function() {
		var button = $(this);
		var form = button.closest('form');

		form.submit();
	}


	/**
	 * Focus input
	 */
	var inputTextFocus = function() {
		var input = $(this);

		input.click();
	}


	/**
	 * Keyboard touch
	 */
	var keyboardLiTouch = function(e) {
		$(this).click();

		e.preventDefault();
	}


	/**
	 * Touch feedback
	 */
	var touchFeedback = function(e) {
		var feedback = $('.touch-feedback');

		// Get touch coordinates
		var x = e.touches[e.touches.length-1].pageX;
		var y = e.touches[e.touches.length-1].pageY;

		feedback.css({top: y, left: x});

		// Animate
		var tl = new TimelineLite();
		tl.pause();

		tl.fromTo(
			feedback,
			0.4,
			{
				scale: 0
			},
			{
				scale: 1
			},
			0
		);

		tl.fromTo(
			feedback,
			0.20,
			{
				alpha: 1
			},
			{
				alpha: 0
			},
			0.20
		);

		tl.play();
		// TweenMax.fromTo(
		// 	feedback,
		// 	0.3,
		// 	{
		// 		alpha: 1,
		// 		scale: 0
		// 	},
		// 	{
		// 		alpha: 0,
		// 		scale: 1,
		// 		ease: Linear.easeNone
		// 	}
		// );
	}


	/**
	 * Touch scroll
	 */
	var touchScroll = function(e) {
		var scroller = $(this);

		// Current values
		var initScroll = scroller.data('initScroll');
		var startX = scroller.data('startX');
		var startY = scroller.data('startY');
		var currentY = scroller.data('currentY');
		var previousY = scroller.data('currentY');
		var distY = 0;

		scroller.data('state', e.type);

		$('.user-select.is-opened').removeClass('is-opened');

		if (e.type == 'touchstart') {

			// Get touch coordinates
			startX = e.touches[e.touches.length-1].pageX;
			startY = e.touches[e.touches.length-1].pageY;

			// Set initial values
			scroller.data('startX', startX);
			scroller.data('startY', startY);
			scroller.data('initScroll', scroller.scrollTop());
			scroller.data('currentScroll', scroller.scrollTop());
			scroller.data('destScroll', scroller.scrollTop());
			scroller.data('distY', distY);
			scroller.data('speedY', 0);
			scroller.data('scrollMax', scroller.find('> div').outerHeight() - scroller.height());

			// Start movement
			var rafID = scroller.data('rafID');
			if (rafID !== undefined)
				cancelAnimationFrame(rafID);
			smoothScrollMove(scroller);

		} else if (e.type == 'touchmove') {
			// Get touch coordinates
			currentY = e.touches[e.touches.length-1].pageY;

			distY = startY - currentY

			scroller.data('currentY', currentY);
			scroller.data('distY', distY);
			scroller.data('speedY', previousY-currentY);

		} else if (e.type == 'touchend') {

			// Reset values
			currentY = startX = startY = null;
			scroller.data('startX', startX);
			scroller.data('startY', startY);
			scroller.data('currentY', currentY);

		}

		if (startY != null && currentY != null) {
			// Reverse direction when moving the scrollbar
			if (startX >= scroller.offset().left+scroller.width()-20)
				distY *= -1;

			// Calculate scroll
			var destScroll = initScroll + distY;

			scroller.data('destScroll', destScroll);
		}

		e.preventDefault();
	}

	/**
	 * Scroll move
	 */
	var smoothScrollMove = function(scroller) {
		// Move container
		var destScroll = scroller.data('destScroll');
		var currentScroll = scroller.data('currentScroll');
		var state = scroller.data('state');
		var speedY = scroller.data('speedY');
		var distY = scroller.data('distY');

		var scrollTop = destScroll;

		// On touchend jump to destination based on speed (momentum)
		if (state == 'touchend') {
			var jump = speedY*20;

			// Limit jump to touch distance
			if (jump > 0)
				jump = Math.min(jump, distY);
			else
				jump = Math.max(jump, distY);

			scrollTop += jump;
		}

		// Inertia
		var ratio = 0.25;

		if (Math.round(currentScroll) != Math.round(scrollTop)) {
			var scrollMax = scroller.data('scrollMax');

			var move = (scrollTop-currentScroll)*ratio;
			if (move > 0)
				move = Math.max(1, move);
			else
				move = Math.min(1, move);

			// Calculate scroll top
			scrollTop = Math.round(currentScroll+move);
			scrollTop = Math.max(0, scrollTop);
			scrollTop = Math.min(scrollMax, scrollTop);

			scroller.scrollTop(scrollTop);

			scroller.data('currentScroll', scrollTop);
		}

		if (state != 'touchend' || Math.round(currentScroll) != Math.round(scrollTop)) {
			var rafID = requestAnimationFrame(function() { smoothScrollMove(scroller); });
			scroller.data('rafID', rafID);
		}
	}




	/**
	 * Move to next checkout step
	 */
	var checkoutNextStep = function() {
		// Selectors
		var steps = $('.screen-checkout .step');
		if (steps.length == 0)
			return;

		var activeStep = steps.filter('.is-active');
		var nextStep = activeStep.next('.step');

		if (activeStep.hasClass('step-login')) {
			var field = activeStep.find('[name="user_name"]');
			var userName = field.val();

			if (userName == '' || activeStep.find('.btn-checkout-next').hasClass('is-sent'))
				return;

			activeStep.find('.btn-checkout-next').addClass('is-sent');

			$.post(
				WRK.ajax_url,
				{
					'action': 'tpb_handle_checkout',
					'user_name': userName
				},
				function(response) {
					//console.debug(response);
					response = jQuery.parseJSON(response);

					if (response.success == 1) {
						// Set user name
						steps.filter('.step-thank-you').find('.output-name').html(response.user_name);

						checkoutSwitchStep(activeStep, nextStep);

						$(window).trigger('printorder');
					} else {
						alert('Error while saving user name, please try again.');
						activeStep.find('.btn-checkout-next').removeClass('is-sent');
					}

					$('#osk-container:visible .osk-hide').click();
				}
			);
		} else {
			checkoutSwitchStep(activeStep, nextStep);
		}
	}


	/**
	 * Checkout switch step
	 */
	var checkoutSwitchStep = function(activeStep, nextStep) {
		// Animation
		var tl = new TimelineLite();
		tl.pause();

		// Animation
		tl.fromTo(
			activeStep,
			0.3,
			{
				alpha: 1,
				y: 0
			},
			{
				alpha: 0,
				y: 20,
				ease: Power3.easeIn
			},
			0
		);

		tl.call(function() {
			activeStep.removeClass('is-active');
			nextStep.addClass('is-active');
		})

		tl.fromTo(
			nextStep,
			0.3,
			{
				alpha: 0,
				y: 20
			},
			{
				alpha: 1,
				y: 0,
				ease: Power3.easeOut
			},
			0.3
		);

		tl.call(function() {
			activeStep.css({opacity: '', transform: ''});
			nextStep.css({opacity: '', transform: ''});
		});

		tl.play();
	}


	/**
	 * Reset session
	 */
	var resetSession = function() {
		$.post(
			WRK.ajax_url,
			{
				'action': 'tpb_reset_session'
			},
			function(response) {
				response = jQuery.parseJSON(response);

				if (response.success == 1) {
					window.location = '/';
				} else {
					alert('Error while reseting session, please try again.');
				}
			}
		);
	}


	/**
	 * Update files
	 */
	var updateFiles = function() {
		$.post(
			WRK.ajax_url,
			{
				'action': 'tpb_update_files'
			},
			function(response) {
				console.debug(response);
			}
		);
	}


	/**
	 * Switch screen tabs
	 */
	var switchScreenTabs = function() {
		if (switchTabWait)
			return;

		// Selectors
		var newButton = $(this).parent();
		var activeButton = newButton.siblings('.is-active');
		var screen = newButton.closest('.screen');
		var body = screen.find('.product-body');
		var similars = screen.find('.product-similars');
		var tabs = screen.find('.screen-tabs .tab');
		var activeTab = tabs.filter('.is-active');
		var newTab = tabs.eq(newButton.index());

		if (newTab.is(':visible'))
			return;

		switchTabWait = true;

		// Switch buttons
		activeButton.removeClass('is-active');
		newButton.addClass('is-active');

		if ($(this).hasClass('btn-tab-similar')) {
			outroTab(body);
			introTab(similars);
		} else if (screen.hasClass('screen-product') && similars.hasClass('is-visible')) {
			outroTab(similars);
			introTab(newTab);
		} else {
			outroTab(activeTab);
			introTab(newTab);
		}
	}
	var switchTabWait = true;


	/**
	 * Intro tab
	 */
	var introTab = function(tab) {
		// Selectors
		var screen = tab.closest('.screen');

		// Animation
		var tl = new TimelineLite();
		tl.pause();

		if (tab.hasClass('product-similars')) {

			// Selectors
			var body = tab.siblings('.product-body');

			// Before animation
			tab.css({opacity: 0});
			tab.addClass('is-visible');

			tl.fromTo(
				tab,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.3
			);

			tl.staggerFromTo(
				tab.find('.product').slice(0, 9),
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.05,
				0.4
			);

			tl.call(function() {
				body.css({opacity: '', transform: ''});
				tab.css({opacity: '', transform: ''});
				tab.find('.product').slice(0, 6).css({opacity: '', transform: ''});

				body.addClass('is-hidden');
			});

			tl.call(function() {
				switchTabWait = false;
			});

		} else if (tab.hasClass('tab-product')) {

			// Selectors
			var body = screen.find('.product-body');
			var similars = screen.find('.product-similars');

			tl.call(function() {
				productIntroTab(tab);
			}, null, null, 0.3);

			if (similars.hasClass('is-visible')) {

				// Before animation
				body.css({opacity: 0});
				body.removeClass('is-hidden');
				tab.css({opacity: 0});
				tab.siblings('.tab').removeClass('is-active');

				tl.call(function() {
					tab.addClass('is-active');
				}, null, null, 0.3)

				tl.fromTo(
					body,
					0.3,
					{
						alpha: 0,
						y: 20
					},
					{
						alpha: 1,
						y: 0,
						ease: Power2.easeOut
					},
					0.3
				);

				tl.call(function() {
					body.css({opacity: '', transform: ''});
					similars.css({opacity: '', transform: ''});

					body.removeClass('is-hidden');
					similars.removeClass('is-visible');
				});

			}

		} else if (tab.hasClass('tab-catalogue')) {

			// Selectors
			var products = tab.find('.products-list .product');
			var title = tab.find('.tab-title');
			var filters = tab.find('.list-filter');

			// Before animation
			tab.css({opacity: ''});
			title.css({opacity: 0});
			filters.css({opacity: 0});
			products.slice(0, 6).css({opacity: 0});
			products.slice(6).css({display: 'none'});

			tl.call(function() {
				tab.addClass('is-active');
			}, null, null, 0.3)

			tl.fromTo(
				title,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.3
			);

			tl.staggerFromTo(
				filters,
				0.3,
				{
					alpha: 0
				},
				{
					alpha: 1,
					ease: Power2.easeOut
				},
				0.1,
				0.45
			);

			tl.staggerFromTo(
				products.slice(0, 6),
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.05,
				0.45
			);

			tl.call(function() {
				title.css({opacity: '', transform: ''});
				filters.css({opacity: '', transform: ''});
				products.css({display: '', opacity: '', transform: ''});
			});

			tl.call(function() {
				switchTabWait = false;
			});

		} else {

			tl.call(function() {
				tab.addClass('is-active');
			}, null, null, 0.3)

			tl.fromTo(
				tab,
				0.3,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.3
			);

			tl.call(function() {
				switchTabWait = false;
			});

		}

		tl.play();

	}


	/**
	 * Outro tab
	 */
	var outroTab = function(tab) {
		var tl = new TimelineLite();
		tl.pause();

		tl.fromTo(
			tab,
			0.3,
			{
				alpha: 1,
				y: 0
			},
			{
				alpha: 0,
				y: 20,
				ease: Power2.easeIn
			},
			0
		);

		tl.call(function() {
			if (!tab.hasClass('product-similars') && !tab.hasClass('product-body')) {
				tab.css({opacity: '', transform: ''});
				tab.removeClass('is-active');
			}
		})

		tl.play();
	}


	/**
	 * Product intro tab
	 */
	var productIntroTab = function(tab) {
		var tabs = $('.screen-product .screen-tabs .tab');

		// Before animation
		tab.css({display: 'block', opacity: 0});

		if (tab.hasClass('tab-highlights')) {
			// Selectors
			var description = tab.find('.description');
			var borders = tab.find('.border');
			var graphs = tab.find('.graph');
			var lines = tab.find('.graph .line circle:last-child');
			var titles = tab.find('.graph .title');
			var values = tab.find('.graph .values');
			var video = tab.find('.video-container');

			// Before animation
			description.css({opacity: 0});
			borders.css({opacity: 0});
			graphs.css({opacity: 0});
			titles.css({opacity: 0});
			values.css({opacity: 0});
			video.css({opacity: 0});

			tab.css({opacity: ''});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.fromTo(
				description,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0
			);

			tl.staggerFromTo(
				borders,
				0.5,
				{
					alpha: 1,
					scaleX: 0
				},
				{
					alpha: 1,
					scaleX: 1,
					ease: Power3.easeInOut
				},
				0.1,
				0
			);

			tl.staggerFromTo(
				borders.find('.logo'),
				0.5,
				{
					alpha: 0,
				},
				{
					alpha: 1,
				},
				0.1,
				0.3
			);

			tl.staggerFromTo(
				graphs,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.2,
				0.2
			);

			tl.staggerFrom(
				lines,
				0.8,
				{
					strokeDashoffset: 502,
					ease: Power2.easeOut
				},
				0.2,
				0.2
			);

			tl.staggerFromTo(
				titles,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.2,
				0.3
			);

			tl.staggerFromTo(
				values,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.2,
				0.4
			);

			tl.fromTo(
				video,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.6
			);

			tl.call(function() {
				description.css({opacity: '', transform: ''});
				borders.css({opacity: '', transform: ''});
				graphs.css({opacity: '', transform: ''});
				titles.css({opacity: '', transform: ''});
				values.css({opacity: '', transform: ''});
				video.css({opacity: '', transform: ''});

				tabs.not(tab).removeClass('is-active');
				tab.addClass('is-active');
				tab.css({display: ''});

				switchTabWait = false;
			});

			tl.play();

		} else if (tab.hasClass('tab-attributes')) {
			var delay = 0;
			tab.css({opacity: ''});

			var tl = new TimelineLite();
			tl.pause();

			tab.find('.column').each(function() {
				// Selectors
				var column = $(this);
				var title = column.find('.column-title');
				var values = column.find('.value');
				var bars = column.find('.bar');
				var progresses = column.find('.progress');

				// Before animation
				title.css({opacity: 0});
				values.css({opacity: 0});
				bars.css({opacity: 0});
				progresses.css({opacity: 0});

				// Animation
				tl.fromTo(
					title,
					0.5,
					{
						alpha: 0,
						y: 20
					},
					{
						alpha: 1,
						y: 0,
						ease: Power2.easeOut
					},
					delay
				);

				tl.staggerFromTo(
					values,
					0.5,
					{
						alpha: 0,
						y: 20
					},
					{
						alpha: 1,
						y: 0,
						ease: Power2.easeOut
					},
					0.1,
					delay+0.1
				);

				tl.staggerFromTo(
					bars,
					0.5,
					{
						alpha: 1,
						scaleX: 0
					},
					{
						alpha: 1,
						scaleX: 1,
						ease: Power3.easeInOut
					},
					0.1,
					delay+0.2
				);

				tl.staggerFromTo(
					progresses,
					0.5,
					{
						alpha: 1,
						scaleX: 0
					},
					{
						alpha: 1,
						scaleX: 1,
						ease: Power3.easeInOut
					},
					0.1,
					delay+0.4
				);

				tl.call(function() {
					title.css({opacity: '', transform: ''});
					values.css({opacity: '', transform: ''});
					bars.css({opacity: '', transform: ''});
					progresses.css({opacity: '', transform: ''});
				})

				delay += 0.3;
			});

			tl.call(function() {
				tabs.not(tab).removeClass('is-active');
				tab.addClass('is-active');
				tab.css({display: ''});

				switchTabWait = false;
			});

			tl.play();

		} else if (tab.hasClass('tab-reviews')) {
			// Selectors
			var review = tab.find('.review.is-active');
			var elements = review.find('.portrait, .name, .review-content');
			var stars = review.find('.note svg:visible');
			var navigation = tab.find('.navigation > *');

			// Before animation
			elements.css({opacity: 0});
			stars.css({opacity: 0});
			navigation.css({opacity: 0});

			tab.css({opacity: ''});

			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.staggerFromTo(
				elements,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.1,
				0
			);

			tl.staggerFromTo(
				stars,
				0.5,
				{
					alpha: 0,
					scale: 0
				},
				{
					alpha: 1,
					scale: 1,
					ease: Power3.easeOut
				},
				0.05,
				0.2
			);

			tl.staggerFromTo(
				navigation,
				0.5,
				{
					alpha: 0,
					y: 20
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0.1,
				0.4
			);

			tl.call(function() {
				elements.css({opacity: '', transform: ''});
				stars.css({opacity: '', transform: ''});
				navigation.css({opacity: '', transform: ''});

				tabs.not(tab).removeClass('is-active');
				tab.addClass('is-active');
				tab.css({display: ''});

				switchTabWait = false;
			});

			tl.play();

		} else {
			// Animation
			var tl = new TimelineLite();
			tl.pause();

			tl.fromTo(
				tab,
				0.5,
				{
					alpha: 0,
					y: 50
				},
				{
					alpha: 1,
					y: 0,
					ease: Power2.easeOut
				},
				0
			);

			tl.call(function() {
				tabs.not(tab).removeClass('is-active');
				tab.addClass('is-active');
				tab.css({display: '', opacity: '', transform: ''});

				switchTabWait = false;
			});

			tl.play();
		}
	}


	/**
	 * Open video
	 */
	var openVideo = function(e) {
		// Selectors
		var link = $(this);
		var href = link.attr('href');

		var width = 1920;
		var height = 1080;

		$.fancybox.open(
			[
				{
					href : href,
					type: 'iframe'
				}
			],
			{
				padding: 0,
				margin: [50, 50, 50, 550],
				width: width,
				height: height,
				scrollOutside: false,
				helpers: {
					overlay: {
					  locked: false
					}
				}
			}
		);

	    return false;
	}


	/**
	 * Close video
	 */
	 var closeVideo = function() {
	 	$.fancybox.close();
	 }


	/**
	 * Link back
	 */
	var linkBack = function(e) {
		if (pageHistory.length > 0) {
			historyBack = true;
			var previousPage = pageHistory.pop();
			loadScreen(previousPage[0], previousPage[2]);
		}
	}
	var historyBack = false;


	/**
	 * Public API
	 */
	return {
		init: init
	}
})();



/* =============================================================================
   Background
   ========================================================================== */

// var siteBackground = (function() {
// 	var c = $('.site-background .canvas').get(0),
// 		ctx = c.getContext( '2d' ),
// 		image = $('.site-background .image'),
// 		twopi = Math.PI * 2,
// 		parts = [],
// 		sizeBase,
// 		cw,
// 		opt,
// 		count,
// 		props = {scale: 0};


// 	/**
// 	 * Init
// 	 */
// 	var init = function() {
// 		bindEvents();

// 		resizeHandler();
// 		create();
// 		loop();
// 	}


// 	/**
// 	 * Create
// 	 */
// 	var create = function() {
// 		sizeBase = cw + ch;
// 		count = Math.floor( sizeBase * 0.01 ),
// 		opt = {
// 			radiusMin: 10,
// 			radiusMax: sizeBase * 0.03,
// 			blurMin: 0,
// 			blurMax: 10,
// 		}

// 		parts.length = 0;
// 		for( var i = 0; i < count; i++ ) {
// 			parts.push({
// 				radius: rand( opt.radiusMin, opt.radiusMax ),
// 				blur: rand( opt.blurMin, opt.blurMax ),
// 				x: rand( 0, cw ),
// 				y: rand( 0, ch ),
// 				angle: rand( 0, twopi ),
// 				vel: rand( 0.1, 0.5 ),
// 				tick: rand( 0, 10000 ),
// 				alpha: 0
// 			});
// 		}
// 	}


// 	/**
// 	 * Loop
// 	 */
// 	var loop = function() {
// 		requestAnimationFrame( loop );

// 		ctx.clearRect( 0, 0, cw, ch );
// 		// ctx.globalCompositeOperation = 'lighten';
// 		ctx.shadowBlur = 0;
// 		// ctx.drawImage( c1, 0, 0 );
// 		ctx.globalCompositeOperation = 'lighter';
// 		ctx.drawImage(image.get(0), image.position().left, image.position().top, image.get(0).width, image.get(0).height);

// 		// ctx.globalCompositeOperation = 'destination-in';

// 		var i = parts.length;
// 		ctx.shadowColor = 'white';
// 		while( i-- ) {
// 			var part = parts[ i ];

// 			ctx.shadowBlur = part.blur;

// 			part.x += Math.cos( part.angle ) * part.vel;
// 			part.y += Math.sin( part.angle ) * part.vel;
// 			part.angle += rand( -0.05, 0.05 );

// 			ctx.beginPath();
// 			ctx.arc( part.x, part.y, part.radius, 0, twopi );
// 			ctx.fillStyle = hsla( 0, 0, 100, (0.01 + Math.cos( part.tick * 0.01 ) * 0.02) * part.alpha );
// 			ctx.fill();

// 			if( part.x - part.radius > cw ) { part.x = -part.radius }
// 			if( part.x + part.radius < 0 )  { part.x = cw + part.radius }
// 			if( part.y - part.radius > ch ) { part.y = -part.radius }
// 			if( part.y + part.radius < 0 )  { part.y = ch + part.radius }

// 			part.tick++;
// 		}
// 	}


// 	/**
// 	 * Bind events
// 	 */
// 	var bindEvents = function() {
// 		$(window).on('resize', resizeHandler)
// 	}


// 	/**
// 	 * Resize
// 	 */
// 	var resizeHandler = function() {
// 		cw = /*c1.width =*/ c.width = window.innerWidth,
// 		ch = /*c1.height =*/ c.height = window.innerHeight;
// 		// create();
// 	}


// 	/**
// 	 * Show dots
// 	 */
// 	var showDots = function() {
// 		TweenMax.staggerFromTo(
// 			parts,
// 			1,
// 			{
// 				alpha: 0
// 			},
// 			{
// 				alpha: 1
// 			},
// 			0.02
// 		);
// 	}


// 	/**
// 	 * Public API
// 	 */
// 	return {
// 		init: init,
// 		showDots: showDots
// 	}
// })();


var siteBackground = (function(){

	//------------------------------
	// Mesh Properties
	//------------------------------
	var MESH = {
		width: 1.2,
		height: 1.2,
		depth: 30,
		segments: 8,
		slices: 6,
		xRange: 0.3,
		yRange: 0.3,
		zRange: 1.0,
		ambient: '#555555',
		diffuse: '#ffffff',
		speed: 0.0003,
		factor: 0
	}

	//------------------------------
	// Light Properties
	//------------------------------
	var LIGHT = {
		count: 2,
		xyScalar: 1,
		zOffset: 300,
		ambient: '#000d78',
		diffuse: '#5c015c',
		speed: 0.001,
		gravity: 1200,
		dampening: 0.95,
		minLimit: 10,
		maxLimit: null,
		minDistance: 100,
		maxDistance: 500,
		autopilot: true,
		bounds: FSS.Vector3.create(),
		step: FSS.Vector3.create(
			Math.randomInRange(0.2, 1.0),
			Math.randomInRange(0.2, 1.0),
			Math.randomInRange(0.2, 1.0)
			)
	};

	//------------------------------
	// Global Properties
	//------------------------------
	var now, start = Date.now();
	var center = FSS.Vector3.create();
	var attractor = FSS.Vector3.create();
	var container = $('.site-background').get(0);
	var output = $('.site-background .canvas').get(0);
	var renderer, scene, mesh, geometry, material;

	//------------------------------
	// Methods
	//------------------------------
	var init = function() {
		createRenderer();
		createScene();
		createMesh();
		createLights();
		addEventListeners();
		resize(container.offsetWidth, container.offsetHeight);
		animate();
	}

	function createRenderer() {
		renderer = new FSS.WebGLRenderer();
		renderer.setSize(container.offsetWidth, container.offsetHeight);
		output.appendChild(renderer.element);
	}

	function createScene() {
		scene = new FSS.Scene();
	}

	function createMesh() {
		scene.remove(mesh);
		renderer.clear();
		geometry = new FSS.Plane(MESH.width * renderer.width, MESH.height * renderer.height, MESH.segments, MESH.slices);
		material = new FSS.Material(MESH.ambient, MESH.diffuse);
		mesh = new FSS.Mesh(geometry, material);
		scene.add(mesh);

	// Augment vertices for animation
	var v, vertex;
	for (v = geometry.vertices.length - 1; v >= 0; v--) {
		vertex = geometry.vertices[v];
		vertex.anchor = FSS.Vector3.clone(vertex.position);
		vertex.step = FSS.Vector3.create(
			Math.randomInRange(0.2, 1.0),
			Math.randomInRange(0.2, 1.0),
			Math.randomInRange(0.2, 1.0)
			);
		vertex.time = Math.randomInRange(0, Math.PIM2);
	}
	}

	function createLights() {
		var l, light;
		for (l = scene.lights.length - 1; l >= 0; l--) {
			light = scene.lights[l];
			scene.remove(light);
		}
		renderer.clear();
		for (l = 0; l < LIGHT.count; l++) {
			light = new FSS.Light(LIGHT.ambient, LIGHT.diffuse);
			light.ambientHex = light.ambient.format();
			light.diffuseHex = light.diffuse.format();
			scene.add(light);

	  // Augment light for animation
	  light.mass = Math.randomInRange(0.5, 1);
	  light.velocity = FSS.Vector3.create();
	  light.acceleration = FSS.Vector3.create();
	  light.force = FSS.Vector3.create();
	}
	}

	function resize(width, height) {
		renderer.setSize(width, height);
		FSS.Vector3.set(center, renderer.halfWidth, renderer.halfHeight);
		createMesh();
	}

	function animate() {
		now = Date.now() - start;
		update();
		render();
		requestAnimationFrame(animate);
	}

	function update() {
		var ox, oy, oz, l, light, v, vertex, offset = MESH.depth/2;

	// Update Bounds
	FSS.Vector3.copy(LIGHT.bounds, center);
	FSS.Vector3.multiplyScalar(LIGHT.bounds, LIGHT.xyScalar);

	// Update Attractor
	FSS.Vector3.setZ(attractor, LIGHT.zOffset);

	// Overwrite the Attractor position
	if (LIGHT.autopilot) {
		ox = Math.sin(LIGHT.step[0] * now * LIGHT.speed);
		oy = Math.cos(LIGHT.step[1] * now * LIGHT.speed);
		FSS.Vector3.set(attractor,
			LIGHT.bounds[0]*ox,
			LIGHT.bounds[1]*oy,
			LIGHT.zOffset);
	}

	// Animate Lights
	for (l = scene.lights.length - 1; l >= 0; l--) {
		light = scene.lights[l];

	  // Reset the z position of the light
	  FSS.Vector3.setZ(light.position, LIGHT.zOffset);

	  // Calculate the force Luke!
	  var D = Math.clamp(FSS.Vector3.distanceSquared(light.position, attractor), LIGHT.minDistance, LIGHT.maxDistance);
	  var F = LIGHT.gravity * light.mass / D;
	  FSS.Vector3.subtractVectors(light.force, attractor, light.position);
	  FSS.Vector3.normalise(light.force);
	  FSS.Vector3.multiplyScalar(light.force, F);

	  // Update the light position
	  FSS.Vector3.set(light.acceleration);
	  FSS.Vector3.add(light.acceleration, light.force);
	  FSS.Vector3.add(light.velocity, light.acceleration);
	  FSS.Vector3.multiplyScalar(light.velocity, LIGHT.dampening);
	  FSS.Vector3.limit(light.velocity, LIGHT.minLimit, LIGHT.maxLimit);
	  FSS.Vector3.add(light.position, light.velocity);
	}

	// Animate Vertices
	for (v = geometry.vertices.length - 1; v >= 0; v--) {
		vertex = geometry.vertices[v];
		ox = Math.sin(MESH.factor + vertex.time + vertex.step[0] * now * MESH.speed);
		oy = Math.cos(MESH.factor + vertex.time + vertex.step[1] * now * MESH.speed);
		oz = Math.sin(MESH.factor + vertex.time + vertex.step[2] * now * MESH.speed);
		FSS.Vector3.set(vertex.position,
			MESH.xRange*geometry.segmentWidth*ox,
			MESH.yRange*geometry.sliceHeight*oy,
			MESH.zRange*offset*oz - offset);
		FSS.Vector3.add(vertex.position, vertex.anchor);
	}

	// Set the Geometry to dirty
	geometry.dirty = true;
	}

	function render() {
		renderer.render(scene);
	}

	function addEventListeners() {
		window.addEventListener('resize', onWindowResize);
	}


	//------------------------------
	// Callbacks
	//------------------------------
	function onWindowResize(event) {
		resize(container.offsetWidth, container.offsetHeight);
		render();
	}


	//------------------------------
	// Speed up intro
	//------------------------------
	function speedUp() {
		TweenMax.to(
			MESH,
			1.4,
			{
				factor: 3,
				ease: Power2.easeInOut
			}
		);
	}

	/**
	 * Public API
	 */
	return {
		init: init,
		speedUp: speedUp
	}
})();



/* =============================================================================
   WIDGET: =Slider
   ========================================================================== */

var slider = (function() {

	/**
	 * Init
	 */
	var init = function() {
		var sliders = $('.slider');

		sliders.each(function() {
			var slider = $(this);

			// Swipe handler
			slider.swipe({
				swipeLeft:function() {
					nextSlide($(this), false);
				},
				swipeRight:function() {
					nextSlide($(this), true);
				}
			});

			// Navigation
			slider.on('touchstart', '.navigation .arrow', navigationClick);
		});
	}


    /**
     * Switch slides
     */
    var switchSlides = function(slider, index) {
		if (slider.data('wait'))
			return;

		// Selectors
		var container = slider.find('.slides');
		var slides = slider.find('.slide');
		var activeSlide = slides.filter('.is-active');
		var newSlide = slides.eq(index);
		var pager = slider.find('.current-page');

		if (newSlide.is(activeSlide))
			return;

		// Before animation
		newSlide.css({display: 'block', opacity: 0, position: 'absolute', top: 0, left: 0, width: '100%'});

		if (newSlide.index() > activeSlide.index())
			var left = 100;
		else
			var left = -100;

		// Animation
		var tl = new TimelineLite();
		tl.pause();

		tl.fromTo(
			activeSlide,
			0.5,
			{
				alpha: 1,
				x: 0
			},
			{
				alpha: 0,
				x: -left,
				ease: Power3.easeIn
			},
			0
		);

		tl.fromTo(
			newSlide,
			0.5,
			{
				alpha: 0,
				x: left
			},
			{
				alpha: 1,
				x: 0,
				ease: Power3.easeOut
			},
			0.5
		);

		tl.call(function() {
			newSlide.css({display: '', opacity: '', position: '', top: '', left: '', width: '', transform: ''});
			activeSlide.css({opacity: '', transform: ''});

			newSlide.addClass('is-active');
			activeSlide.removeClass('is-active');

		});

		tl.play();

		pager.text(newSlide.index()+1);
		slider.data('wait', false);
	}


	/**
	 * Move to next slide
	 */
	var nextSlide = function(slider, previous, auto) {
		// Selectors
		var slides = slider.find('.slide');
		var activeSlide = slides.filter('.is-active');
		var newSlide = null;

		if (previous) {
			newSlide = activeSlide.prev('.slide');

			if (newSlide.length == 0)
				newSlide = slides.last();
		} else {
			newSlide = activeSlide.next('.slide');

			if (newSlide.length == 0)
				newSlide = slides.filter('.slide').first();
		}

		switchSlides(slider, newSlide.index());
	}


	/**
	 * Navigation click callback
	 */
	var navigationClick = function() {
		nextSlide($(this).closest('.slider'), $(this).hasClass('prev'));
	}


	/**
	 * Public API
	 */
	return {
		init: init
	}
})();



// Launch site
site.init();