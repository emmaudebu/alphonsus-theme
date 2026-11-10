/**
 * Alphonsus theme interactions (no dependencies).
 */
( function () {
	'use strict';

	var data = window.alphData || { i18n: {} };
	var i18n = data.i18n || {};

	/* Toast ------------------------------------------------------------ */

	var toastEl = document.querySelector( '.toast' );
	var toastTimer;
	function toast( message ) {
		if ( ! toastEl ) {
			return;
		}
		toastEl.textContent = message;
		toastEl.hidden = false;
		clearTimeout( toastTimer );
		toastTimer = setTimeout( function () {
			toastEl.hidden = true;
		}, 2600 );
	}

	/* Mobile navigation drawer ---------------------------------------- */

	var nav = document.getElementById( 'site-nav' );
	var toggle = document.querySelector( '.nav-toggle' );
	var overlay = document.querySelector( '.nav-overlay' );

	function setNav( open ) {
		if ( ! nav || ! toggle ) {
			return;
		}
		nav.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		if ( overlay ) {
			overlay.hidden = ! open;
		}
		document.documentElement.style.overflow = open ? 'hidden' : '';
		if ( open ) {
			var first = nav.querySelector( '.menu a' );
			if ( first ) {
				first.focus();
			}
		} else {
			toggle.focus();
		}
	}

	if ( toggle ) {
		toggle.addEventListener( 'click', function () {
			setNav( ! nav.classList.contains( 'is-open' ) );
		} );
	}
	document.querySelectorAll( '.nav-close, .nav-overlay' ).forEach( function ( el ) {
		el.addEventListener( 'click', function () {
			setNav( false );
		} );
	} );
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && nav && nav.classList.contains( 'is-open' ) ) {
			setNav( false );
		}
	} );

	/* Sticky header shadow -------------------------------------------- */

	var header = document.querySelector( '[data-sticky-header]' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-stuck', window.scrollY > 40 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* Live product search --------------------------------------------- */

	function escapeHtml( str ) {
		return String( str ).replace( /[&<>"']/g, function ( c ) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ c ];
		} );
	}

	document.querySelectorAll( '[data-live-search]' ).forEach( function ( form ) {
		var input = form.querySelector( 'input[type="search"]' );
		var cat = form.querySelector( 'select[name="product_cat"]' );
		var box = form.querySelector( '.search__results' );
		var timer;
		var controller;
		var active = -1;

		if ( ! input || ! box || ! data.restUrl ) {
			return;
		}

		function close() {
			box.hidden = true;
			input.setAttribute( 'aria-expanded', 'false' );
			active = -1;
		}

		function render( items, q ) {
			var all = form.action + '?post_type=product&s=' + encodeURIComponent( q ) + ( cat && cat.value ? '&product_cat=' + encodeURIComponent( cat.value ) : '' );
			if ( ! items.length ) {
				box.innerHTML = '<div class="search__empty">' + escapeHtml( i18n.noResults || 'No results' ) + '</div>';
			} else {
				box.innerHTML = items.map( function ( item, i ) {
					var img = item.image ? '<img src="' + escapeHtml( item.image ) + '" alt="" loading="lazy">' : '<span class="ph"></span>';
					return '<a class="search__result" role="option" id="sr-' + i + '" aria-selected="false" href="' + escapeHtml( item.url ) + '">' + img + '<span><strong>' + escapeHtml( item.title ) + '</strong><span>' + escapeHtml( item.price ) + '</span></span></a>';
				} ).join( '' ) + '<a class="search__all" href="' + escapeHtml( all ) + '">' + escapeHtml( i18n.viewAll || 'See all results' ) + ' →</a>';
			}
			box.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
			active = -1;
		}

		function run() {
			var q = input.value.trim();
			if ( q.length < 2 ) {
				close();
				return;
			}
			if ( controller ) {
				controller.abort();
			}
			controller = 'AbortController' in window ? new AbortController() : null;
			var url = data.restUrl + 'search?q=' + encodeURIComponent( q ) + ( cat && cat.value ? '&cat=' + encodeURIComponent( cat.value ) : '' );
			fetch( url, controller ? { signal: controller.signal } : {} )
				.then( function ( r ) {
					return r.ok ? r.json() : [];
				} )
				.then( function ( items ) {
					render( Array.isArray( items ) ? items : [], q );
				} )
				.catch( function () {} );
		}

		input.addEventListener( 'input', function () {
			clearTimeout( timer );
			timer = setTimeout( run, 220 );
		} );
		if ( cat ) {
			cat.addEventListener( 'change', run );
		}
		input.addEventListener( 'keydown', function ( e ) {
			var links = box.querySelectorAll( '.search__result' );
			if ( box.hidden || ! links.length ) {
				return;
			}
			if ( 'ArrowDown' === e.key || 'ArrowUp' === e.key ) {
				e.preventDefault();
				active = ( active + ( 'ArrowDown' === e.key ? 1 : -1 ) + links.length ) % links.length;
				links.forEach( function ( l, i ) {
					l.setAttribute( 'aria-selected', i === active ? 'true' : 'false' );
				} );
				input.setAttribute( 'aria-activedescendant', links[ active ].id );
			} else if ( 'Enter' === e.key && active > -1 ) {
				e.preventDefault();
				window.location.href = links[ active ].href;
			} else if ( 'Escape' === e.key ) {
				close();
			}
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( ! form.contains( e.target ) ) {
				close();
			}
		} );
	} );

	// Press "/" anywhere to jump to the search box.
	document.addEventListener( 'keydown', function ( e ) {
		if ( '/' !== e.key || e.metaKey || e.ctrlKey ) {
			return;
		}
		var tag = ( e.target.tagName || '' ).toLowerCase();
		if ( 'input' === tag || 'textarea' === tag || 'select' === tag || e.target.isContentEditable ) {
			return;
		}
		var search = document.querySelector( '.site-header input[type="search"]' );
		if ( search ) {
			e.preventDefault();
			search.focus();
		}
	} );

	/* Wishlist -------------------------------------------------------- */

	var wl = window.alphWishlist || { ids: [], cookie: 'alph_wishlist', path: '/' };
	var wishIds = ( wl.ids || [] ).map( Number );

	function saveWishlist() {
		var maxAge = 60 * 60 * 24 * 365;
		document.cookie = wl.cookie + '=' + wishIds.join( ',' ) + ';path=' + wl.path + ';max-age=' + maxAge + ';SameSite=Lax' + ( 'https:' === location.protocol ? ';Secure' : '' );
		document.querySelectorAll( '[data-wishlist-count]' ).forEach( function ( el ) {
			el.textContent = wishIds.length;
			el.classList.toggle( 'is-empty', ! wishIds.length );
		} );
		if ( wl.loggedIn && wl.nonce && data.restUrl ) {
			fetch( data.restUrl + 'wishlist', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': wl.nonce },
				body: JSON.stringify( { ids: wishIds } ),
				credentials: 'same-origin',
			} ).catch( function () {} );
		}
	}

	// Sync any server-merged list (logged-in users) back to the cookie.
	if ( wl.loggedIn ) {
		saveWishlist();
	}

	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-wishlist]' );
		if ( ! btn ) {
			return;
		}
		e.preventDefault();
		var id = Number( btn.getAttribute( 'data-wishlist' ) );
		var on = wishIds.indexOf( id ) === -1;
		wishIds = on ? wishIds.concat( id ) : wishIds.filter( function ( x ) {
			return x !== id;
		} );
		saveWishlist();

		document.querySelectorAll( '[data-wishlist="' + id + '"]' ).forEach( function ( b ) {
			b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			b.setAttribute( 'aria-label', on ? i18n.removeLabel : i18n.addLabel );
			b.setAttribute( 'title', on ? i18n.removeLabel : i18n.addLabel );
		} );
		toast( on ? i18n.added : i18n.removed );

		// On the wishlist page, removing a book removes its card.
		var page = btn.closest( '[data-wishlist-page]' );
		if ( page && ! on ) {
			var card = btn.closest( '.product-card' );
			if ( card ) {
				card.remove();
			}
			if ( ! page.querySelector( '.product-card' ) ) {
				var empty = page.querySelector( '.empty-state' );
				if ( empty ) {
					empty.hidden = false;
				}
			}
		}
	} );

	/* Offer countdown ------------------------------------------------- */

	document.querySelectorAll( '[data-countdown]' ).forEach( function ( el ) {
		var end = Number( el.getAttribute( 'data-countdown' ) ) * 1000;
		var out = el.querySelector( '[data-countdown-text]' );
		function tick() {
			var left = end - Date.now();
			if ( left <= 0 ) {
				el.hidden = true;
				return;
			}
			var d = Math.floor( left / 864e5 );
			var h = Math.floor( ( left % 864e5 ) / 36e5 );
			var m = Math.floor( ( left % 36e5 ) / 6e4 );
			var s = Math.floor( ( left % 6e4 ) / 1e3 );
			out.textContent = d > 0 ? d + 'd ' + h + 'h ' + m + 'm' : h + 'h ' + m + 'm ' + s + 's';
			setTimeout( tick, d > 0 ? 30000 : 1000 );
		}
		if ( out ) {
			tick();
		}
	} );

	/* Google Translate (loaded only when a visitor asks for it) ------- */

	var translateBtn = document.querySelector( '[data-translate]' );
	var translatePanel = document.getElementById( 'google_translate_element' );
	var translateLoaded = false;

	window.alphTranslateInit = function () {
		/* global google */
		new google.translate.TranslateElement( {
			pageLanguage: 'en',
			includedLanguages: 'en,sv,fr,de,es,it,pt,yo,ig,ha,ar,zh-CN',
			autoDisplay: false,
		}, 'google_translate_element' );
	};

	if ( translateBtn && translatePanel ) {
		translateBtn.addEventListener( 'click', function () {
			var open = translatePanel.hidden;
			translatePanel.hidden = ! open;
			translateBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open && ! translateLoaded ) {
				translateLoaded = true;
				var s = document.createElement( 'script' );
				s.src = 'https://translate.google.com/translate_a/element.js?cb=alphTranslateInit';
				s.async = true;
				document.body.appendChild( s );
			}
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( ! translatePanel.hidden && ! e.target.closest( '.translate' ) ) {
				translatePanel.hidden = true;
				translateBtn.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	/* Pre-order form: at least one book ------------------------------- */

	document.querySelectorAll( '[data-require-one]' ).forEach( function ( group ) {
		var form = group.closest( 'form' );
		var boxes = group.querySelectorAll( 'input[type="checkbox"]' );
		if ( ! form || ! boxes.length ) {
			return;
		}
		function validate() {
			var any = Array.prototype.some.call( boxes, function ( b ) {
				return b.checked;
			} );
			boxes[ 0 ].setCustomValidity( any ? '' : 'Please select at least one book.' );
			group.classList.toggle( 'is-invalid', ! any && form.classList.contains( 'was-submitted' ) );
			return any;
		}
		boxes.forEach( function ( b ) {
			b.addEventListener( 'change', validate );
		} );
		form.addEventListener( 'submit', function ( e ) {
			form.classList.add( 'was-submitted' );
			if ( ! validate() ) {
				e.preventDefault();
				boxes[ 0 ].reportValidity();
			}
		} );
		validate();
	} );
}() );
