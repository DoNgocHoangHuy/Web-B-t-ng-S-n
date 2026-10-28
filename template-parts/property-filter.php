<?php
/** Left sidebar filter (archive + search). Always submits to the property archive. */
$types = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
$locs = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);
$kw = nvr_req('keyword', nvr_req('s'));
?>
<aside class="filter-box" id="property-filter" aria-label="Bộ lọc bất động sản">
	<div class="filter-head"><h2>Bộ lọc</h2><button type="button" class="icon-btn filter-close" data-filter-toggle aria-label="Đóng bộ lọc"><?php echo nvr_icon('close', 22); ?></button></div>
	<form class="filter-form" method="get" action="<?php echo esc_url(nvr_archive_url('property')); ?>">
		<label for="f-keyword">Từ khóa</label>
		<input id="f-keyword" type="search" name="keyword" value="<?php echo esc_attr($kw); ?>" placeholder="Tên, đường, phường, mã tin…">

		<label for="f-deal">Nhu cầu</label>
		<select id="f-deal" name="deal">
			<option value="">Mua bán & Cho thuê</option>
			<option value="sale" <?php selected(nvr_req('deal'), 'sale'); ?>>Mua bán</option>
			<option value="rent" <?php selected(nvr_req('deal'), 'rent'); ?>>Cho thuê</option>
		</select>

		<label for="f-type">Loại bất động sản</label>
		<select id="f-type" name="type"><option value="">Tất cả loại</option>
			<?php foreach ($types as $t) echo '<option value="' . esc_attr($t->slug) . '" ' . selected(nvr_req('type'), $t->slug, false) . '>' . esc_html($t->name) . '</option>'; ?>
		</select>

		<label for="f-location">Khu vực</label>
		<select id="f-location" name="location"><option value="">Tất cả khu vực</option>
			<?php foreach ($locs as $t) echo '<option value="' . esc_attr($t->slug) . '" ' . selected(nvr_req('location'), $t->slug, false) . '>' . esc_html($t->name) . '</option>'; ?>
		</select>

		<label for="f-price">Mức giá</label>
		<select id="f-price" name="price"><option value="">Tất cả mức giá</option>
			<optgroup label="Mua bán">
				<?php foreach (nvr_price_ranges() as $k => $r) if ($r[1] === 'sale') echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('price'), $k, false) . '>' . esc_html($r[0]) . '</option>'; ?>
			</optgroup>
			<optgroup label="Cho thuê">
				<?php foreach (nvr_price_ranges() as $k => $r) if ($r[1] === 'rent') echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('price'), $k, false) . '>' . esc_html($r[0]) . '</option>'; ?>
			</optgroup>
		</select>

		<label for="f-area">Diện tích</label>
		<select id="f-area" name="area"><option value="">Tất cả diện tích</option>
			<?php foreach (nvr_area_ranges() as $k => $l) echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('area'), $k, false) . '>' . esc_html($l) . '</option>'; ?>
		</select>

		<label for="f-beds">Phòng ngủ</label>
		<select id="f-beds" name="beds"><option value="">Bất kỳ</option>
			<?php foreach (['1' => '1 PN', '2' => '2 PN', '3' => '3 PN', '4' => '4 PN trở lên'] as $k => $l) echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('beds'), (string) $k, false) . '>' . esc_html($l) . '</option>'; ?>
		</select>

		<label for="f-direction">Hướng</label>
		<select id="f-direction" name="direction">
			<?php foreach (nvr_direction_options() as $k => $l) echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('direction'), (string) $k, false) . '>' . esc_html($k === '' ? 'Tất cả' : $l) . '</option>'; ?>
		</select>

		<label for="f-legal">Pháp lý</label>
		<select id="f-legal" name="legal">
			<?php foreach (nvr_legal_options() as $k => $l) echo '<option value="' . esc_attr($k) . '" ' . selected(nvr_req('legal'), (string) $k, false) . '>' . esc_html($k === '' ? 'Tất cả' : $l) . '</option>'; ?>
		</select>

		<label for="f-status">Trạng thái</label>
		<select id="f-status" name="status"><option value="">Tất cả</option>
			<?php foreach (['Đang bán', 'Đã bán', 'Đang cho thuê', 'Đã cho thuê'] as $s) echo '<option value="' . esc_attr($s) . '" ' . selected(nvr_req('status'), $s, false) . '>' . esc_html($s) . '</option>'; ?>
		</select>

		<label for="f-sort">Sắp xếp</label>
		<select id="f-sort" name="sort">
			<option value="">Mới nhất</option>
			<option value="price_asc" <?php selected(nvr_req('sort'), 'price_asc'); ?>>Giá tăng dần</option>
			<option value="price_desc" <?php selected(nvr_req('sort'), 'price_desc'); ?>>Giá giảm dần</option>
			<option value="area_desc" <?php selected(nvr_req('sort'), 'area_desc'); ?>>Diện tích lớn nhất</option>
		</select>

		<button class="btn btn-block" type="submit"><?php echo nvr_icon('search', 18); ?> Áp dụng bộ lọc</button>
		<a class="reset-link" href="<?php echo esc_url(nvr_archive_url('property')); ?>">Xóa bộ lọc</a>
	</form>
</aside>
