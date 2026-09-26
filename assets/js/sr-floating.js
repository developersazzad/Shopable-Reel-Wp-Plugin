(function () {
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) { fn(); }
		else { document.addEventListener( 'DOMContentLoaded', fn ); }
	}

	ready( function () {
		initAllFloating();
		observeForNewFloating();
	} );

	function initAllFloating() {
		document.querySelectorAll( '.sr-floating-reel:not([data-sr-initialized])' ).forEach( function ( container ) {
			container.setAttribute( 'data-sr-initialized', '1' );
			initFloatingReel( container );
		} );
	}

	function observeForNewFloating() {
		if ( ! window.MutationObserver ) return;
		var observer = new MutationObserver( function ( mutations ) {
			var found = false;
			for ( var i = 0; i < mutations.length; i++ ) {
				if ( mutations[ i ].addedNodes && mutations[ i ].addedNodes.length ) { found = true; break; }
			}
			if ( found ) initAllFloating();
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	}

	function initFloatingReel( container ) {
		var gallerySelector  = container.getAttribute( 'data-gallery-selector' );
		var fallbackSelector = container.getAttribute( 'data-fallback-selector' );
		var position         = container.getAttribute( 'data-position' ) || 'bottom-right';

		var target = null;
		if ( gallerySelector ) {
			try { target = document.querySelector( gallerySelector ); } catch ( e ) { target = null; }
		}
		if ( ! target && fallbackSelector ) {
			try { target = document.querySelector( fallbackSelector ); } catch ( e ) { target = null; }
		}

		if ( target ) {
			var computed = window.getComputedStyle( target );
			if ( computed.position === 'static' ) {
				target.style.position = 'relative';
			}
			target.appendChild( container );
		} else {
			// No gallery container found on this page — fall back to a fixed floating position.
			container.style.position = 'fixed';
		}

		container.classList.add( 'minimized' );
		container.classList.add( 'sr-pos-' + position );

		var dragOverlay = container.querySelector( '.sr-floating-drag-overlay' );
		var expandBtn   = container.querySelector( '.sr-floating-expand' );
		var closeBtn    = container.querySelector( '.sr-floating-close' );

		var isDragging = false;
		var currentX = 0, currentY = 0, initialX = 0, initialY = 0, xOffset = 0, yOffset = 0;

		closeBtn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			if ( container.classList.contains( 'expanded' ) ) {
				container.classList.remove( 'expanded' );
				container.classList.add( 'minimized' );
				container.style.transform = 'translate3d(' + xOffset + 'px,' + yOffset + 'px,0)';
			} else {
				container.style.display = 'none';
			}
		} );

		expandBtn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			container.classList.remove( 'minimized' );
			container.classList.add( 'expanded' );
			container.style.transform = '';
		} );

		dragOverlay.addEventListener( 'mousedown', dragStart );
		document.addEventListener( 'mouseup', dragEnd );
		document.addEventListener( 'mousemove', drag );
		dragOverlay.addEventListener( 'touchstart', dragStart, { passive: false } );
		document.addEventListener( 'touchend', dragEnd );
		document.addEventListener( 'touchmove', drag, { passive: false } );

		function dragStart( e ) {
			if ( container.classList.contains( 'expanded' ) ) return;

			container.classList.add( 'is-dragging' );

			if ( e.type === 'touchstart' ) {
				initialX = e.touches[0].clientX - xOffset;
				initialY = e.touches[0].clientY - yOffset;
			} else {
				initialX = e.clientX - xOffset;
				initialY = e.clientY - yOffset;
			}
			isDragging = true;
		}

		function dragEnd() {
			isDragging = false;
			container.classList.remove( 'is-dragging' );
		}

		function drag( e ) {
			if ( isDragging && ! container.classList.contains( 'expanded' ) ) {
				e.preventDefault();
				if ( e.type === 'touchmove' ) {
					currentX = e.touches[0].clientX - initialX;
					currentY = e.touches[0].clientY - initialY;
				} else {
					currentX = e.clientX - initialX;
					currentY = e.clientY - initialY;
				}
				xOffset = currentX;
				yOffset = currentY;
				container.style.transform = 'translate3d(' + currentX + 'px,' + currentY + 'px,0)';
			}
		}
	}
} )();
