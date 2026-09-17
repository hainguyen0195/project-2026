/* Validation form */
validateForm('validation-newsletter');
validateForm('validation-cart');
validateForm('validation-user');
validateForm('validation-contact');

NN_FRAMEWORK.Common = function () {
	$(".content-ck iframe,.content-ck embed").each(function (e, n) { $(this).wrap("<div class='video-container'></div>") });
	$(".content-ck table").each(function (e, t) { $(this).wrap("<div class='table-responsive'></div>") });
};

/* Lazys */
NN_FRAMEWORK.Lazys = function () {
	if (isExist($('.lazy'))) {
		var lazyLoadInstance = new LazyLoad({
			elements_selector: '.lazy'
		});
	}
};

/* Load name input file */
NN_FRAMEWORK.loadNameInputFile = function () {
	if (isExist($('.custom-file input[type=file]'))) {
		$('body').on('change', '.custom-file input[type=file]', function () {
			var fileName = $(this).val();
			fileName = fileName.substr(fileName.lastIndexOf('\\') + 1, fileName.length);
			$(this).siblings('label').html(fileName);
		});
	}
};

/* Back to top */
NN_FRAMEWORK.GoTop = function () {
	$(window).scroll(function () {
		if (!$('.scrollToTop').length)
			$('body').append('<div class="scrollToTop"><img src="' + GOTOP + '" alt="Go Top"/></div>');
		if ($(this).scrollTop() > 100) $('.scrollToTop').fadeIn();
		else $('.scrollToTop').fadeOut();
	});

	$('body').on('click', '.scrollToTop, .scrollToTopMobile', function () {
		if (window.lenis && typeof window.lenis.scrollTo === 'function') {
			window.lenis.scrollTo(0, { duration: 1.2 });
		} else {
			$('html, body').animate({ scrollTop: 0 }, 300);
		}
		return false;
	});
};

/* Smooth scroll */
NN_FRAMEWORK.SmoothScroll = function () {
	if (typeof Lenis === 'undefined' || window.lenis) return;

	var reduceMotion = window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (reduceMotion) return;

	window.lenis = new Lenis({
		duration: 1.7,
		smoothWheel: true,
		wheelMultiplier: 0.85,
		lerp: 0.05,
		autoRaf: true
	});

	window.dispatchEvent(new CustomEvent('lenis:ready', {
		detail: window.lenis
	}));
};

/* Alt images */
NN_FRAMEWORK.AltImg = function () {
	$('img').each(function (index, element) {
		if (!$(this).attr('alt') || $(this).attr('alt') == '') {
			$(this).attr('alt', WEBSITE_NAME);
		}
	});
};

/* Menu */
NN_FRAMEWORK.Menu = function () {
	/* Menu remove empty ul */
	if (isExist($('.menu'))) {
		$('.menu ul li a').each(function () {
			$this = $(this);

			if (!isExist($this.next('ul').find('li'))) {
				$this.next('ul').remove();
				$this.removeClass('has-child');
			}
		});
	}

	/* Menu fixed */
	$(window).scroll(function () {
		var cach_top = $(window).scrollTop();
		var heaigt_header = $(".head").height() + $(".w-menu").height();

		if (cach_top >= heaigt_header) {
			if (!$(".w-menu").hasClass("fix_head animate__animated animate__fadeInDown")) {
				$(".w-menu").addClass("fix_head animate__animated animate__fadeInDown");
			}
		} else {
			$(".w-menu").removeClass("fix_head animate__animated animate__fadeInDown");
		}
	});

	/* Mmenu */
	if (isExist($('nav#menu'))) {
		menuMobile({ sliding: false, search: false, lang: true });
	}
};

/* Tools */
NN_FRAMEWORK.Tools = function () {
	$(".toolbar-app .phone").click(function (e) {
		e.stopPropagation();
		$(".toolbar-app").toggleClass('is-active');
	});
	$(document).click(function () {
		$(".toolbar-app").removeClass('is-active');
	});
	var lastScrollTop = 0;
	$(window).scroll(function () {
		var ex6Exists = $('.ex6').length > 0;
		if ($(this).scrollTop() > 100) {
			if (!ex6Exists) {
				$('.toolbar-app .scrollToTopMobile').addClass('ex6');
			}
		} else {
			$('.toolbar-app .scrollToTopMobile').removeClass('ex6');
		}


		var scrollTop = $(this).scrollTop();
		if (scrollTop <= lastScrollTop && scrollTop < 70) {
			$("body").addClass("show-toolbar");
			$("body").removeClass("hidden-toolbar");
			$('#check-toolbar').prop('checked', true);
		}
		lastScrollTop = scrollTop;
	});
	$("body").addClass("show-toolbar");
	$("#check-toolbar").change(function (e) {
		if ($(this).prop('checked')) {
			$("body").addClass("show-toolbar");
			$("body").removeClass("hidden-toolbar");
		} else {
			$("body").removeClass("show-toolbar");
			$("body").addClass("hidden-toolbar");
		}
	});
};

/* Popup */
NN_FRAMEWORK.Popup = function () {
	if (isExist($('#popup'))) {
		$('#popup').modal('show');
	}
};

/* Wow */
NN_FRAMEWORK.Wows = function () {
	new WOW().init();
};

/* Pagings */
NN_FRAMEWORK.Pagings = function () {
	/* Products */
	if (isExist($('.paging-product'))) {
		loadPaging('api/product.php?perpage=4', '.paging-product');
	}

	/* Categories */
	if (isExist($('.paging-product-category'))) {
		$('.paging-product-category').each(function () {
			var list = $(this).data('list');
			loadPaging('api/product.php?perpage=12&idList=' + list, '.paging-product-category-' + list);
		});
	}
	if (isExist($('.show_padding'))) {
		$(".show_padding").each(function () {
			var list = $(this).data("list");
			var cat = $(this).data("cat");
			loadPaging("api/product.php?perpage=" + "12" + "&idList=" + list + "&idCat=" + cat, '.show_padding' + list);
		})
	}
	if (isExist($('.choose_list'))) {
		$(".choose_list span").click(function () {
			($(this).parents('.choose_list').find("span").hasClass('choosed')) ? $(this).parents('.choose_list').find("span").removeClass('choosed') : '';
			$(this).addClass('choosed');
			var list = $(this).attr("data-list");
			var cat = $(this).attr("data-cat");
			$(".show_padding" + list).attr("data-list", list);
			$(".show_padding" + list).attr("data-cat", cat);
			loadPaging("api/product.php?perpage=" + "4" + "&idList=" + list + "&idCat=" + cat, '.show_padding' + list);
			return false;
		})
	}
};

/* Ticker scroll 
NN_FRAMEWORK.TickerScroll = function () {
	if (isExist($('.news-scroll'))) {
		$('.news-scroll')
			.easyTicker({
				direction: 'up',
				easing: 'swing',
				speed: 'slow',
				interval: 3500,
				height: 'auto',
				visible: 3,
				mousePause: true,
				controls: {
					up: '.news-control#up',
					down: '.news-control#down'
					// toggle: '.toggle',
					// stopText: 'Stop'
				},
				callbacks: {
					before: function (ul, li) {
						// $(li).css('color', 'red');
					},
					after: function (ul, li) {}
				}
			})
			.data('easyTicker');
	}
};
*/

/* Photobox */
NN_FRAMEWORK.Photobox = function () {
	if (isExist($('.album-gallery'))) {
		$('.album-gallery').photobox('a', { thumbs: true, loop: false });
	}
};

/* Comment */
NN_FRAMEWORK.Comment = function () {
	if (isExist($('.comment-page'))) {
		$('.comment-page').comments({
			url: 'api/comment.php'
		});
	}
};

/* DatePicker */
NN_FRAMEWORK.DatePicker = function () {
	if (isExist($('#birthday'))) {
		$('#birthday').datetimepicker({
			timepicker: false,
			format: 'd/m/Y',
			formatDate: 'd/m/Y',
			minDate: '01/01/1950',
			maxDate: TIMENOW
		});
	}
};

/* Search */
NN_FRAMEWORK.Search = function () {
	if (isExist($(".icon-search"))) {
		$(".icon-search").click(function () {
			if ($(this).hasClass("active")) {
				$(this).removeClass("active");
				$(".search-grid").stop(true, true).animate({ opacity: "0", width: "0px" }, 200);
			} else {
				$(this).addClass("active");
				$(".search-grid").stop(true, true).animate({ opacity: "1", width: "230px" }, 200);
			}
			document.getElementById($(this).next().find("input").attr("id")).focus();
			$(".icon-search i").toggleClass("fa-search fa-xmark");
		});
	}

	/* Header Cafune search overlay */
	if (isExist($('.hd-open-search'))) {
		var recognition = null;
		var voiceButton = $('.hd-search-voice');
		var voiceStatus = $('#header-voice-status');
		var cancelVoice = function () {
			var activeRecognition = recognition;
			recognition = null;
			if (activeRecognition) activeRecognition.abort();
			voiceButton.attr('aria-pressed', 'false');
			voiceStatus.text('');
		};
		voiceButton.on('click', function () {
			if (recognition) {
				cancelVoice();
				return;
			}
			var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
			if (!SpeechRecognition || !window.isSecureContext) {
				voiceStatus.text(!window.isSecureContext ? 'Vui lòng mở website bằng HTTPS để sử dụng micro.' : 'Trình duyệt chưa hỗ trợ nhận diện giọng nói. Bạn vẫn có thể nhập từ khóa.');
				return;
			}
			try {
				var session = new SpeechRecognition();
				recognition = session;
				session.lang = 'vi-VN';
				session.continuous = false;
				session.interimResults = false;
				var hasResult = false;
				var hasError = false;
				session.onstart = function () {
					if (recognition === session) voiceStatus.text('Đang nghe… Hãy nói từ khóa tiếng Việt. Bấm micro lần nữa để hủy.');
				};
				session.onresult = function (event) {
					if (recognition !== session) return;
					var transcript = event.results[0][0].transcript.trim();
					if (!transcript) return;
					hasResult = true;
					$('#keyword-header').val(transcript).trigger('input').trigger('focus');
					voiceStatus.text('Đã nhận từ khóa. Bạn có thể chỉnh sửa rồi nhấn Enter hoặc nút tìm kiếm.');
				};
				session.onerror = function (event) {
					if (recognition !== session) return;
					hasError = true;
					var messages = {
						'not-allowed': 'Vui lòng cho phép truy cập micro trong cài đặt trình duyệt rồi thử lại.',
						'service-not-allowed': 'Dịch vụ nhận diện giọng nói đang bị chặn. Vui lòng nhập từ khóa.',
						'audio-capture': 'Không tìm thấy micro khả dụng. Vui lòng kiểm tra thiết bị.',
						'no-speech': 'Chưa nghe rõ giọng nói. Bấm micro để thử lại.',
						'network': 'Không kết nối được dịch vụ nhận diện. Vui lòng kiểm tra mạng.'
					};
					voiceStatus.text(messages[event.error] || 'Không thể nhận diện giọng nói. Vui lòng thử lại hoặc nhập từ khóa.');
				};
				session.onend = function () {
					if (recognition !== session) return;
					recognition = null;
					voiceButton.attr('aria-pressed', 'false');
					if (!hasResult && !hasError) voiceStatus.text('Chưa nhận được từ khóa. Bấm micro để thử lại.');
				};
				voiceButton.attr('aria-pressed', 'true');
				voiceStatus.text('Đang bật micro… Vui lòng cho phép truy cập nếu trình duyệt yêu cầu.');
				session.start();
			} catch (error) {
				cancelVoice();
				voiceStatus.text('Không thể bật micro. Vui lòng thử lại hoặc nhập từ khóa.');
			}
		});
		$(window).on('pagehide', cancelVoice);
		$(document).on('click', '.hd-open-search', function () {
			$('.hd-search-wrap').addClass('active');
			$('body').addClass('no-scroll');
			setTimeout(function () {
				$('#keyword-header').trigger('focus');
			}, 120);
		});
		$(document).on('click', '.hd-search-close', function () {
			cancelVoice();
			$('.hd-search-wrap').removeClass('active');
			$('body').removeClass('no-scroll');
		});
		$(document).on('keyup', function (e) {
			if (e.key === 'Escape') {
				cancelVoice();
				$('.hd-search-wrap').removeClass('active');
				$('body').removeClass('no-scroll');
			}
		});
	}

	/* Header sticky */
	if (isExist($('header.hd'))) {
		var $hd = $('header.hd');
		var onScrollHd = function () {
			if ($(window).scrollTop() > 10) $hd.addClass('sticky');
			else $hd.removeClass('sticky');
		};
		onScrollHd();
		$(window).on('scroll', onScrollHd);
	}
};

/* Videos */
NN_FRAMEWORK.Videos = function () {
	if (typeof Fancybox !== "undefined" && document.querySelector("[data-fancybox]")) {
		Fancybox.bind("[data-fancybox]", {});
	}
};

/* Owl Data */
NN_FRAMEWORK.OwlData = function (obj) {
	if (!isExist(obj)) return false;
	var items = obj.attr('data-items');
	var rewind = Number(obj.attr('data-rewind')) ? true : false;
	var autoplay = Number(obj.attr('data-autoplay')) ? true : false;
	var loop = Number(obj.attr('data-loop')) ? true : false;
	var lazyLoad = Number(obj.attr('data-lazyload')) ? true : false;
	var mouseDrag = Number(obj.attr('data-mousedrag')) ? true : false;
	var touchDrag = Number(obj.attr('data-touchdrag')) ? true : false;
	var animations = obj.attr('data-animations') || false;
	var smartSpeed = Number(obj.attr('data-smartspeed')) || 800;
	var autoplaySpeed = Number(obj.attr('data-autoplayspeed')) || 800;
	var autoplayTimeout = Number(obj.attr('data-autoplaytimeout')) || 5000;
	var dots = Number(obj.attr('data-dots')) ? true : false;
	var responsive = {};
	var responsiveClass = true;
	var responsiveRefreshRate = 200;
	var nav = Number(obj.attr('data-nav')) ? true : false;
	var navContainer = obj.attr('data-navcontainer') || false;
	var navTextTemp =
		"<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>";
	var navText = obj.attr('data-navtext');
	navText = nav && navContainer && (((navText === undefined || Number(navText)) && navTextTemp) || (isNaN(Number(navText)) && navText) || (Number(navText) === 0 && false));

	if (items) {
		items = items.split(',');

		if (items.length) {
			var itemsCount = items.length;

			for (var i = 0; i < itemsCount; i++) {
				var options = items[i].split('|'),
					optionsCount = options.length,
					responsiveKey;

				for (var j = 0; j < optionsCount; j++) {
					const attr = options[j].indexOf(':') ? options[j].split(':') : options[j];

					if (attr[0] === 'screen') {
						responsiveKey = Number(attr[1]);
					} else if (Number(responsiveKey) >= 0) {
						responsive[responsiveKey] = {
							...responsive[responsiveKey],
							[attr[0]]: (isNumeric(attr[1]) && Number(attr[1])) ?? attr[1]
						};
					}
				}
			}
		}
	}

	if (nav && navText) {
		navText = navText.indexOf('|') > 0 ? navText.split('|') : navText.split(':');
		navText = [navText[0], navText[1]];
	}

	obj.owlCarousel({
		rewind,
		autoplay,
		loop,
		lazyLoad,
		mouseDrag,
		touchDrag,
		smartSpeed,
		autoplaySpeed,
		autoplayTimeout,
		dots,
		nav,
		navText,
		navContainer: nav && navText && navContainer,
		responsiveClass,
		responsiveRefreshRate,
		responsive
	});

	if (autoplay) {
		obj.on('translate.owl.carousel', function (event) {
			obj.trigger('stop.owl.autoplay');
		});

		obj.on('translated.owl.carousel', function (event) {
			obj.trigger('play.owl.autoplay', [autoplayTimeout]);
		});
	}

	if (animations && isExist(obj.find('[owl-item-animation]'))) {
		var animation_now = '';
		var animation_count = 0;
		var animations_excuted = [];
		var animations_list = animations.indexOf(',') ? animations.split(',') : animations;

		obj.on('changed.owl.carousel', function (event) {
			$(this).find('.owl-item.active').find('[owl-item-animation]').removeClass(animation_now);
		});

		obj.on('translate.owl.carousel', function (event) {
			var item = event.item.index;

			if (Array.isArray(animations_list)) {
				var animation_trim = animations_list[animation_count].trim();

				if (!animations_excuted.includes(animation_trim)) {
					animation_now = 'animate__animated ' + animation_trim;
					animations_excuted.push(animation_trim);
					animation_count++;
				}

				if (animations_excuted.length == animations_list.length) {
					animation_count = 0;
					animations_excuted = [];
				}
			} else {
				animation_now = 'animate__animated ' + animations_list.trim();
			}
			$(this).find('.owl-item').eq(item).find('[owl-item-animation]').addClass(animation_now);
		});
	}
};

/* Owl Page */
NN_FRAMEWORK.OwlPage = function () {
	if (isExist($('.owl-page'))) {
		$('.owl-page').each(function () {
			NN_FRAMEWORK.OwlData($(this));
		});
	}
};

/* Dom Change */
NN_FRAMEWORK.DomChange = function () {
	if (isExist($('#video-fotorama'))) {
		const observer = new MutationObserver(function (mutations) {
			$('#fotorama-videos').fotorama();
			observer.disconnect();
		});
		const config = { childList: true, subtree: true };
		observer.observe(document.getElementById('video-fotorama'), config);
	}

	$('#video-select').on('change', '.listvideos', function () {
		var id = $(this).val();
		$.ajax({
			url: 'api/video.php',
			type: 'POST',
			dataType: 'html',
			data: { id: id },
			beforeSend: function () {
				holdonOpen();
			},
			success: function (result) {
				$('.video-main').html(result);
				holdonClose();
			}
		});
	});

	/* Chat Facebook */
	$(document).on('click', '.js-facebook-messenger-box', function () {
		$('.js-facebook-messenger-box, .js-facebook-messenger-container').toggleClass('open');
		if ($('.js-facebook-messenger-tooltip').length) {
			$('.js-facebook-messenger-tooltip').toggle();
		}
	});

	if ($('.js-facebook-messenger-box').hasClass('cfm')) {
		setTimeout(function () {
			$('.js-facebook-messenger-box').addClass('rubberBand animated');
		}, 3500);
	}

	$(document).on('click', '.search_open', function () {
		$('.search_box_hide').toggleClass('opening');
	});

	if ($('.js-facebook-messenger-tooltip').length) {
		if ($('.js-facebook-messenger-tooltip').hasClass('fixed')) {
			$('.js-facebook-messenger-tooltip').show();
		} else {
			$(document).on('mouseenter', '.js-facebook-messenger-box', function () {
				$('.js-facebook-messenger-tooltip').show();
			});
		}

		$(document).on('click', '.js-facebook-messenger-close-tooltip', function () {
			$('.js-facebook-messenger-tooltip').addClass('closed');
		});
	}
};

/* Quick View */
NN_FRAMEWORK.QuickView = function (obj) {
	$("body").on("click", ".product-quick-view", function () {
		var slug = $(this).attr("data-slug");

		if (slug) {
			$.ajax({
				type: "POST",
				url: slug + "?quickview=1",
				dataType: "html",
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					holdonClose();
					$("#popup-quickview").find(".modal-body").html(result);
					$("#popup-quickview").modal("show");
					// MagicZoom.refresh("Zoom-quickview");
					// NN_FRAMEWORK.OwlData($('.owl-pro-detail'));
					MagicZoom.refresh("Zoom-1");
					NN_FRAMEWORK.OwlData($(".owl-pro-detail"));
					NN_FRAMEWORK.Lazys();
				},
			});
		}
	});
};

/* Cart */
NN_FRAMEWORK.Cart = function () {
	function setMiniCartOpen(open) {
		$('.side-cmini, .side-cmini-overlay').toggleClass('open', open);
		$('.side-cmini').attr('aria-hidden', open ? 'false' : 'true');
		$('.side-open').attr('aria-expanded', open ? 'true' : 'false');
		$('body').toggleClass('mini-cart-open', open);
	}

	function loadMiniCart(openAfterLoad) {
		$.ajax({
			url: CONFIG_BASE + 'api/cart.php',
			type: 'POST',
			dataType: 'html',
			data: { cmd: 'mini-cart' },
			success: function (result) {
				$('.mini-cart-content').html(result);
				NN_FRAMEWORK.Lazys();
				if (openAfterLoad) setMiniCartOpen(true);
			}
		});
	}

	$('body').on('click', '.side-open', function () { loadMiniCart(true); });
	$('body').on('click', '.side-cmini-close, .side-cmini-overlay', function () { setMiniCartOpen(false); });
	$(document).on('keydown', function (event) { if (event.key === 'Escape') setMiniCartOpen(false); });

	/* Add */
	if (isExist($('.select-city-cart'))) {
		fetch(CONFIG_BASE + "assets/jsons/city-group.json", { headers: { "Content-Type": "application/json" } }).then(response => {
			return response.json();
		}).then(function (data) {
			$.each(data.citysCentral, function (index, val) {
				$('.select-city-cart').append(`<option value="` + val.id + `">` + val.name + `</option>`);
			});
		});
	}
	$('body').on('click', '.addcart', function () {
		$this = $(this);
		$parents = $this.parents('.right-pro-detail');
		var id = $this.data('id');
		var action = $this.data('action');
		var quantity = $parents.find('.quantity-pro-detail').find('.qty-pro').val();
		quantity = quantity ? quantity : 1;
		var color = $parents.find('.color-block-pro-detail').find('.color-pro-detail input:checked').val();
		color = color ? color : 0;
		var size = $parents.find('.size-block-pro-detail').find('.size-pro-detail input:checked').val();
		size = size ? size : 0;

		if (id) {
			$.ajax({
				url: 'api/cart.php',
				type: 'POST',
				dataType: 'json',
				async: true,
				data: {
					cmd: 'add-cart',
					id: id,
					color: color,
					size: size,
					quantity: quantity
				},
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					if (action == 'addnow') {
						$('.count-cart').html(result.max);
						$('#popup-quickview').modal('hide');
						loadMiniCart(true);
						holdonClose();
					} else if (action == 'buynow') {
						window.location = CONFIG_BASE + 'gio-hang';
					}
				}
			});
		}
	});

	$('body').on('click', '.cmini-delete', function () {
		var code = $(this).data('code');
		$.post(CONFIG_BASE + 'api/cart.php', { cmd: 'delete-cart', code: code }, function (result) {
			$('.count-cart').html(result.max);
			loadMiniCart(false);
		}, 'json');
	});

	$('body').on('click', '.mini-cart-minus, .mini-cart-plus', function () {
		var item = $(this).closest('.cmini-item');
		var quantity = parseInt(item.find('.count-number').text(), 10) || 1;
		quantity += $(this).hasClass('mini-cart-plus') ? 1 : -1;
		quantity = Math.max(1, quantity);
		$.post(CONFIG_BASE + 'api/cart.php', { cmd: 'update-cart', id: item.data('pid'), code: item.data('code'), quantity: quantity }, function () {
			loadMiniCart(false);
		}, 'json');
	});

	/* Delete */
	$('body').on('click', '.del-procart', function () {
		confirmDialog('delete-procart', LANG['delete_product_from_cart'], $(this));
	});

	/* Counter */
	$('body').on('click', '.counter-procart', function () {
		var $button = $(this);
		var quantity = 1;
		var input = $button.parent().find('input');
		var id = input.data('pid');
		var code = input.data('code');
		var oldValue = $button.parent().find('input').val();
		if ($button.text() == '+') quantity = parseFloat(oldValue) + 1;
		else if (oldValue > 1) quantity = parseFloat(oldValue) - 1;
		$button.parent().find('input').val(quantity);
		updateCart(id, code, quantity);
	});

	/* Quantity */
	$('body').on('change', 'input.quantity-procart', function () {
		var quantity = $(this).val() < 1 ? 1 : $(this).val();
		$(this).val(quantity);
		var id = $(this).data('pid');
		var code = $(this).data('code');
		updateCart(id, code, quantity);
	});

	/* City */
	if (isExist($('.select-city-cart'))) {
		$('.select-city-cart').change(function () {
			var id = $(this).val();
			loadDistrict(id);
			loadShip();
		});
	}

	/* District */
	if (isExist($('.select-district-cart'))) {
		$('.select-district-cart').change(function () {
			var id = $(this).val();
			var city = $('.select-city-cart').val();
			loadWard(city, id);
			loadShip();
		});
	}

	/* Ward */
	if (isExist($('.select-ward-cart'))) {
		$('.select-ward-cart').change(function () {
			var id = $(this).val();
			loadShip(id);
		});
	}

	/* Payments */
	if (isExist($('.payments-cart'))) {
		$('body').on('change', '.payments-cart input', function () {
			var payments = $(this).data('payments');
			$('.payments-cart .payments-label, .payments-info').removeClass('active');
			$(this).addClass('active');
			$('.payments-info-' + payments).addClass('active');
		});
	}

	/* Colors */
	$('body').on('click', '.color-pro-detail input', function (event) {
		$this = $(this).parents('label.color-pro-detail');
		$parents = $this.parents('.attr-pro-detail');
		$parents_detail = $this.parents('.grid-pro-detail');
		$parents.find('.color-block-pro-detail').find('.color-pro-detail').removeClass('active');
		$parents.find('.color-block-pro-detail').find('.color-pro-detail input').prop('checked', false);
		$this.addClass('active');
		$this.find('input').prop('checked', true);
		var id_color = $parents.find('.color-block-pro-detail').find('.color-pro-detail input:checked').val();
		var id_pro = $this.data('idproduct');

		$.ajax({
			url: 'api/color.php',
			type: 'POST',
			dataType: 'html',
			data: {
				id_color: id_color,
				id_pro: id_pro
			},
			beforeSend: function () {
				holdonOpen();
			},
			success: function (result) {
				if (result) {
					$parents_detail.find('.left-pro-detail').html(result);
					MagicZoom.refresh('Zoom-1');
					NN_FRAMEWORK.OwlData($('.owl-pro-detail'));
					NN_FRAMEWORK.Lazys();
				}
				holdonClose();
			}
		});
	});

	/* Sizes */
	$('body').on('click', '.size-pro-detail input', function (event) {
		$this = $(this).parent();
		$parents = $this.parents('.attr-pro-detail');
		console.log($(this));
		$parents.find('.size-block-pro-detail').find('.size-pro-detail').removeClass('active');
		$parents.find('.size-block-pro-detail').find('.size-pro-detail input').prop('checked', false);
		$this.addClass('active');
		$this.find('input').prop('checked', true);
	});

	/* Quantity detail page */
	$('body').on('click', '.quantity-pro-detail span', function () {
		var $button = $(this);
		var oldValue = $button.parent().find('input').val();
		if ($button.text() == '+') {
			var newVal = parseFloat(oldValue) + 1;
		} else {
			if (oldValue > 1) var newVal = parseFloat(oldValue) - 1;
			else var newVal = 1;
		}
		$button.parent().find('input').val(newVal);
	});
};

/* Slick */
NN_FRAMEWORK.SlickPage = function () {
	if (isExist($("[data-flash-sale-slider]"))) {
		$("[data-flash-sale-slider]").not(".slick-initialized").each(function () {
			var $slider = $(this);
			var count = $slider.children("[data-flash-sale-item]").length;
			if (count <= 3) return;

			$slider.slick({
				dots: false,
				arrows: false,
				infinite: true,
				autoplay: true,
				autoplaySpeed: 3500,
				speed: 500,
				pauseOnHover: true,
				swipeToSlide: true,
				slidesToShow: 3,
				slidesToScroll: 1,
				responsive: [
					{ breakpoint: 1100, settings: { slidesToShow: 2 } },
					{ breakpoint: 700, settings: { slidesToShow: 1 } }
				]
			});
		});
	}
	if (isExist($(".newsnb-slick"))) {
		$(".newsnb-slick").not(".slick-initialized").slick({
			dots: false,
			arrows: false,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 3500,
			speed: 500,
			pauseOnHover: true,
			swipeToSlide: true,
			slidesToShow: 3,
			slidesToScroll: 1,
			responsive: [
				{ breakpoint: 992, settings: { slidesToShow: 2 } },
				{ breakpoint: 576, settings: { slidesToShow: 1 } }
			]
		});
	}
	if (isExist($(".row-news-photos")) && window.innerWidth < 768) {
		$(".row-news-photos").slick({
			dots: false,
			infinite: true,
			autoplaySpeed: 3000,
			slidesToShow: 1,
			slidesToScroll: 1,
		});
	}
	if (isExist($(".duan-grid")) && window.innerWidth < 768) {
		$(".duan-grid").slick({
			dots: false,
			infinite: true,
			autoplaySpeed: 3000,
			slidesToShow: 1,
			slidesToScroll: 1,
		});
	}
	if (isExist($(".by-partner-grid")) && window.innerWidth < 768) {
		$(".by-partner-grid").slick({
			dots: false,
			infinite: true,
			autoplaySpeed: 3000,
			slidesToShow: 3,
			slidesToScroll: 1,
		});
	}
	if (isExist($(".hbs-slider"))) {
		$(".hbs-slider").not(".slick-initialized").each(function () {
			var $categorySlider = $(this);
			var categoryCount = $categorySlider.children().length;
			var shouldRun = categoryCount > 4;
			var showMobileDots = categoryCount > 2;

			$categorySlider.slick({
				dots: false,
				arrows: false,
				infinite: shouldRun,
				autoplay: true,
				autoplaySpeed: 3000,
				pauseOnHover: true,
				speed: 500,
				slidesToShow: 4,
				slidesToScroll: 1,
				swipeToSlide: true,
				responsive: [
					{ breakpoint: 1025, settings: { slidesToShow: 3 } },
					{
						breakpoint: 769,
						settings: {
							slidesToShow: 1.54,
							dots: showMobileDots
						}
					}
				]
			});
		});
	}
	if (isExist($(".slick-slideshow"))) {
		var $slideshow = $(".slick-slideshow");
		var reduceSlideMotion = window.matchMedia &&
			window.matchMedia("(prefers-reduced-motion: reduce)").matches;

		function syncSlideshowMedia(slick, currentSlide) {
			var $slides = $(slick.$slides);
			var $currentSlide = $slides.eq(currentSlide);
			var currentVideo = $currentSlide.find(".slideshow-video").get(0);

			$slideshow.find(".slideshow-video").each(function () {
				if (this !== currentVideo) {
					this.pause();
					this.currentTime = 0;
				}
			});

			if (currentVideo) {
				slick.slickPause();
				if (!reduceSlideMotion) {
					currentVideo.currentTime = 0;
					var playPromise = currentVideo.play();
					if (playPromise && typeof playPromise.catch === "function") {
						playPromise.catch(function () { });
					}
				}
			} else if (!reduceSlideMotion && slick.slideCount > 1) {
				slick.slickPlay();
			}
		}

		$slideshow
			.on("init", function (event, slick) {
				window.requestAnimationFrame(function () {
					syncSlideshowMedia(slick, slick.currentSlide || 0);
				});
			})
			.on("beforeChange", function () {
				$slideshow.find(".slideshow-video").each(function () {
					this.pause();
				});
			})
			.on("afterChange", function (event, slick, currentSlide) {
				syncSlideshowMedia(slick, currentSlide);
			});

		$slideshow.find(".slideshow-video").on("ended", function () {
			var slick = $slideshow.slick("getSlick");
			if (!$(this).closest(".slick-slide").hasClass("slick-current")) return;

			if (slick.slideCount > 1) {
				$slideshow.slick("slickNext");
			} else if (!reduceSlideMotion) {
				this.currentTime = 0;
				this.play().catch(function () { });
			}
		});

		$slideshow.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: !reduceSlideMotion,
			autoplaySpeed: 5000,
			dots: true,
			speed: reduceSlideMotion ? 0 : 650,
			infinite: $slideshow.children().length > 1,
			arrows: false,
			draggable: true,
			swipe: true,
			pauseOnHover: false,
			cssEase: "ease-in-out"
		});

		$slideshow.find(".bnh-blog").each(function () {
			var $blogSlider = $(this);
			if ($blogSlider.children(".blog-it").length <= 1 || $blogSlider.hasClass("slick-initialized")) return;

			$blogSlider.slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				vertical: true,
				verticalSwiping: true,
				infinite: true,
				autoplay: !reduceSlideMotion,
				autoplaySpeed: 3000,
				speed: reduceSlideMotion ? 0 : 500,
				dots: false,
				arrows: false,
				pauseOnHover: true
			});
		});
	}

	if (isExist($(".slide-text"))) {
		$(".slide-text").slick({
			dots: true,
			infinite: true,
			autoplaySpeed: 3000,
			slidesToShow: 1,
			slidesToScroll: 1,
			adaptiveHeight: true,
			autoplay: true,
			arrows: true,
			fade: true,
		});
	}
	if (isExist($(".slick-v-3"))) {
		$(".slick-v-3").slick({
			dots: false,
			infinite: true,
			autoplaySpeed: 3000,
			slidesToShow: 3,
			slidesToScroll: 1,
			adaptiveHeight: true,
			vertical: true,
			verticalSwiping: true,
			autoplay: true,
			infinite: true,
			arrows: false,
		});
	}
	if (isExist($(".about-projects-slider"))) {
		$(".about-projects-slider").not(".slick-initialized").slick({
			dots: true,
			arrows: false,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 3500,
			speed: 500,
			slidesToShow: 3,
			slidesToScroll: 1,
			responsive: [
				{
					breakpoint: 992,
					settings: { slidesToShow: 2 }
				},
				{
					breakpoint: 576,
					settings: { slidesToShow: 1 }
				}
			]
		});
	}
	if (isExist($(".by-partner-slick"))) {
		$(".by-partner-slick").not(".slick-initialized").slick({
			dots: false,
			arrows: true,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 3500,
			speed: 500,
			slidesToShow: 6,
			slidesToScroll: 1,
			responsive: [
				{
					breakpoint: 992,
					settings: { slidesToShow: 4 }
				},
				{
					breakpoint: 576,
					settings: { slidesToShow: 3 }
				}
			]
		});
	}
};

/* Aos */
NN_FRAMEWORK.AosAnimation = function () {
	var startAos = function () {
		if (typeof AOS !== "undefined") AOS.init({ once: true });
	};
	if ("requestIdleCallback" in window) {
		window.requestIdleCallback(startAos, { timeout: 1500 });
	} else {
		window.setTimeout(startAos, 300);
	}
};

/* TOC */
NN_FRAMEWORK.Toc = function () {
	if (isExist($(".toc-list"))) {
		$(".toc-list").toc({
			content: "div#toc-content",
			headings: "h2,h3,h4"
		});

		if (!$(".toc-list li").length) $(".meta-toc").hide();
		if (!$(".toc-list li").length) $(".meta-toc .mucluc-dropdown-list_button").hide();

		$('.toc-list').find('a').click(function () {
			var x = $(this).attr('data-rel');
			goToByScroll(x);
		});

		$("body").on("click", ".mucluc-dropdown-list_button", function () {
			$(".box-readmore").slideToggle(200);
		});

		$(document).scroll(function () {
			var y = $(this).scrollTop();
			if (y > 300) {
				$('.meta-toc').addClass('fiedx');
			} else {
				$('.meta-toc').removeClass('fiedx');
			}
		});
	}
};

NN_FRAMEWORK.LoaderWrapper = function () {
	if (isExist($("#loader-wrapper"))) {
		setTimeout(function () {
			$("#loader-wrapper").addClass('show1');
		}, 1500);
		setTimeout(function () {
			$('#loader-wrapper').remove();
		}, 3000);
	}
};

NN_FRAMEWORK.Homes = function () {
	if (isExist($(".list-hot"))) {
		//FirstLoadAPI(".list-hot a:first", "api/load_ajax_product.php", ".load_ajax_product");
		//LoadAPI(".list-hot a", "api/load_ajax_product.php", ".load_ajax_product");
	}

	if (isExist($(".cats-bar-icon"))) {
		$("body").on("click", ".cats-bar-icon", function () {
			$this = $(this);
			$this.toggleClass("active not-active");
			var isActive = $this.hasClass("active");
			$(".cats-owl").animate({
				opacity: +isActive,
				visibility: isActive ? "visible" : "hidden",
			}, 1000, function () { });
		});
	}
	if (isExist($(".content-text"))) {
		$(".content-text table").each(function (i, val) {
			$(this).addClass("table table-bordered");
		});
	}

};
NN_FRAMEWORK.aweOwlPage = function () {
	var owl = $('.owl-carousel.in-page');
	owl.each(function () {
		var xs_item = $(this).attr('data-xs-items');
		var md_item = $(this).attr('data-md-items');
		var lg_item = $(this).attr('data-lg-items');
		var sm_item = $(this).attr('data-sm-items');
		var margin = $(this).attr('data-margin');
		var dot = $(this).attr('data-dot');
		var nav = $(this).attr('data-nav');
		var height = $(this).attr('data-height');
		var play = $(this).attr('data-play');
		var loop = $(this).attr('data-loop');

		if (typeof margin !== typeof undefined && margin !== false) {
		} else {
			margin = 30;
		}
		if (typeof xs_item !== typeof undefined && xs_item !== false) {
		} else {
			xs_item = 1;
		}
		if (typeof sm_item !== typeof undefined && sm_item !== false) {

		} else {
			sm_item = 3;
		}
		if (typeof md_item !== typeof undefined && md_item !== false) {
		} else {
			md_item = 3;
		}
		if (typeof lg_item !== typeof undefined && lg_item !== false) {
		} else {
			lg_item = 3;
		}

		if (loop == 1) { loop = true; } else { loop = false; }
		if (dot == 1) { dot = true; } else { dot = false; }
		if (nav == 1) { nav = true; } else { nav = false; }
		if (play == 1) { play = true; } else { play = false; }

		$(this).owlCarousel({
			loop: loop,
			margin: Number(margin),
			responsiveClass: true,
			dots: dot,
			nav: nav,
			navText: ['<div class="owlleft"><svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;"><polyline class="a" points="11040,1920 4960,8000 11040,14080 "></polyline></svg></div>', '<div class="owlright"><svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;"><polyline class="a" points="4960,1920 11040,8000 4960,14080 "></polyline></svg></div>'],
			autoplay: play,
			autoplayTimeout: 4000,
			smartSpeed: 3000,
			autoplayHoverPause: true,
			autoHeight: false,
			responsive: {
				0: {
					items: Number(xs_item)
				},
				600: {
					items: Number(sm_item)
				},
				1000: {
					items: Number(md_item)
				},
				1200: {
					items: Number(lg_item)
				}
			}
		})
	});
};

NN_FRAMEWORK.slickPage = function () {
	if (isExist($(".slick.in-page"))) {
		$('.slick.in-page').each(function () {
			var dots = $(this).attr('data-dots');
			var infinite = $(this).attr('data-infinite');
			var speed = $(this).attr('data-speed');
			var vertical = $(this).attr('data-vertical');
			var verticalSwiping = false;
			var arrows = $(this).attr('data-arrows');
			var autoplay = $(this).attr('data-autoplay');
			var autoplaySpeed = $(this).attr('data-autoplaySpeed');
			var centerMode = $(this).attr('data-centerMode');
			var centerPadding = $(this).attr('data-centerPadding');
			var slidesDefault = $(this).attr('data-slidesDefault');
			var responsive = $(this).attr('data-responsive');
			var xs_item = $(this).attr('data-xs-items');
			var md_item = $(this).attr('data-md-items');
			var lg_item = $(this).attr('data-lg-items');
			var sm_item = $(this).attr('data-sm-items');
			var slidesDefault_ar = slidesDefault.split(":");
			var xs_item_ar = xs_item.split(":");
			var sm_item_ar = sm_item.split(":");
			var md_item_ar = md_item.split(":");
			var lg_item_ar = lg_item.split(":");
			var to_show = slidesDefault_ar[0];
			var to_scroll = slidesDefault_ar[1];
			if (responsive == 1) { responsive = true; } else { responsive = false; }
			if (dots == 1) { dots = true; } else { dots = false; }
			if (arrows == 1) { arrows = true; } else { arrows = false; }
			if (infinite == 1) { infinite = true; } else { infinite = false; }
			if (autoplay == 1) { autoplay = true; } else { autoplay = false; }
			if (centerMode == 1) { centerMode = true; } else { centerMode = false; }
			if (vertical == 1) { vertical = true; verticalSwiping: true; } else { vertical = false; verticalSwiping: false; }
			if (typeof speed !== typeof undefined && speed !== false) {
			} else { speed = 300; }
			if (typeof autoplaySpeed !== typeof undefined && autoplaySpeed !== false) {
			} else { autoplaySpeed = 2000; }
			if (typeof centerPadding !== typeof undefined && centerPadding !== false) {
			} else { centerPadding = "0px"; }
			var reponsive_json = [{
				breakpoint: 1024,
				settings: {
					slidesToShow: Number(lg_item_ar[0]),
					slidesToScroll: Number(lg_item_ar[1])
				}
			}, {
				breakpoint: 992,
				settings: {
					slidesToShow: Number(md_item_ar[0]),
					slidesToScroll: Number(md_item_ar[1])
				}
			}, {
				breakpoint: 768,
				settings: {
					slidesToShow: Number(sm_item_ar[0]),
					slidesToScroll: Number(sm_item_ar[1]),
					vertical: false
				}
			}, {
				breakpoint: 480,
				settings: {
					slidesToShow: Number(xs_item_ar[0]),
					slidesToScroll: Number(xs_item_ar[1]),
					vertical: false
				}
			}];
			if (responsive == 1) {
				$(this).slick({
					dots: dots,
					infinite: infinite,
					arrows: arrows,
					speed: Number(speed),
					vertical: vertical,
					verticalSwiping: verticalSwiping,
					slidesToShow: Number(to_show),
					slidesToScroll: Number(to_scroll),
					autoplay: autoplay,
					autoplaySpeed: Number(autoplaySpeed),
					responsive: reponsive_json
				});
			} else {
				$(this).slick({
					dots: dots,
					infinite: infinite,
					arrows: arrows,
					speed: Number(speed),
					vertical: vertical,
					verticalSwiping: verticalSwiping,
					slidesToShow: Number(to_show),
					slidesToScroll: Number(to_scroll),
					autoplay: autoplay,
					autoplaySpeed: Number(autoplaySpeed)
				});
			}
		});
	}
};
NN_FRAMEWORK.Custome = function () {
	$(".profile-name").click(function (e) {
		$(".profile-list-options").slideToggle("slow");
	});
	$('.profile').click(function (e) {
		e.stopPropagation();
	});
	$(document).click(function () {
		$('.profile-list-options').slideUp();
	});
};
NN_FRAMEWORK.AchievementCounter = function () {
	var sections = document.querySelectorAll('[data-achievements]');
	if (!sections.length) return;

	var formatter = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function runCounter(counter) {
		if (counter.getAttribute('data-counted') === 'true') return;
		counter.setAttribute('data-counted', 'true');

		var target = Number(counter.getAttribute('data-target')) || 0;
		if (reduceMotion || target <= 0) {
			counter.textContent = formatter.format(target);
			return;
		}

		var duration = 1800;
		var startTime = null;

		function updateCounter(timestamp) {
			if (!startTime) startTime = timestamp;
			var progress = Math.min((timestamp - startTime) / duration, 1);
			var easedProgress = 1 - Math.pow(1 - progress, 3);
			counter.textContent = formatter.format(Math.round(target * easedProgress));

			if (progress < 1) {
				window.requestAnimationFrame(updateCounter);
			} else {
				counter.textContent = formatter.format(target);
			}
		}

		window.requestAnimationFrame(updateCounter);
	}

	function runSection(section) {
		section.querySelectorAll('[data-achievement-counter]').forEach(runCounter);
	}

	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					runSection(entry.target);
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.25 });

		sections.forEach(function (section) {
			observer.observe(section);
		});
	} else {
		sections.forEach(runSection);
	}
};

/* Accessibility fixes for third-party sliders and legacy CMS content */
NN_FRAMEWORK.Accessibility = function () {
	$(".slick-track[role='listbox']").each(function (index) {
		if (!this.getAttribute("aria-label")) {
			this.setAttribute("aria-label", "Nội dung trình chiếu " + (index + 1));
		}
	});

	$(".slick-slide[role='option']").each(function (index) {
		if (!this.getAttribute("aria-label")) {
			var label = $(this).find("img[alt]").first().attr("alt") ||
				$(this).find("[title]").first().attr("title") ||
				"Nội dung " + (index + 1);
			this.setAttribute("aria-label", label);
		}
	});

	$("a").each(function () {
		var text = $.trim($(this).text());
		var hasAccessibleName = text || this.getAttribute("aria-label") || this.getAttribute("title");
		if (hasAccessibleName) return;

		var imageAlt = $(this).find("img[alt]").first().attr("alt");
		var href = this.getAttribute("href") || "";
		var fallback = imageAlt || href.replace(/^.*\//, "").replace(/[-_]+/g, " ") || "Liên kết";
		if (!href) this.setAttribute("role", "button");
		this.setAttribute("aria-label", fallback);
	});
};

/* Ready */
$(document).ready(function () {
	NN_FRAMEWORK.SmoothScroll();
	NN_FRAMEWORK.Common();
	NN_FRAMEWORK.Homes();
	NN_FRAMEWORK.LoaderWrapper();
	NN_FRAMEWORK.SlickPage();
	NN_FRAMEWORK.AosAnimation();
	NN_FRAMEWORK.Lazys();
	NN_FRAMEWORK.Tools();
	NN_FRAMEWORK.Popup();
	NN_FRAMEWORK.Wows();
	NN_FRAMEWORK.AltImg();
	NN_FRAMEWORK.GoTop();
	NN_FRAMEWORK.Menu();
	NN_FRAMEWORK.OwlPage();
	NN_FRAMEWORK.Pagings();
	NN_FRAMEWORK.Cart();
	NN_FRAMEWORK.Videos();
	NN_FRAMEWORK.Photobox();
	NN_FRAMEWORK.Comment();
	NN_FRAMEWORK.Search();
	NN_FRAMEWORK.DomChange();
	/*NN_FRAMEWORK.TickerScroll();*/
	NN_FRAMEWORK.DatePicker();
	NN_FRAMEWORK.loadNameInputFile();
	NN_FRAMEWORK.QuickView();
	NN_FRAMEWORK.Toc();
	NN_FRAMEWORK.Custome();
	NN_FRAMEWORK.AchievementCounter();
	NN_FRAMEWORK.Accessibility();
});
/* Flash Sale countdowns: days : hours : minutes : seconds */
(function () {
	function pad(value) { return String(value).padStart(2, '0'); }
	function updateFlashSales() {
		document.querySelectorAll('[data-flash-sale-end]').forEach(function (badge) {
			var remaining = Number(badge.getAttribute('data-flash-sale-end')) - Date.now();
			if (remaining <= 0) {
				var item = badge.closest('[data-flash-sale-item]');
				if (item) window.location.reload();
				else badge.remove();
				return;
			}
			var totalSeconds = Math.floor(remaining / 1000);
			var days = Math.floor(totalSeconds / 86400);
			var hours = Math.floor((totalSeconds % 86400) / 3600);
			var minutes = Math.floor((totalSeconds % 3600) / 60);
			var seconds = totalSeconds % 60;
			var output = badge.querySelector('.product-flash-sale__time');
			if (output) output.textContent = pad(days) + ' : ' + pad(hours) + ' : ' + pad(minutes) + ' : ' + pad(seconds);
			var units = { days: days, hours: hours, minutes: minutes, seconds: seconds };
			Object.keys(units).forEach(function (unit) {
				var field = badge.querySelector('[data-flash-unit="' + unit + '"]');
				if (field) field.textContent = pad(units[unit]);
			});
		});
	}
	document.addEventListener('DOMContentLoaded', function () { updateFlashSales(); window.setInterval(updateFlashSales, 1000); });
}());
