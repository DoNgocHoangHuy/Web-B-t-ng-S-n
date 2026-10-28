<?php
/**
 * Contact / lead form.
 *  - Shortcode [nvr_contact_form source="..." compact="1"]
 *  - POST handled on template_redirect (Post/Redirect/Get) -> redirects to /cam-on/
 *  - Lead saved in WP (private CPT) + e-mailed to admin, with honeypot, time trap and rate limit.
 */
if (!defined('ABSPATH')) exit;

function nvr_form_messages() {
	return [
		'expired'  => 'Phiên gửi đã hết hạn. Vui lòng tải lại trang và thử lại.',
		'invalid'  => 'Vui lòng nhập họ tên và số điện thoại hợp lệ (9–11 số).',
		'email'    => 'Địa chỉ email chưa hợp lệ.',
		'spam'     => 'Không thể xử lý yêu cầu. Vui lòng thử lại sau ít phút.',
		'limit'    => 'Bạn gửi quá nhiều yêu cầu. Vui lòng gọi hotline để được hỗ trợ nhanh hơn.',
		'date'     => 'Vui lòng chọn ngày hẹn hợp lệ.',
	];
}

/** Unique counter shared by every form on the page (ids must not collide). */
function nvr_next_form_index() {
	static $i = 0;
	return ++$i;
}

/** Radio "chips" group. $options = list of strings. */
function nvr_chip_group($name, $legend, $options, $uid) {
	$out = '<fieldset class="chip-group"><legend>' . esc_html($legend) . '</legend><div class="chip-row">';
	foreach ($options as $i => $opt) {
		$out .= '<label class="chip-opt"><input type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($opt) . '"><span>' . esc_html($opt) . '</span></label>';
	}
	return $out . '</div></fieldset>';
}

function nvr_contact_form_html($args = []) {
	$form_index = nvr_next_form_index();
	$args = wp_parse_args($args, ['source' => '', 'compact' => false, 'extended' => false, 'ref' => 0, 'button' => 'Gửi yêu cầu tư vấn', 'message_required' => false, 'consign' => false]);
	$ref = $args['ref'] ? (int) $args['ref'] : (is_singular() ? get_queried_object_id() : 0);
	$notice = '';
	if (isset($_GET['nvr_error']) && (!isset($_GET['nvr_kind']) || $_GET['nvr_kind'] !== 'booking') && empty($GLOBALS['nvr_notice_shown'])) {
		$GLOBALS['nvr_notice_shown'] = true;
		$msgs = nvr_form_messages();
		$code = sanitize_key(wp_unslash($_GET['nvr_error']));
		if (isset($msgs[$code])) $notice = '<div class="notice notice-error" role="alert">' . esc_html($msgs[$code]) . '</div>';
	}
	$uid = 'f' . $form_index;
	ob_start();
	echo $notice;
	?>
	<form method="post" class="contact-form<?php echo $args['compact'] ? ' is-compact' : ''; ?><?php echo $args['extended'] ? ' is-extended' : ''; ?>" action="" data-track-submit="submit_form" novalidate>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('user', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-name">Họ và tên</label>
			<input id="<?php echo esc_attr($uid); ?>-name" name="contact_name" type="text" autocomplete="name" placeholder="Họ và tên *" required></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('phone', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-phone">Số điện thoại</label>
			<input id="<?php echo esc_attr($uid); ?>-phone" name="contact_phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="Số điện thoại *" required></div>
		<?php if (!$args['extended']) : ?>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('mail', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-email">Email</label>
			<input id="<?php echo esc_attr($uid); ?>-email" name="contact_email" type="email" autocomplete="email" placeholder="Email (không bắt buộc)"></div>
		<?php endif; ?>
		<?php if ($args['extended']) : ?>
		<?php echo nvr_chip_group('need_type', 'Bạn đang quan tâm đến', ['Mua để ở', 'Mua đầu tư', 'Thuê nhà', 'Ký gửi BĐS'], $uid); ?>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('calc', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-budget">Ngân sách</label>
			<select id="<?php echo esc_attr($uid); ?>-budget" name="budget"><option value="">Ngân sách dự kiến</option><option>Dưới 2 tỷ</option><option>2 – 4 tỷ</option><option>4 – 7 tỷ</option><option>7 – 12 tỷ</option><option>Trên 12 tỷ</option><option>Chưa xác định</option></select></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('pin', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-areapref">Khu vực quan tâm</label>
			<input id="<?php echo esc_attr($uid); ?>-areapref" name="area_pref" type="text" autocomplete="off" placeholder="Khu vực quan tâm (VD: Thủ Đức, Quận 7)"></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('mail', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-email">Email</label>
			<input id="<?php echo esc_attr($uid); ?>-email" name="contact_email" type="email" autocomplete="email" placeholder="Email (không bắt buộc)"></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('clock', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-calltime">Giờ gọi lại</label>
			<select id="<?php echo esc_attr($uid); ?>-calltime" name="call_time"><option value="">Giờ tiện gọi lại</option><option>Bất kỳ lúc nào</option><option>Sáng (8h – 11h)</option><option>Chiều (13h – 17h)</option><option>Tối (18h – 20h)</option></select></div>
		<?php endif; ?>
		<?php if ($args['consign']) : $ptypes = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]); ?>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('home', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-deal">Nhu cầu</label>
			<select id="<?php echo esc_attr($uid); ?>-deal" name="consign_deal"><option value="Bán">Tôi muốn bán</option><option value="Cho thuê">Tôi muốn cho thuê</option></select></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('building', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-type">Loại bất động sản</label>
			<select id="<?php echo esc_attr($uid); ?>-type" name="consign_type"><option value="">Loại bất động sản</option><?php if (!is_wp_error($ptypes)) foreach ($ptypes as $t) echo '<option value="' . esc_attr($t->name) . '">' . esc_html($t->name) . '</option>'; ?></select></div>
		<div class="field field-wide"><span class="field-ico"><?php echo nvr_icon('pin', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-addr">Địa chỉ bất động sản</label>
			<input id="<?php echo esc_attr($uid); ?>-addr" name="consign_address" type="text" autocomplete="off" placeholder="Địa chỉ bất động sản (đường, phường, quận)"></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('ruler', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-area">Diện tích</label>
			<input id="<?php echo esc_attr($uid); ?>-area" name="consign_area" type="text" inputmode="decimal" placeholder="Diện tích (m²)"></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('calc', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-price">Giá mong muốn</label>
			<input id="<?php echo esc_attr($uid); ?>-price" name="consign_price" type="text" placeholder="Giá mong muốn (VD: 5,2 tỷ)"></div>
		<?php endif; ?>
		<div class="field field-wide field-area"><span class="field-ico"><?php echo nvr_icon('message', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-msg">Lời nhắn</label>
			<textarea id="<?php echo esc_attr($uid); ?>-msg" name="contact_message" rows="3" placeholder="Lời nhắn / nhu cầu cụ thể của bạn (loại hình, số phòng ngủ, tiến độ…)"></textarea></div>
		<div class="nvr-honeypot" aria-hidden="true"><label>Website <input type="text" name="contact_website" tabindex="-1" autocomplete="off"></label></div>
		<input type="hidden" name="nvr_contact_form" value="1">
		<input type="hidden" name="nvr_ts" value="<?php echo esc_attr(time()); ?>">
		<input type="hidden" name="nvr_ref" value="<?php echo esc_attr($ref); ?>">
		<input type="hidden" name="nvr_source" value="<?php echo esc_attr($args['source']); ?>">
		<?php wp_nonce_field('nvr_contact', 'nvr_contact_nonce', false); ?>
		<button class="btn btn-block btn-submit" type="submit"><span><?php echo esc_html($args['button']); ?></span><?php echo nvr_icon('send', 18); ?></button>
		<p class="form-note"><?php echo nvr_icon('lock', 14); ?> Thông tin của bạn chỉ dùng để tư vấn và được bảo mật.</p>
	</form>
	<?php
	return ob_get_clean();
}

/** Floating-label form (trang Liên hệ). Dùng chung bộ xử lý POST với các form khác. */
function nvr_floating_form_html($args = []) {
	$uid = 'f' . nvr_next_form_index();
	$args = wp_parse_args($args, ['source' => 'Trang liên hệ', 'button' => 'Gửi yêu cầu tư vấn']);
	$ref = is_singular() ? get_queried_object_id() : 0;
	$notice = '';
	if (isset($_GET['nvr_error']) && (!isset($_GET['nvr_kind']) || $_GET['nvr_kind'] !== 'booking') && empty($GLOBALS['nvr_notice_shown'])) {
		$GLOBALS['nvr_notice_shown'] = true;
		$msgs = nvr_form_messages();
		$code = sanitize_key(wp_unslash($_GET['nvr_error']));
		if (isset($msgs[$code])) $notice = '<div class="notice notice-error" role="alert">' . esc_html($msgs[$code]) . '</div>';
	}
	ob_start();
	echo $notice;
	?>
	<form method="post" class="lh-form" action="" data-track-submit="submit_form" novalidate>
		<div class="lh-field"><input id="<?php echo esc_attr($uid); ?>-name" name="contact_name" type="text" autocomplete="name" placeholder=" " required><label for="<?php echo esc_attr($uid); ?>-name">Họ và tên *</label></div>
		<div class="lh-field"><input id="<?php echo esc_attr($uid); ?>-phone" name="contact_phone" type="tel" inputmode="tel" autocomplete="tel" placeholder=" " required><label for="<?php echo esc_attr($uid); ?>-phone">Số điện thoại *</label></div>
		<div class="lh-field"><input id="<?php echo esc_attr($uid); ?>-email" name="contact_email" type="email" autocomplete="email" placeholder=" "><label for="<?php echo esc_attr($uid); ?>-email">Email (không bắt buộc)</label></div>
		<div class="lh-field lh-field-area"><textarea id="<?php echo esc_attr($uid); ?>-msg" name="contact_message" rows="3" placeholder=" "></textarea><label for="<?php echo esc_attr($uid); ?>-msg">Nội dung cần tư vấn</label></div>
		<div class="nvr-honeypot" aria-hidden="true"><label>Website <input type="text" name="contact_website" tabindex="-1" autocomplete="off"></label></div>
		<input type="hidden" name="nvr_contact_form" value="1">
		<input type="hidden" name="nvr_ts" value="<?php echo esc_attr(time()); ?>">
		<input type="hidden" name="nvr_ref" value="<?php echo esc_attr($ref); ?>">
		<input type="hidden" name="nvr_source" value="<?php echo esc_attr($args['source']); ?>">
		<?php wp_nonce_field('nvr_contact', 'nvr_contact_nonce', false); ?>
		<button class="lh-submit" type="submit"><span><?php echo esc_html($args['button']); ?></span><?php echo nvr_icon('arrow', 18); ?></button>
		<p class="lh-note"><?php echo nvr_icon('lock', 14); ?> Thông tin của bạn chỉ dùng để tư vấn và được bảo mật.</p>
	</form>
	<?php
	return ob_get_clean();
}

/** Booking ("Đặt lịch") form shown in the floating pop-up. */
function nvr_booking_form_html($args = []) {
	$uid = 'f' . nvr_next_form_index();
	$args = wp_parse_args($args, ['source' => 'Đặt lịch hẹn']);
	$ref = is_singular() ? get_queried_object_id() : 0;
	$notice = '';
	if (isset($_GET['nvr_error'], $_GET['nvr_kind']) && $_GET['nvr_kind'] === 'booking') {
		$msgs = nvr_form_messages();
		$code = sanitize_key(wp_unslash($_GET['nvr_error']));
		if (isset($msgs[$code])) $notice = '<div class="notice notice-error" role="alert">' . esc_html($msgs[$code]) . '</div>';
	}
	$today = wp_date('Y-m-d');
	ob_start();
	echo $notice;
	?>
	<form method="post" class="contact-form is-extended is-booking" action="" data-track-submit="submit_booking" novalidate>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('user', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-name">Họ và tên</label>
			<input id="<?php echo esc_attr($uid); ?>-name" name="contact_name" type="text" autocomplete="name" placeholder="Họ và tên *" required></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('phone', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-phone">Số điện thoại</label>
			<input id="<?php echo esc_attr($uid); ?>-phone" name="contact_phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="Số điện thoại *" required></div>
		<?php echo nvr_chip_group('booking_type', 'Bạn muốn đặt lịch để', ['Xem nhà / dự án', 'Tư vấn qua điện thoại', 'Gặp tại văn phòng'], $uid); ?>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('calendar', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-date">Ngày hẹn</label>
			<input id="<?php echo esc_attr($uid); ?>-date" name="booking_date" type="date" min="<?php echo esc_attr($today); ?>" required></div>
		<div class="field"><span class="field-ico"><?php echo nvr_icon('clock', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-time">Khung giờ</label>
			<select id="<?php echo esc_attr($uid); ?>-time" name="booking_time"><option>Sáng (8h – 11h)</option><option>Chiều (13h – 17h)</option><option>Tối (18h – 20h)</option></select></div>
		<div class="field field-wide field-area"><span class="field-ico"><?php echo nvr_icon('message', 18); ?></span><label class="screen-reader-text" for="<?php echo esc_attr($uid); ?>-msg">Ghi chú</label>
			<textarea id="<?php echo esc_attr($uid); ?>-msg" name="contact_message" rows="3" placeholder="Ghi chú (dự án / căn bạn muốn xem, số người đi cùng…)"></textarea></div>
		<div class="nvr-honeypot" aria-hidden="true"><label>Website <input type="text" name="contact_website" tabindex="-1" autocomplete="off"></label></div>
		<input type="hidden" name="nvr_contact_form" value="1">
		<input type="hidden" name="nvr_kind" value="booking">
		<input type="hidden" name="nvr_ts" value="<?php echo esc_attr(time()); ?>">
		<input type="hidden" name="nvr_ref" value="<?php echo esc_attr($ref); ?>">
		<input type="hidden" name="nvr_source" value="<?php echo esc_attr($args['source']); ?>">
		<?php wp_nonce_field('nvr_contact', 'nvr_contact_nonce', false); ?>
		<button class="btn btn-block btn-submit" type="submit"><span>Xác nhận đặt lịch</span><?php echo nvr_icon('calendar', 18); ?></button>
		<p class="form-note"><?php echo nvr_icon('lock', 14); ?> Chuyên viên sẽ gọi xác nhận lịch hẹn với bạn trong thời gian sớm nhất.</p>
	</form>
	<?php
	return ob_get_clean();
}

function nvr_contact_shortcode($atts) {
	return nvr_contact_form_html(shortcode_atts(['source' => '', 'compact' => '', 'extended' => '', 'ref' => 0, 'button' => 'Gửi yêu cầu tư vấn', 'consign' => ''], $atts, 'nvr_contact_form'));
}
add_shortcode('nvr_contact_form', 'nvr_contact_shortcode');

function nvr_client_ip() {
	return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0';
}

function nvr_handle_contact_post() {
	if (empty($_POST['nvr_contact_form'])) return;
	$back = wp_get_referer();
	if (!$back) $back = home_url('/');
	$back = remove_query_arg(['nvr_error', 'nvr_kind'], $back);
	$kind = (isset($_POST['nvr_kind']) && $_POST['nvr_kind'] === 'booking') ? 'booking' : '';
	$fail = function ($code) use ($back, $kind) {
		wp_safe_redirect(add_query_arg(array_filter(['nvr_error' => $code, 'nvr_kind' => $kind]), $back));
		exit;
	};

	// Anonymous visitors: nonces break on page-cached sites (stale after 12-24h), so rely on honeypot + time trap + rate limit.
	// Logged-in users: verify the nonce as usual.
	if (is_user_logged_in() && (!isset($_POST['nvr_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nvr_contact_nonce'])), 'nvr_contact'))) $fail('expired');
	if (!empty($_POST['contact_website'])) $fail('spam');
	$ts = isset($_POST['nvr_ts']) ? (int) $_POST['nvr_ts'] : 0;
	if ($ts && time() - $ts < 2) $fail('spam');

	$ip_key = 'nvr_rl_' . md5(nvr_client_ip());
	$count = (int) get_transient($ip_key);
	if ($count >= 6) $fail('limit');

	$name = sanitize_text_field(wp_unslash($_POST['contact_name'] ?? ''));
	$phone = sanitize_text_field(wp_unslash($_POST['contact_phone'] ?? ''));
	$email = sanitize_email(wp_unslash($_POST['contact_email'] ?? ''));
	$message = sanitize_textarea_field(wp_unslash($_POST['contact_message'] ?? ''));
	$ref = isset($_POST['nvr_ref']) ? absint($_POST['nvr_ref']) : 0;
	$source = sanitize_text_field(wp_unslash($_POST['nvr_source'] ?? ''));
	$digits = preg_replace('/\D/', '', $phone);
	$extra = '';
	foreach (['consign_deal' => 'Nhu cầu ký gửi', 'consign_type' => 'Loại BĐS', 'consign_address' => 'Địa chỉ BĐS', 'consign_area' => 'Diện tích (m²)', 'consign_price' => 'Giá mong muốn', 'need_type' => 'Nhu cầu', 'budget' => 'Ngân sách', 'area_pref' => 'Khu vực quan tâm', 'call_time' => 'Giờ tiện gọi lại', 'booking_type' => 'Đặt lịch để', 'booking_time' => 'Khung giờ'] as $k => $label) {
		$v = sanitize_text_field(wp_unslash($_POST[$k] ?? ''));
		if ($v !== '') $extra .= $label . ': ' . mb_substr($v, 0, 200) . "\n";
	}

	if ($name === '' || strlen($digits) < 9 || strlen($digits) > 12) $fail('invalid');
	if (!empty($_POST['contact_email']) && !is_email($email)) $fail('email');
	if ($kind === 'booking') {
		$d = sanitize_text_field(wp_unslash($_POST['booking_date'] ?? ''));
		$dt = DateTime::createFromFormat('Y-m-d', $d);
		if (!$dt || $dt->format('Y-m-d') !== $d || $d < wp_date('Y-m-d')) $fail('date');
		$extra = 'Ngày hẹn: ' . $dt->format('d/m/Y') . "\n" . $extra;
	}

	set_transient($ip_key, $count + 1, HOUR_IN_SECONDS);

	$ref_title = $ref ? get_the_title($ref) : '';
	$ref_url = $ref ? get_permalink($ref) : esc_url_raw($back);
	$content = "Họ tên: $name\nĐiện thoại: $phone\nEmail: " . ($email ?: '—') . "\nQuan tâm: " . ($ref_title ?: ($source ?: 'Liên hệ chung')) . "\nTrang gửi: $ref_url\n" . ($extra ? "\n" . $extra : '') . "\nNội dung:\n" . ($message ?: '—');
	$lead_id = wp_insert_post([
		'post_type' => 'nvr_lead', 'post_status' => 'private',
		'post_title' => $name . ' - ' . $phone, 'post_content' => $content,
	]);
	if ($lead_id && !is_wp_error($lead_id)) {
		update_post_meta($lead_id, '_nvr_lead_phone', $phone);
		update_post_meta($lead_id, '_nvr_lead_email', $email);
		update_post_meta($lead_id, '_nvr_lead_ref', $ref);
		update_post_meta($lead_id, '_nvr_lead_source', $source);
	}

	$headers = ['Content-Type: text/plain; charset=UTF-8'];
	if ($email) $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
	wp_mail(nvr_notify_email(), '[' . wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES) . '] Khách hàng mới: ' . $name . ' - ' . $phone, $content, $headers);

	wp_safe_redirect(add_query_arg('ref', $ref ?: 0, nvr_page_url('cam-on')));
	exit;
}
add_action('template_redirect', 'nvr_handle_contact_post');
