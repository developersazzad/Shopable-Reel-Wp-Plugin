(function () {
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) { fn(); }
		else { document.addEventListener( 'DOMContentLoaded', fn ); }
	}

	/* ---- YouTube IFrame Player API loader (shared across all instances) ----
	   Raw postMessage "commands" sent to a plain <iframe> are unreliable
	   before the player has actually finished initializing, which is what
	   caused mute/unmute and autoplay to behave inconsistently. Loading the
	   real API and using YT.Player() gives us proper onReady/onStateChange
	   events and guaranteed-working mute()/unMute()/playVideo() calls. */
	var ytApiPromise = null;
	function loadYouTubeAPI() {
		if ( ytApiPromise ) return ytApiPromise;
		ytApiPromise = new Promise( function ( resolve ) {
			if ( window.YT && window.YT.Player ) {
				resolve( window.YT );
				return;
			}
			var previousCallback = window.onYouTubeIframeAPIReady;
			window.onYouTubeIframeAPIReady = function () {
				if ( typeof previousCallback === 'function' ) previousCallback();
				resolve( window.YT );
			};
			if ( ! document.querySelector( 'script[src*="youtube.com/iframe_api"]' ) ) {
				var tag = document.createElement( 'script' );
				tag.src = 'https://www.youtube.com/iframe_api';
				document.head.appendChild( tag );
			}
		} );
		return ytApiPromise;
	}

	ready( function () {
		initAllWrappers();
		observeForNewWrappers();
	} );

	function initAllWrappers() {
		document.querySelectorAll( '.sr-devsazzad-wrapper:not([data-sr-initialized])' ).forEach( function ( wrapper ) {
			wrapper.setAttribute( 'data-sr-initialized', '1' );
			initWrapper( wrapper );
		} );
	}

	/* Elementor's editor renders/re-renders widgets via AJAX after the initial
	   DOMContentLoaded event has already fired, which is why the carousel
	   could look inert/broken inside the editor even though a hard page load
	   (the live site) worked fine. Watching the DOM for newly-added wrappers
	   makes this self-healing regardless of how the markup got inserted. */
	function observeForNewWrappers() {
		if ( ! window.MutationObserver ) return;
		var observer = new MutationObserver( function ( mutations ) {
			var found = false;
			for ( var i = 0; i < mutations.length; i++ ) {
				if ( mutations[ i ].addedNodes && mutations[ i ].addedNodes.length ) { found = true; break; }
			}
			if ( found ) initAllWrappers();
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	}

	/* Small helper: eased smooth-scroll with a configurable duration, since
	   native scrollBy({behavior:'smooth'}) can't be timed/eased consistently
	   across browsers. */
	function animateScroll( el, deltaX, duration ) {
		var start = el.scrollLeft;
		var startTime = null;

		function easeInOutCubic( t ) {
			return t < 0.5 ? 4 * t * t * t : 1 - Math.pow( -2 * t + 2, 3 ) / 2;
		}

		function step( timestamp ) {
			if ( startTime === null ) startTime = timestamp;
			var elapsed = timestamp - startTime;
			var progress = Math.min( elapsed / duration, 1 );
			el.scrollLeft = start + deltaX * easeInOutCubic( progress );
			if ( progress < 1 ) requestAnimationFrame( step );
		}
		requestAnimationFrame( step );
	}

	var uidCounter = 0;

	function initWrapper( wrapper ) {
		var track   = wrapper.querySelector( '.sr-reels-track' );
		var prevBtn = wrapper.querySelector( '.sr-nav-prev' );
		var nextBtn = wrapper.querySelector( '.sr-nav-next' );

		var reels = [];
		try { reels = JSON.parse( wrapper.getAttribute( 'data-reels' ) ); } catch ( e ) { reels = []; }

		var autoplay          = wrapper.getAttribute( 'data-autoplay' ) === '1';
		var startMuted        = wrapper.getAttribute( 'data-muted' ) === '1';
		var badgeSuffix       = wrapper.getAttribute( 'data-badge-suffix' ) || '% Off';
		var scrollSpeed       = parseInt( wrapper.getAttribute( 'data-scroll-speed' ), 10 ) || 450;
		var showGoButton      = wrapper.getAttribute( 'data-show-go-button' ) === '1';
		var goButtonText      = wrapper.getAttribute( 'data-go-button-text' ) || 'Go';
		var showBottomOverlay = wrapper.getAttribute( 'data-modal-bottom-overlay' ) !== '0';

		var modal     = document.getElementById( wrapper.id + '-modal' );
		var stage     = modal.querySelector( '.sr-modal-stage' );
		var closeBtn  = modal.querySelector( '.sr-modal-close' );
		var modalPrev = modal.querySelector( '.sr-modal-prev' );
		var modalNext = modal.querySelector( '.sr-modal-next' );

		var activeIndex = 0;
		var muted = startMuted;
		var currentPlayer = null;
		var renderToken = 0; // guards against a stale async player create() landing after the user already navigated away

		if ( prevBtn ) prevBtn.addEventListener( 'click', function () { animateScroll( track, -320, scrollSpeed ); } );
		if ( nextBtn ) nextBtn.addEventListener( 'click', function () { animateScroll( track, 320, scrollSpeed ); } );

		wrapper.querySelectorAll( '.sr-reel-card' ).forEach( function ( card ) {
			card.addEventListener( 'click', function () {
				openModal( parseInt( card.getAttribute( 'data-index' ), 10 ) );
			} );
			card.addEventListener( 'keypress', function ( e ) {
				if ( e.key === 'Enter' ) openModal( parseInt( card.getAttribute( 'data-index' ), 10 ) );
			} );
			var expandBtn = card.querySelector( '.sr-card-expand-btn' );
			if ( expandBtn ) {
				expandBtn.addEventListener( 'click', function ( e ) {
					e.stopPropagation();
					openModal( parseInt( card.getAttribute( 'data-index' ), 10 ) );
				} );
			}
		} );

		closeBtn.addEventListener( 'click', closeModal );
		modal.addEventListener( 'click', function ( e ) { if ( e.target === modal ) closeModal(); } );
		document.addEventListener( 'keydown', function ( e ) {
			if ( ! modal.classList.contains( 'sr-open' ) ) return;
			if ( e.key === 'Escape' ) closeModal();
			if ( e.key === 'ArrowLeft' ) showReel( activeIndex - 1 );
			if ( e.key === 'ArrowRight' ) showReel( activeIndex + 1 );
		} );
		modalPrev.addEventListener( 'click', function () { showReel( activeIndex - 1 ); } );
		modalNext.addEventListener( 'click', function () { showReel( activeIndex + 1 ); } );

		function openModal( index ) {
			muted = startMuted;
			activeIndex = index;
			modal.classList.add( 'sr-open' );
			modal.setAttribute( 'aria-hidden', 'false' );
			document.body.style.overflow = 'hidden';
			renderStage();
		}

		function closeModal() {
			modal.classList.remove( 'sr-open' );
			modal.setAttribute( 'aria-hidden', 'true' );
			document.body.style.overflow = '';
			destroyCurrentPlayer();
			stage.innerHTML = '';
		}

		function showReel( index ) {
			if ( reels.length === 0 ) return;
			index = ( ( index % reels.length ) + reels.length ) % reels.length;
			activeIndex = index;
			muted = startMuted;
			renderStage();
		}

		function destroyCurrentPlayer() {
			if ( currentPlayer ) {
				try { currentPlayer.destroy(); } catch ( e ) {}
				currentPlayer = null;
			}
		}

		function updateSoundGlyph( glyphEl ) {
			if ( ! glyphEl ) return;
			glyphEl.textContent = muted ? '\uD83D\uDD07' : '\uD83D\uDD0A';
		}

		function togglePlay() {
			if ( ! currentPlayer || typeof currentPlayer.getPlayerState !== 'function' ) return;
			try {
				var state = currentPlayer.getPlayerState();
				if ( state === 1 ) { // playing
					currentPlayer.pauseVideo();
				} else {
					currentPlayer.playVideo();
				}
			} catch ( e ) {}
		}

		function renderStage() {
			destroyCurrentPlayer();
			stage.innerHTML = '';
			renderToken++;
			if ( ! reels.length ) return;

			var prevReel = reels[ ( activeIndex - 1 + reels.length ) % reels.length ];
			var current  = reels[ activeIndex ];
			var nextReel = reels[ ( activeIndex + 1 ) % reels.length ];

			if ( reels.length > 1 ) stage.appendChild( buildSide( prevReel, activeIndex - 1 ) );
			stage.appendChild( buildMain( current, renderToken ) );
			if ( reels.length > 1 ) stage.appendChild( buildSide( nextReel, activeIndex + 1 ) );

			trackView( current.id );
		}

		function buildSide( reel, index ) {
			var div = document.createElement( 'div' );
			div.className = 'sr-side-reel';

			var discountBadge = reel.discount > 0 ? '<span class="sr-off-badge">' + reel.discount + badgeSuffix + '</span>' : '';
			var goBtn = showGoButton ? '<span class="sr-side-go-btn">' + escapeHtml( goButtonText ) + '</span>' : '';

			div.innerHTML =
				'<img src="' + escapeAttr( reel.thumbnail ) + '" alt="' + escapeAttr( reel.title ) + '">' +
				'<div class="sr-side-card">' +
					'<img class="sr-side-avatar" src="' + escapeAttr( reel.product_thumb || reel.thumbnail ) + '" alt="">' +
					'<div class="sr-side-card-info">' +
						discountBadge +
						goBtn +
					'</div>' +
				'</div>';

			div.addEventListener( 'click', function () { showReel( index ); } );
			return div;
		}

		function buildMain( reel, token ) {
			var div = document.createElement( 'div' );
			div.className = 'sr-main-reel';

			var discountBadge = reel.discount > 0 ? '<span class="sr-off-badge">' + reel.discount + badgeSuffix + '</span>' : '';
			var goButtonHtml  = showGoButton ? '<a href="' + escapeAttr( reel.permalink ) + '" class="sr-main-go-btn">' + escapeHtml( goButtonText ) + '</a>' : '';

			var mediaHtml = reel.video_id
				? '<div class="sr-yt-player-target"></div>'
				: '<img src="' + escapeAttr( reel.thumbnail ) + '" alt="' + escapeAttr( reel.title ) + '">';

			div.innerHTML = mediaHtml +
				( reel.video_id ? '<button type="button" class="sr-sound-toggle" title="Toggle sound" aria-label="Toggle sound"><span class="sr-sound-glyph"></span></button>' : '' ) +
				'<button type="button" class="sr-share-btn" title="Share">&#128279;</button>' +
				'<div class="sr-main-info' + ( showBottomOverlay ? '' : ' sr-no-overlay' ) + '">' +
					'<img src="' + escapeAttr( reel.product_thumb || reel.thumbnail ) + '" alt="">' +
					'<div class="sr-main-info-text">' +
						'<h4><a href="' + escapeAttr( reel.permalink ) + '">' + escapeHtml( reel.title ) + '</a></h4>' +
						'<p>' + reel.price_html + ' ' + discountBadge + '</p>' +
					'</div>' +
					goButtonHtml +
				'</div>';

			if ( reel.video_id ) {
				var targetEl = div.querySelector( '.sr-yt-player-target' );
				var playerId = 'sr-yt-player-' + ( ++uidCounter );
				targetEl.id = playerId;

				var soundToggle = div.querySelector( '.sr-sound-toggle' );
				var soundGlyph  = div.querySelector( '.sr-sound-glyph' );
				updateSoundGlyph( soundGlyph );

				loadYouTubeAPI().then( function ( YT ) {
					if ( token !== renderToken ) return; // user already navigated away before the API/script finished loading

					currentPlayer = new YT.Player( playerId, {
						videoId: reel.video_id,
						playerVars: {
							autoplay: autoplay ? 1 : 0,
							mute: muted ? 1 : 0,
							controls: 0,
							rel: 0,
							modestbranding: 1,
							playsinline: 1,
							iv_load_policy: 3,
							disablekb: 1,
							loop: 1,
							playlist: reel.video_id
						},
						events: {
							onReady: function ( e ) {
								if ( token !== renderToken ) { try { e.target.destroy(); } catch ( er ) {} return; }
								if ( muted ) { e.target.mute(); } else { e.target.unMute(); }
								if ( autoplay ) { e.target.playVideo(); }
							}
						}
					} );
				} );

				soundToggle.addEventListener( 'click', function ( e ) {
					e.stopPropagation();
					muted = ! muted;
					if ( currentPlayer && typeof currentPlayer.mute === 'function' ) {
						muted ? currentPlayer.mute() : currentPlayer.unMute();
					}
					updateSoundGlyph( soundGlyph );
				} );

				// Tap anywhere on the video (outside the buttons/info bar) to toggle play/pause.
				div.addEventListener( 'click', function ( e ) {
					if ( e.target.closest( '.sr-sound-toggle, .sr-share-btn, .sr-main-info, .sr-main-go-btn' ) ) return;
					togglePlay();
				} );
			}

			var shareBtn = div.querySelector( '.sr-share-btn' );
			shareBtn.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
				if ( navigator.share ) {
					navigator.share( { title: reel.title, url: reel.permalink } ).catch( function () {} );
				} else if ( navigator.clipboard ) {
					navigator.clipboard.writeText( reel.permalink );
					shareBtn.innerHTML = '&#10003;';
					setTimeout( function () { shareBtn.innerHTML = '&#128279;'; }, 1500 );
				}
			} );

			return div;
		}

		function trackView( productId ) {
			if ( ! window.SR_DEVSAZZAD ) return;
			var body = new URLSearchParams();
			body.append( 'action', 'sr_devsazzad_track_view' );
			body.append( 'nonce', SR_DEVSAZZAD.nonce );
			body.append( 'product_id', productId );
			fetch( SR_DEVSAZZAD.ajax_url, { method: 'POST', body: body } ).catch( function () {} );
		}

		function escapeAttr( str ) {
			return ( str || '' ).replace( /"/g, '&quot;' );
		}
		function escapeHtml( str ) {
			var div = document.createElement( 'div' );
			div.textContent = str || '';
			return div.innerHTML;
		}
	}
} )();
