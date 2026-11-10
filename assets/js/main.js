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

	/* Mobile search toggle ---------------------------------------- */

	var searchToggle = document.querySelector( '[data-search-toggle]' );
	var siteHeader = document.querySelector( '.site-header' );
	if ( searchToggle && siteHeader ) {
		searchToggle.addEventListener( 'click', function () {
			var open = ! siteHeader.classList.contains( 'search-open' );
			siteHeader.classList.toggle( 'search-open', open );
			searchToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				var input = siteHeader.querySelector( '.search input[type="search"]' );
				if ( input ) {
					input.focus();
				}
			}
		} );
	}

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

	function formatPrice( amount ) {
		var p = data.price;
		if ( ! p ) {
			return String( amount );
		}
		var parts = Number( amount ).toFixed( p.decimals ).split( '.' );
		parts[ 0 ] = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, p.thou );
		var num = parts.join( p.dec );
		return p.format.replace( '%1$s', p.symbol ).replace( '%2$s', num ).replace( /&nbsp;/g, '\u00a0' );
	}

	function updateWishlistSummary( page ) {
		var rows = page.querySelectorAll( '[data-wl-item]' );
		var total = 0;
		rows.forEach( function ( r ) {
			total += Number( r.getAttribute( 'data-price' ) ) || 0;
		} );
		var count = page.querySelector( '[data-wl-count]' );
		var sum = page.querySelector( '[data-wl-total]' );
		if ( count ) {
			count.textContent = rows.length;
		}
		if ( sum ) {
			sum.textContent = formatPrice( total );
		}
		if ( ! rows.length ) {
			[ '[data-wl-summary]', '[data-wl-addall]', '[data-wl-list]' ].forEach( function ( sel ) {
				var el = page.querySelector( sel );
				if ( el ) {
					el.hidden = true;
				}
			} );
			var empty = page.querySelector( '[data-wl-empty]' );
			if ( empty ) {
				empty.hidden = false;
			}
		}
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

		// On the wishlist page, removing a saved book removes its row and updates the totals.
		var page = btn.closest( '[data-wishlist-page]' );
		var row = btn.closest( '[data-wl-item]' );
		if ( page && row && ! on ) {
			row.classList.add( 'is-leaving' );
			setTimeout( function () {
				row.remove();
				updateWishlistSummary( page );
			}, 250 );
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

	/* Shop filters collapse on small screens ------------------------ */

	document.querySelectorAll( '[data-filters-toggle]' ).forEach( function ( d ) {
		if ( window.matchMedia( '(max-width: 1024px)' ).matches && ! document.querySelector( '.active-filters' ) ) {
			d.open = false;
		}
	} );

	/* Currency picker ----------------------------------------------- */

	var currencyModal = document.getElementById( 'currency-modal' );
	if ( currencyModal && 'function' === typeof currencyModal.showModal ) {
		document.querySelectorAll( '[data-currency-open]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				currencyModal.showModal();
			} );
		} );
		var cSearch = currencyModal.querySelector( '[data-currency-search]' );
		if ( cSearch ) {
			cSearch.addEventListener( 'input', function () {
				var q = cSearch.value.trim().toLowerCase();
				currencyModal.querySelectorAll( '.currency-row' ).forEach( function ( row ) {
					row.hidden = q && row.getAttribute( 'data-search' ).indexOf( q ) === -1;
				} );
			} );
		}
		currencyModal.addEventListener( 'close', function () {
			if ( 'save' !== currencyModal.returnValue ) {
				return;
			}
			var chosen = currencyModal.querySelector( 'input[name="currency"]:checked' );
			if ( chosen ) {
				document.cookie = 'psmmc_currency=' + encodeURIComponent( chosen.value ) + ';path=/;max-age=86400;SameSite=Lax' + ( 'https:' === location.protocol ? ';Secure' : '' );
				window.location.reload();
			}
		} );
		currencyModal.addEventListener( 'click', function ( e ) {
			if ( e.target === currencyModal ) {
				currencyModal.close( 'cancel' );
			}
		} );
	}

	/* Donation tiers fill the amount field -------------------------- */

	document.querySelectorAll( '[data-donate]' ).forEach( function ( form ) {
		var amount = form.querySelector( 'input[name="amount"]' );
		form.querySelectorAll( 'input[name="tier"]' ).forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				if ( amount ) {
					amount.value = radio.value;
				}
			} );
		} );
		if ( amount ) {
			amount.addEventListener( 'input', function () {
				form.querySelectorAll( 'input[name="tier"]' ).forEach( function ( radio ) {
					radio.checked = radio.value === amount.value;
				} );
			} );
		}
	} );

	/* "Enrol" buttons pre-select the course in the enrolment form ---- */

	document.querySelectorAll( '[data-track]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			var select = document.querySelector( '#enrol select[name="track"]' );
			var value = link.getAttribute( 'data-track' );
			if ( select && Array.prototype.some.call( select.options, function ( o ) {
				return o.value === value;
			} ) ) {
				select.value = value;
			}
		} );
	} );

	/* Merch category pills filter the grid in place ------------------- */

	document.querySelectorAll( '[data-filter-pills]' ).forEach( function ( nav ) {
		var grid = document.querySelector( '[data-filter-grid]' );
		nav.addEventListener( 'click', function ( e ) {
			var pill = e.target.closest( '[data-filter]' );
			if ( ! pill || ! grid ) {
				return;
			}
			var f = pill.getAttribute( 'data-filter' );
			nav.querySelectorAll( '.pill' ).forEach( function ( p ) {
				p.classList.toggle( 'is-active', p === pill );
			} );
			grid.querySelectorAll( '[data-cats]' ).forEach( function ( cell ) {
				cell.hidden = 'all' !== f && ( ' ' + cell.getAttribute( 'data-cats' ) + ' ' ).indexOf( ' ' + f + ' ' ) === -1;
			} );
		} );
	} );

	/* Reading Journey ----------------------------------------------- */

	var rj = window.alphReading;
	var rjRoot = document.querySelector( '[data-rj]' );
	if ( rj && rjRoot ) {
		var rjI18n = rj.i18n || {};
		var rjPost = function ( path, body ) {
			return fetch( rj.url + path, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': rj.nonce },
				body: JSON.stringify( body || {} ),
			} ).then( function ( r ) {
				if ( ! r.ok ) {
					throw new Error( r.status );
				}
				return r.json();
			} );
		};
		var rjFail = function () {
			toast( rjI18n.error );
		};

		// Timer: survives reloads by keeping its state in localStorage.
		var KEY = 'alph_rj_timer';
		var store = {
			get: function () {
				try {
					return JSON.parse( localStorage.getItem( KEY ) ) || null;
				} catch ( e ) {
					return null;
				}
			},
			set: function ( v ) {
				try {
					if ( v ) {
						localStorage.setItem( KEY, JSON.stringify( v ) );
					} else {
						localStorage.removeItem( KEY );
					}
				} catch ( e ) {}
			},
		};
		var timerEl = rjRoot.querySelector( '[data-rj-timer]' );
		var clock = rjRoot.querySelector( '[data-rj-clock]' );
		var startBtn = rjRoot.querySelector( '[data-rj-start]' );
		var pauseBtn = rjRoot.querySelector( '[data-rj-pause]' );
		var bookSel = rjRoot.querySelector( '[data-rj-book]' );
		var tick = null;

		var elapsed = function ( t ) {
			return ( t.acc || 0 ) + ( t.start ? Date.now() - t.start : 0 );
		};
		var paint = function () {
			var t = store.get();
			var ms = t ? elapsed( t ) : 0;
			var s = Math.floor( ms / 1000 );
			clock.textContent = String( Math.floor( s / 60 ) ).padStart( 2, '0' ) + ':' + String( s % 60 ).padStart( 2, '0' );
			var running = !! ( t && t.start );
			timerEl.classList.toggle( 'is-running', running );
			startBtn.querySelector( 'span' ).textContent = t ? rjI18n.stop : rjI18n.start;
			pauseBtn.hidden = ! t;
			pauseBtn.querySelector( 'span' ).textContent = running ? rjI18n.pause : rjI18n.resume;
		};
		var loop = function () {
			clearInterval( tick );
			tick = setInterval( paint, 1000 );
			paint();
		};

		if ( timerEl ) {
			var saved = store.get();
			if ( saved ) {
				if ( bookSel && saved.book ) {
					bookSel.value = String( saved.book );
				}
				loop();
			}
			startBtn.addEventListener( 'click', function () {
				var t = store.get();
				if ( ! t ) {
					store.set( { start: Date.now(), acc: 0, book: bookSel ? Number( bookSel.value ) : 0 } );
					loop();
					return;
				}
				// Stop & log.
				var minutes = Math.max( 1, Math.round( elapsed( t ) / 60000 ) );
				store.set( null );
				clearInterval( tick );
				paint();
				rjPost( 'log', { minutes: Math.min( 600, minutes ), product: t.book || 0, date: rj.today } ).then( function () {
					toast( rjI18n.logged );
					setTimeout( function () {
						window.location.reload();
					}, 700 );
				} ).catch( rjFail );
			} );
			pauseBtn.addEventListener( 'click', function () {
				var t = store.get();
				if ( ! t ) {
					return;
				}
				if ( t.start ) {
					t.acc = elapsed( t );
					t.start = 0;
				} else {
					t.start = Date.now();
				}
				store.set( t );
				paint();
			} );
			if ( bookSel ) {
				bookSel.addEventListener( 'change', function () {
					var t = store.get();
					if ( t ) {
						t.book = Number( bookSel.value );
						store.set( t );
					}
				} );
			}
		}

		// Manual log.
		var logToggle = rjRoot.querySelector( '[data-rj-log-toggle]' );
		var logForm = rjRoot.querySelector( '[data-rj-log]' );
		if ( logToggle && logForm ) {
			logToggle.addEventListener( 'click', function () {
				logForm.hidden = ! logForm.hidden;
				logToggle.setAttribute( 'aria-expanded', logForm.hidden ? 'false' : 'true' );
				if ( ! logForm.hidden ) {
					logForm.querySelector( 'input' ).focus();
				}
			} );
			logForm.addEventListener( 'submit', function ( e ) {
				e.preventDefault();
				var fd = new FormData( logForm );
				rjPost( 'log', { minutes: Number( fd.get( 'minutes' ) ), date: fd.get( 'date' ), product: Number( fd.get( 'product' ) ) } ).then( function () {
					toast( rjI18n.logged );
					setTimeout( function () {
						window.location.reload();
					}, 700 );
				} ).catch( rjFail );
			} );
		}

		var undo = rjRoot.querySelector( '[data-rj-undo]' );
		if ( undo ) {
			undo.addEventListener( 'click', function () {
				rjPost( 'undo' ).then( function () {
					window.location.reload();
				} ).catch( rjFail );
			} );
		}

		// Quests.
		rjRoot.querySelectorAll( '[data-rj-quest]' ).forEach( function ( box ) {
			box.addEventListener( 'change', function () {
				rjPost( 'quest', { key: box.getAttribute( 'data-rj-quest' ), done: box.checked } ).then( function () {
					toast( rjI18n.saved );
				} ).catch( function () {
					box.checked = ! box.checked;
					rjFail();
				} );
			} );
		} );

		// Goal tiers.
		rjRoot.querySelectorAll( '[data-rj-goal]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				rjPost( 'goal', { minutes: Number( btn.getAttribute( 'data-rj-goal' ) ) } ).then( function () {
					window.location.reload();
				} ).catch( rjFail );
			} );
		} );

		// Book progress sliders (saved when released).
		rjRoot.querySelectorAll( '[data-rj-progress]' ).forEach( function ( range ) {
			var label = range.closest( '.bp' ).querySelector( '[data-rj-pct]' );
			range.addEventListener( 'input', function () {
				range.style.setProperty( '--pct', range.value + '%' );
				label.textContent = range.value + '%';
			} );
			range.addEventListener( 'change', function () {
				rjPost( 'progress', { product: Number( range.getAttribute( 'data-rj-progress' ) ), percent: Number( range.value ) } ).then( function () {
					toast( rjI18n.saved );
					if ( '100' === range.value ) {
						setTimeout( function () {
							window.location.reload();
						}, 600 );
					}
				} ).catch( rjFail );
			} );
		} );
	}
}() );
