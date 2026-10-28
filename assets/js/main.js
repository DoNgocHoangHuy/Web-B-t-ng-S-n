/**
 * Nhà Việt Realty - front-end behaviour (no dependencies).
 * Modules: header, menu, favourites, listing views + lazy map, gallery, share, filter drawer, tracking, image fallback.
 */
(function () {
	'use strict';
	var CFG = window.NVR || {};
	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var esc = function (v) {
		return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	};

	/* ------------------------------------------------------------------
	 * Tracking: one place, no duplicates. GA4 + Meta Pixel + dataLayer (GTM).
	 * ---------------------------------------------------------------- */
	function track(name, params) {
		params = params || {};
		try {
			window.dataLayer = window.dataLayer || [];
			if (typeof window.gtag !== 'function') window.dataLayer.push(Object.assign({ event: name }, params));
		} catch (e) {}
		if (typeof window.gtag === 'function') {
			window.gtag('event', name, Object.assign({ transport_type: 'beacon' }, params));
		}
		if (typeof window.fbq === 'function') {
			if (name === 'generate_lead') window.fbq('track', 'Lead');
			else if (name === 'click_hotline' || name === 'click_zalo') { window.fbq('track', 'Contact', { method: name }); window.fbq('trackCustom', name); }
			else window.fbq('trackCustom', name);
		}
	}
	window.nvrTrack = track;

	document.addEventListener('click', function (e) {
		var el = e.target.closest ? e.target.closest('[data-track]') : null;
		if (el) track(el.getAttribute('data-track'), { link_url: el.getAttribute('href') || '' });
	});

	document.addEventListener('submit', function (e) {
		var form = e.target;
		if (!form.matches || !form.matches('form[data-track-submit]')) return;
		var name = form.querySelector('[name=contact_name]');
		var phone = form.querySelector('[name=contact_phone]');
		var digits = phone ? phone.value.replace(/\D/g, '') : '';
		var old = form.parentNode.querySelector('.notice-client');
		if (old) old.remove();
		if (!name || !name.value.trim() || digits.length < 9 || digits.length > 12) {
			e.preventDefault();
			var n = document.createElement('div');
			n.className = 'notice notice-error notice-client';
			n.setAttribute('role', 'alert');
			n.textContent = 'Vui lòng nhập họ tên và số điện thoại hợp lệ (9–11 số).';
			form.parentNode.insertBefore(n, form);
			(name && !name.value.trim() ? name : phone).focus();
			return;
		}
		track(form.getAttribute('data-track-submit'));
	});

	/* Thank-you page: fire the conversion once per session. */
	var onLoad = $('[data-track-on-load]');
	if (onLoad) {
		var fired = false;
		try { fired = sessionStorage.getItem('nvr_lead_fired') === '1'; sessionStorage.setItem('nvr_lead_fired', '1'); } catch (e) {}
		if (!fired) track(onLoad.getAttribute('data-track-on-load'));
	}

	/* ------------------------------------------------------------------
	 * Header: sticky + compact on scroll, compact search bar, mobile menu
	 * ---------------------------------------------------------------- */
	var header = $('#site-header');
	var searchToggle = $('.search-toggle');
	var menuToggle = $('.menu-toggle');
	var mobileMenu = $('#mobile-menu');
	var heroSearch = $('.search-panel');

	function setHeaderVar() {
		if (header) document.documentElement.style.setProperty('--header-h', header.offsetHeight + 'px');
	}
	function onScroll() {
		if (!header) return;
		var y = window.pageYOffset || document.documentElement.scrollTop;
		header.classList.toggle('is-scrolled', y > 40);
		if (heroSearch) {
			var past = heroSearch.getBoundingClientRect().bottom < 70;
			header.classList.toggle('show-search', past);
		}
		setHeaderVar();
	}
	var ticking = false;
	window.addEventListener('scroll', function () {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(function () { onScroll(); ticking = false; });
	}, { passive: true });
	window.addEventListener('resize', setHeaderVar);
	onScroll();

	if (searchToggle && header) {
		searchToggle.addEventListener('click', function () {
			var open = header.classList.toggle('search-open');
			searchToggle.setAttribute('aria-expanded', String(open));
			if (open) { var kw = $('#hs-kw'); if (kw) setTimeout(function () { kw.focus(); }, 50); }
			setHeaderVar();
		});
	}
	if (menuToggle && mobileMenu) {
		menuToggle.addEventListener('click', function () {
			var open = mobileMenu.hasAttribute('hidden');
			if (open) mobileMenu.removeAttribute('hidden'); else mobileMenu.setAttribute('hidden', '');
			menuToggle.setAttribute('aria-expanded', String(open));
			document.body.classList.toggle('menu-open', open);
			setHeaderVar();
		});
	}
	document.addEventListener('keydown', function (e) {
		if (e.key !== 'Escape') return;
		if (mobileMenu && !mobileMenu.hasAttribute('hidden')) menuToggle.click();
		var fb = $('#property-filter.filter-open');
		if (fb) toggleFilter(false);
	});

	/* ------------------------------------------------------------------
	 * Favourites (localStorage)
	 * ---------------------------------------------------------------- */
	var FAV_KEY = 'nhaviet_favorites';
	function getFavs() { try { return JSON.parse(localStorage.getItem(FAV_KEY) || '[]').map(String); } catch (e) { return []; } }
	function setFavs(a) { try { localStorage.setItem(FAV_KEY, JSON.stringify(a)); } catch (e) {} }
	function syncFavs() {
		var favs = getFavs();
		$$('[data-favorite]').forEach(function (b) {
			var on = favs.indexOf(String(b.getAttribute('data-favorite'))) > -1;
			b.classList.toggle('is-saved', on);
			b.setAttribute('aria-pressed', String(on));
			b.setAttribute('aria-label', on ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích');
			var label = $('span', b);
			if (label) label.textContent = on ? 'Đã lưu' : 'Yêu thích';
		});
		$$('[data-fav-count]').forEach(function (c) { c.textContent = favs.length; if (favs.length) c.removeAttribute('hidden'); else c.setAttribute('hidden', ''); });
	}
	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('[data-favorite]') : null;
		if (!btn) return;
		e.preventDefault(); e.stopPropagation();
		var id = String(btn.getAttribute('data-favorite'));
		var favs = getFavs();
		favs = favs.indexOf(id) > -1 ? favs.filter(function (x) { return x !== id; }) : favs.concat(id);
		setFavs(favs);
		syncFavs();
		var list = $('#favorites-list');
		if (list && btn.closest('#favorites-list')) {
			var card = btn.closest('.property-card');
			if (card && favs.indexOf(id) === -1) card.remove();
			if (!list.children.length) { list.setAttribute('hidden', ''); var em = $('#favorites-empty'); if (em) em.removeAttribute('hidden'); }
		}
	});
	function loadFavoritesPage() {
		var list = $('#favorites-list');
		if (!list) return;
		var favs = getFavs();
		if (!favs.length) return;
		fetch(CFG.ajax + '?action=nvr_favorites&ids=' + encodeURIComponent(favs.join(',')))
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res && res.success && res.data.count) {
					list.innerHTML = res.data.html;
					list.removeAttribute('hidden');
					var em = $('#favorites-empty'); if (em) em.setAttribute('hidden', '');
					syncFavs();
				}
			}).catch(function () {});
	}
	syncFavs();
	loadFavoritesPage();

	/* ------------------------------------------------------------------
	 * Listing: grid / list / map (map loads lazily on first click)
	 * ---------------------------------------------------------------- */
	var mapInstance = null, mapKind = '';
	function loadScript(src, attrs) {
		return new Promise(function (resolve, reject) {
			var s = document.createElement('script');
			s.src = src; s.async = true;
			Object.keys(attrs || {}).forEach(function (k) { s[k] = attrs[k]; });
			s.onload = resolve; s.onerror = reject;
			document.head.appendChild(s);
		});
	}
	function loadLeaflet() {
		if (window.L) return Promise.resolve();
		var l = document.createElement('link'); l.rel = 'stylesheet'; l.href = CFG.maps.leafletCss; document.head.appendChild(l);
		return loadScript(CFG.maps.leafletJs);
	}
	function loadGoogle() {
		if (window.google && window.google.maps) return Promise.resolve();
		return new Promise(function (resolve, reject) {
			var done = false;
			var timer = setTimeout(function () { if (!done) reject(new Error('timeout')); }, 9000);
			window.__nvrGmaps = function () { done = true; clearTimeout(timer); resolve(); };
			window.gm_authFailure = function () { done = true; clearTimeout(timer); reject(new Error('auth')); };
			loadScript('https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(CFG.maps.key) + '&callback=__nvrGmaps&loading=async&language=vi&region=VN').catch(reject);
		});
	}
	function popupHtml(p) {
		return '<div class="map-popup">' + (p.image ? '<img src="' + esc(p.image) + '" alt="" width="200" height="130">' : '') +
			'<strong>' + esc(p.title) + '</strong><span>' + esc(p.address) + '</span><b>' + esc(p.price) + '</b><a href="' + esc(p.url) + '">Xem chi tiết →</a></div>';
	}
	function buildLeaflet(el, points) {
		var map = window.L.map(el, { scrollWheelZoom: false }).setView([points[0].lat, points[0].lng], 12);
		window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
		var b = [];
		points.forEach(function (p) {
			window.L.marker([p.lat, p.lng]).addTo(map).bindPopup(popupHtml(p));
			b.push([p.lat, p.lng]);
		});
		if (b.length > 1) map.fitBounds(b, { padding: [30, 30] });
		mapKind = 'leaflet';
		return map;
	}
	function buildGoogle(el, points) {
		var g = window.google.maps;
		var map = new g.Map(el, { center: { lat: points[0].lat, lng: points[0].lng }, zoom: 12, mapTypeControl: false, streetViewControl: false });
		var bounds = new g.LatLngBounds(), info = new g.InfoWindow();
		points.forEach(function (p) {
			var pos = { lat: p.lat, lng: p.lng };
			var m = new g.Marker({ position: pos, map: map, title: p.title });
			m.addListener('click', function () { info.setContent(popupHtml(p)); info.open({ map: map, anchor: m }); });
			bounds.extend(pos);
		});
		if (points.length > 1) map.fitBounds(bounds);
		mapKind = 'google';
		return map;
	}
	function initMap() {
		var el = $('#nvr-property-map'), data = $('#nvr-map-data');
		if (!el || !data) return;
		if (mapInstance) {
			if (mapKind === 'leaflet') mapInstance.invalidateSize();
			else if (window.google) window.google.maps.event.trigger(mapInstance, 'resize');
			return;
		}
		var points = [];
		try { points = JSON.parse(data.textContent || '[]'); } catch (e) {}
		if (!points.length) { el.innerHTML = '<div class="empty-state">Chưa có tin đăng nào gắn tọa độ.</div>'; return; }
		el.classList.add('is-loading');
		var fallback = function () { return loadLeaflet().then(function () { mapInstance = buildLeaflet(el, points); }); };
		var p = (CFG.maps && CFG.maps.key)
			? loadGoogle().then(function () { mapInstance = buildGoogle(el, points); }).catch(function () { el.innerHTML = ''; el.removeAttribute('class'); return fallback(); })
			: fallback();
		p.then(function () { el.classList.remove('is-loading'); }).catch(function () { el.innerHTML = '<div class="empty-state">Không tải được bản đồ. Vui lòng thử lại.</div>'; });
	}
	$$('[data-view]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var results = $('#archive-results'), map = $('#map-wrap');
			if (!results) return;
			var view = btn.getAttribute('data-view');
			$$('[data-view]').forEach(function (b) { var on = b === btn; b.classList.toggle('active', on); b.setAttribute('aria-pressed', String(on)); });
			results.hidden = view === 'map';
			results.classList.toggle('list-view', view === 'list');
			var pag = $('.pagination'); if (pag) pag.hidden = view === 'map';
			if (map) { map.hidden = view !== 'map'; if (view === 'map') initMap(); }
		});
	});

	/* Mobile filter drawer */
	function toggleFilter(force) {
		var box = $('#property-filter'), bd = $('.filter-backdrop');
		if (!box) return;
		var open = typeof force === 'boolean' ? force : !box.classList.contains('filter-open');
		box.classList.toggle('filter-open', open);
		if (bd) bd.hidden = !open;
		document.body.classList.toggle('filter-lock', open);
		$$('.filter-mobile-toggle').forEach(function (b) { b.setAttribute('aria-expanded', String(open)); });
	}
	$$('[data-filter-toggle]').forEach(function (b) { b.addEventListener('click', function () { toggleFilter(); }); });

	/* ------------------------------------------------------------------
	 * Gallery (arrows, thumbs, keyboard, swipe)
	 * ---------------------------------------------------------------- */
	$$('[data-gallery]').forEach(function (g) {
		var slides = $$('.gallery-slide', g), thumbs = $$('.gallery-thumb', g), count = $('.gallery-count', g);
		if (slides.length < 2) return;
		var i = 0;
		function show(n) {
			i = (n + slides.length) % slides.length;
			slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); });
			thumbs.forEach(function (t, k) { t.classList.toggle('is-active', k === i); });
			if (count) count.textContent = (i + 1) + ' / ' + slides.length;
			var t = thumbs[i]; if (t && t.scrollIntoView) { var bar = t.parentNode; bar.scrollLeft = t.offsetLeft - bar.clientWidth / 2 + t.clientWidth / 2; }
		}
		var prev = $('.gallery-arrow.prev', g), next = $('.gallery-arrow.next', g);
		if (prev) prev.addEventListener('click', function () { show(i - 1); });
		if (next) next.addEventListener('click', function () { show(i + 1); });
		thumbs.forEach(function (t, k) { t.addEventListener('click', function () { show(k); }); });
		g.setAttribute('tabindex', '0');
		g.addEventListener('keydown', function (e) { if (e.key === 'ArrowLeft') show(i - 1); if (e.key === 'ArrowRight') show(i + 1); });
		var x0 = null;
		g.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		g.addEventListener('touchend', function (e) {
			if (x0 === null) return;
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) show(i + (dx < 0 ? 1 : -1));
			x0 = null;
		}, { passive: true });
	});

	/* Share */
	$$('[data-share]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (navigator.share) { navigator.share({ title: document.title, url: location.href }).catch(function () {}); }
			else if (navigator.clipboard) { navigator.clipboard.writeText(location.href).then(function () { btn.textContent = '✓ Đã sao chép link'; }); }
		});
	});

	/* ------------------------------------------------------------------
	 * Image fallback: never show a broken image icon.
	 * ---------------------------------------------------------------- */
	function fixImg(img) {
		if (img.dataset.nvrFallback || !CFG.placeholder) return;
		img.dataset.nvrFallback = '1';
		img.removeAttribute('srcset'); img.removeAttribute('sizes');
		img.src = CFG.placeholder;
	}
	document.addEventListener('error', function (e) { if (e.target && e.target.tagName === 'IMG') fixImg(e.target); }, true);
	$$('img').forEach(function (img) { if (img.getAttribute('loading') !== 'lazy' && img.complete && img.naturalWidth === 0 && img.getAttribute('src')) fixImg(img); });
})();
