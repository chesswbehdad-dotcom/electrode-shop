/* Electrode Shop – slider + mobile nav. No dependencies. */
(function () {
	'use strict';

	// Mobile navigation toggle.
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	// Hero slider.
	document.querySelectorAll('[data-slider]').forEach(function (root) {
		var track = root.querySelector('.slider__track');
		var slides = Array.prototype.slice.call(root.querySelectorAll('.slide'));
		var dotsWrap = root.querySelector('.slider__dots');
		var progress = root.querySelector('.slider__progress');
		var interval = parseInt(root.getAttribute('data-interval'), 10) || 6000;
		var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var index = 0;
		var timer = null;

		if (slides.length < 2) { return; }
		root.style.setProperty('--slide-ms', interval + 'ms');

		var dots = slides.map(function (_, i) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'اسلاید ' + (i + 1));
			b.addEventListener('click', function () { go(i); restart(); });
			dotsWrap.appendChild(b);
			return b;
		});

		function go(i) {
			index = (i + slides.length) % slides.length;
			// RTL: positive translate moves the track to the right.
			track.style.transform = 'translateX(' + (index * 100) + '%)';
			slides.forEach(function (s, k) {
				s.classList.toggle('is-active', k === index);
				s.setAttribute('aria-hidden', k === index ? 'false' : 'true');
			});
			dots.forEach(function (d, k) { d.setAttribute('aria-current', k === index ? 'true' : 'false'); });
			// Restart progress bar animation.
			if (progress) {
				root.classList.remove('is-playing');
				void progress.offsetWidth;
				if (timer) { root.classList.add('is-playing'); }
			}
		}
		function next() { go(index + 1); }
		function prev() { go(index - 1); }
		function stop() { clearInterval(timer); timer = null; root.classList.remove('is-playing'); }
		function start() {
			if (reduce || timer) { return; }
			timer = setInterval(next, interval);
			root.classList.add('is-playing');
		}
		function restart() { stop(); start(); go(index); }

		root.querySelector('.slider__btn--next').addEventListener('click', function () { next(); restart(); });
		root.querySelector('.slider__btn--prev').addEventListener('click', function () { prev(); restart(); });
		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { next(); }
			if (e.key === 'ArrowRight') { prev(); }
		});
		document.addEventListener('visibilitychange', function () { if (document.hidden) { stop(); } else { start(); } });

		// Touch swipe (RTL: swipe left = next).
		var x0 = null;
		root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; stop(); }, { passive: true });
		root.addEventListener('touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) { if (dx < 0) { next(); } else { prev(); } }
			x0 = null;
			start();
		});

		go(0);
		start();
	});
})();
