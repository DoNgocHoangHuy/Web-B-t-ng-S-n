<?php
/**
 * Template: Tính khoản vay mua nhà (slug: tinh-vay)
 * Tính ở trình duyệt (không gửi dữ liệu đi đâu). Hỗ trợ trả đều hàng tháng (niên kim), gốc đều, và lãi suất ưu đãi giai đoạn đầu.
 * Có thể điền sẵn giá bằng ?price=3000000000 (nút "Tính khoản vay" trên trang chi tiết bất động sản dùng cách này).
 */
get_header();
while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([[get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
	<p class="lead">Ước tính số tiền trả mỗi tháng, tổng lãi và lịch trả nợ theo năm trước khi làm việc với ngân hàng.</p>
</div></section>

<div class="container"><div class="calc-layout section">
	<form class="detail-card calc-form" id="nvr-calc" onsubmit="return false" novalidate>
		<h2 class="card-title">Thông tin khoản vay</h2>
		<div class="calc-field"><label for="c-price">Giá trị bất động sản (VND)</label>
			<input id="c-price" type="text" inputmode="numeric" autocomplete="off" value="3.000.000.000"><small id="c-price-h"></small></div>
		<div class="calc-field"><label for="c-ltv">Tỷ lệ vay: <output id="c-ltv-o">70%</output></label>
			<input id="c-ltv" type="range" min="10" max="90" step="5" value="70"></div>
		<div class="calc-two">
			<div class="calc-field"><label for="c-term">Thời hạn vay (năm)</label><input id="c-term" type="number" min="1" max="35" step="1" value="20"></div>
			<div class="calc-field"><label for="c-rate">Lãi suất (%/năm)</label><input id="c-rate" type="number" min="0" max="30" step="0.1" value="9"></div>
		</div>
		<div class="calc-field"><label for="c-method">Hình thức trả nợ</label>
			<select id="c-method"><option value="annuity">Trả đều hàng tháng (gốc + lãi cố định)</option><option value="equal">Gốc đều (tiền trả giảm dần)</option></select></div>
		<details class="calc-promo"><summary>Có lãi suất ưu đãi giai đoạn đầu</summary>
			<div class="calc-two">
				<div class="calc-field"><label for="c-promo">Thời gian ưu đãi (tháng)</label><input id="c-promo" type="number" min="0" max="120" step="1" value="0"></div>
				<div class="calc-field"><label for="c-rate1">Lãi suất ưu đãi (%/năm)</label><input id="c-rate1" type="number" min="0" max="30" step="0.1" value="7"></div>
			</div>
			<small>Sau thời gian ưu đãi, hệ thống áp dụng lãi suất ở ô "Lãi suất" phía trên.</small>
		</details>
	</form>

	<div class="calc-side">
		<section class="calc-result" aria-live="polite">
			<div class="calc-main"><span>Số tiền trả mỗi tháng</span><strong id="r-monthly">—</strong><small id="r-monthly-note"></small></div>
			<dl class="calc-grid">
				<div><dt>Số tiền vay</dt><dd id="r-loan">—</dd></div>
				<div><dt>Tiền tự có (trả trước)</dt><dd id="r-down">—</dd></div>
				<div><dt>Tổng lãi phải trả</dt><dd id="r-interest">—</dd></div>
				<div><dt>Tổng gốc + lãi</dt><dd id="r-total">—</dd></div>
			</dl>
		</section>
		<section class="calc-table-wrap">
			<h2>Lịch trả nợ theo năm</h2>
			<div class="calc-scroll"><table class="calc-table"><thead><tr><th>Năm</th><th>Tổng trả trong năm</th><th>Gốc</th><th>Lãi</th><th>Dư nợ cuối năm</th></tr></thead><tbody id="r-rows"></tbody></table></div>
		</section>
		<p class="disclaimer">Kết quả chỉ mang tính tham khảo. Lãi suất, phí và điều kiện thực tế phụ thuộc từng ngân hàng và hồ sơ của bạn. Vui lòng xác nhận lại với ngân hàng trước khi quyết định.</p>
	</div>
</div></div>

<section class="section soft calc-cta"><div class="container narrow">
	<h2>Cần tư vấn gói vay phù hợp?</h2>
	<p class="muted">Để lại thông tin, chuyên viên sẽ liên hệ giúp bạn tham khảo phương án tài chính và kiểm tra hồ sơ pháp lý của bất động sản.</p>
	<?php echo nvr_contact_form_html(['source' => 'Công cụ tính vay', 'compact' => true, 'button' => 'Nhận tư vấn gói vay']); ?>
</div></section>

<script>
(function () {
	var $ = function (id) { return document.getElementById(id); };
	var nf = new Intl.NumberFormat('vi-VN');
	var vnd = function (x) { return nf.format(Math.round(x)) + ' ₫'; };
	var human = function (x) {
		if (x >= 1e9) return (x / 1e9).toFixed(2).replace('.', ',') + ' tỷ đồng';
		if (x >= 1e6) return (x / 1e6).toFixed(1).replace('.', ',') + ' triệu đồng';
		return nf.format(Math.round(x)) + ' đồng';
	};
	var intOf = function (s) { return parseInt(String(s).replace(/\D/g, ''), 10) || 0; };
	var numOf = function (el) { var v = parseFloat(el.value); return isFinite(v) ? v : 0; };
	var touched = false;
	var msg = document.querySelector('.calc-cta textarea[name="contact_message"]');
	if (msg) msg.addEventListener('input', function () { touched = true; });

	function calc() {
		var price = intOf($('c-price').value);
		var ltv = numOf($('c-ltv'));
		var years = Math.min(35, Math.max(1, Math.round(numOf($('c-term')) || 1)));
		var n = years * 12;
		var r2 = Math.max(0, numOf($('c-rate'))) / 100 / 12;
		var promoM = Math.min(n, Math.max(0, Math.round(numOf($('c-promo')))));
		var r1 = promoM ? Math.max(0, numOf($('c-rate1'))) / 100 / 12 : r2;
		var method = $('c-method').value;
		var loan = price * ltv / 100, bal = loan, totalInt = 0, pmt = 0, first = 0, afterPromo = 0, last = 0, years_ = {};
		$('c-ltv-o').textContent = ltv + '%';
		$('c-price-h').textContent = price ? human(price) : '';
		for (var m = 0; m < n; m++) {
			var r = m < promoM ? r1 : r2, interest = bal * r, principal, pay;
			if (method === 'annuity') {
				if (m === 0 || m === promoM) { var rem = n - m; pmt = r === 0 ? bal / rem : bal * r / (1 - Math.pow(1 + r, -rem)); }
				pay = pmt; principal = pay - interest;
			} else { principal = loan / n; pay = principal + interest; }
			if (principal > bal) principal = bal;
			bal -= principal; totalInt += interest;
			if (m === 0) first = pay;
			if (m === promoM) afterPromo = pay;
			last = pay;
			var y = Math.floor(m / 12);
			years_[y] = years_[y] || { pay: 0, pr: 0, it: 0, bal: 0 };
			years_[y].pay += pay; years_[y].pr += principal; years_[y].it += interest; years_[y].bal = bal;
		}
		var note = '';
		if (method === 'equal') note = 'Tháng đầu tiên. Số tiền giảm dần, tháng cuối khoảng ' + vnd(last) + '.';
		else if (promoM && promoM < n) note = 'Trong ' + promoM + ' tháng ưu đãi. Sau đó khoảng ' + vnd(afterPromo) + '/tháng.';
		else note = 'Cố định trong suốt ' + years + ' năm.';
		$('r-monthly').textContent = loan > 0 ? vnd(first) : '—';
		$('r-monthly-note').textContent = loan > 0 ? note : '';
		$('r-loan').textContent = vnd(loan);
		$('r-down').textContent = vnd(price - loan);
		$('r-interest').textContent = vnd(totalInt);
		$('r-total').textContent = vnd(loan + totalInt);
		var rows = '';
		Object.keys(years_).forEach(function (k) {
			var o = years_[k];
			rows += '<tr><td>' + (+k + 1) + '</td><td>' + nf.format(Math.round(o.pay)) + '</td><td>' + nf.format(Math.round(o.pr)) + '</td><td>' + nf.format(Math.round(o.it)) + '</td><td>' + nf.format(Math.max(0, Math.round(o.bal))) + '</td></tr>';
		});
		$('r-rows').innerHTML = loan > 0 ? rows : '';
		if (msg && !touched && price) {
			msg.value = 'Tôi cần tư vấn vay mua nhà: giá trị ' + human(price) + ', vay ' + ltv + '% (' + human(loan) + '), thời hạn ' + years + ' năm, lãi suất ' + (numOf($('c-rate'))) + '%/năm.';
		}
	}

	$('c-price').addEventListener('input', function () {
		var v = intOf(this.value);
		this.value = v ? nf.format(v) : '';
		calc();
	});
	['c-ltv', 'c-term', 'c-rate', 'c-method', 'c-promo', 'c-rate1'].forEach(function (id) {
		$(id).addEventListener('input', calc);
		$(id).addEventListener('change', calc);
	});
	try {
		var p = parseInt(new URLSearchParams(window.location.search).get('price'), 10);
		if (p > 0) $('c-price').value = nf.format(p);
	} catch (e) {}
	calc();
})();
</script>
<?php endwhile; get_footer(); ?>
