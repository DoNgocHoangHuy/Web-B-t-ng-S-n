<?php
/**
 * Template: Về chúng tôi (slug: ve-chung-toi)
 * WordPress tự dùng file này cho trang có đường dẫn /ve-chung-toi/ nên không phụ thuộc nội dung đã lưu trong database.
 * Dữ liệu thật (đội ngũ, nhận xét, mốc phát triển, số liệu, năm thành lập) nhập tại Cài đặt > Nhà Việt Realty.
 */
get_header();

$brand   = 'NhàViệt';
$company = nvr_opt('company') ?: 'Nhà Việt Realty';
$address = nvr_opt('address');
$email   = nvr_public_email();
$hours   = nvr_opt('hours');
$founded = preg_match('/^(19|20)\d{2}$/', nvr_opt('founded')) ? (int) nvr_opt('founded') : 0;

/* ---- Số liệu: ưu tiên số do bạn tự nhập; không có thì đếm thật từ website ---- */
$stats = [];
foreach (nvr_opt_rows('stats_custom', 2) as $r) { if ($r[0] !== '' && $r[1] !== '') $stats[] = [$r[0], $r[1]]; }
if (!$stats) {
	$count = function ($type) { $c = wp_count_posts($type); return isset($c->publish) ? (int) $c->publish : 0; };
	foreach ([['property', 'Tin đăng bất động sản'], ['project', 'Dự án được giới thiệu'], ['developer', 'Chủ đầu tư'], ['neighborhood', 'Khu dân cư']] as $t) {
		$n = $count($t[0]);
		if ($n > 0) $stats[] = [number_format_i18n($n), $t[1]];
	}
	if ($founded && (int) date_i18n('Y') - $founded >= 1) array_unshift($stats, [(string) ((int) date_i18n('Y') - $founded), 'Năm hoạt động']);
}

/* ---- Đội ngũ ---- */
$team = [];
foreach (nvr_opt_rows('team', 5) as $r) { if ($r[0] !== '') $team[] = ['name' => $r[0], 'role' => $r[1], 'phone' => $r[2], 'photo' => $r[3], 'bio' => $r[4]]; }
$team_is_real = (bool) $team;
if (!$team) {
	$team = [
		['name' => '', 'role' => 'Chuyên viên tư vấn mua bán', 'phone' => '', 'photo' => '', 'bio' => 'Tư vấn căn hộ, nhà phố, biệt thự và đất nền theo ngân sách, khu vực và mục đích của bạn.'],
		['name' => '', 'role' => 'Chuyên viên tư vấn cho thuê', 'phone' => '', 'photo' => '', 'bio' => 'Tìm căn hộ, studio, officetel hoặc văn phòng nhỏ phù hợp thời hạn thuê và chi phí hằng tháng.'],
		['name' => '', 'role' => 'Hỗ trợ pháp lý & vay vốn', 'phone' => '', 'photo' => '', 'bio' => 'Định hướng kiểm tra sổ hồng, quy hoạch và tham khảo phương án tài chính trước khi đặt cọc.'],
	];
}

/* ---- Mốc phát triển & nhận xét (chỉ hiện khi bạn đã nhập) ---- */
$milestones = array_values(array_filter(nvr_opt_rows('milestones', 3), function ($r) { return $r[0] !== '' && $r[1] !== ''; }));
$testimonials = array_values(array_filter(nvr_opt_rows('testimonials', 3), function ($r) { return $r[0] !== '' && $r[2] !== ''; }));

$services = [
	['building', 'Mua bán',             'Căn hộ, nhà phố, biệt thự và đất nền tại Quận 1, Quận 7, Bình Thạnh, Thủ Đức, Nhà Bè và các khu vực lân cận.', nvr_archive_url('property', ['deal' => 'sale'])],
	['pin',      'Cho thuê',            'Studio, căn hộ, officetel và văn phòng nhỏ, linh hoạt theo hợp đồng 6–12 tháng.', nvr_archive_url('property', ['deal' => 'rent'])],
	['grid',     'Dự án & chủ đầu tư',  'Tổng hợp thông tin dự án đang được quan tâm cùng đơn vị phát triển, tiện ích và tiến độ.', nvr_archive_url('project')],
	['map',      'Khu dân cư',          'Giới thiệu vị trí, tiện ích, hạ tầng và kết nối giao thông của từng khu vực.', nvr_archive_url('neighborhood')],
	['calc',     'Tính khoản vay',      'Ước tính số tiền trả mỗi tháng, tổng lãi và lịch trả nợ trước khi làm việc với ngân hàng.', nvr_page_url('tinh-vay')],
	['home',     'Ký gửi bất động sản', 'Chủ nhà muốn bán hoặc cho thuê có thể gửi thông tin để được tư vấn và định hướng giá.', nvr_page_url('ky-gui')],
];

$steps = [
	['Tiếp nhận nhu cầu',         'Lắng nghe ngân sách, khu vực, loại hình và mục đích: ở thực, cho thuê hay đầu tư.'],
	['Chọn lọc sản phẩm',         'Lọc ra các lựa chọn phù hợp và gửi thông tin chi tiết để bạn dễ so sánh.'],
	['Xem thực tế & kiểm tra',    'Dẫn xem nhà, cùng rà soát hiện trạng, pháp lý và quy hoạch khu vực.'],
	['Thương lượng & hoàn tất',   'Hỗ trợ thương lượng giá, đặt cọc và các thủ tục cần thiết để giao dịch an toàn.'],
	['Đồng hành sau giao dịch',   'Giải đáp vướng mắc sau bàn giao và hỗ trợ khi bạn có nhu cầu tiếp theo.'],
];

$values = [
	['Minh bạch',      'Thông tin rõ ràng, nêu cụ thể những gì cần xác minh trước khi quyết định.'],
	['Tận tâm',        'Lắng nghe nhu cầu thực của từng khách hàng, không thúc ép quyết định.'],
	['Chuyên nghiệp',  'Phản hồi nhanh, làm việc theo quy trình và có người phụ trách từ đầu đến cuối.'],
	['Bảo mật',        'Thông tin cá nhân chỉ dùng để tư vấn và không chia sẻ cho bên thứ ba khi chưa có sự đồng ý.'],
];

$faqs = [
	['Tôi cần chuẩn bị gì khi liên hệ tư vấn?', 'Bạn chỉ cần cho biết khu vực, ngân sách dự kiến, loại hình mong muốn và mục đích (ở hoặc đầu tư). Chuyên viên sẽ hỏi thêm nếu cần.'],
	['Thông tin trên website có chính xác không?', 'Giá, diện tích và pháp lý được trình bày mang tính tham khảo tại thời điểm đăng. Trước khi giao dịch, bạn nên kiểm tra giấy tờ, quy hoạch và hợp đồng từ nguồn có thẩm quyền.'],
	['Tôi có phải trả phí khi gửi yêu cầu tư vấn không?', 'Việc gửi yêu cầu và nhận tư vấn ban đầu không mất phí. Mọi chi phí liên quan đến giao dịch (nếu có) sẽ được trao đổi rõ ràng trước khi bạn quyết định.'],
	['Bao lâu tôi nhận được phản hồi?', 'Chúng tôi phản hồi trong thời gian sớm nhất, thường trong vòng 30 phút vào giờ hành chính. Bạn cũng có thể gọi hotline hoặc nhắn Zalo để được hỗ trợ ngay.'],
	['Tôi là chủ nhà, có thể nhờ bán hoặc cho thuê không?', 'Được. Bạn gửi thông tin tại trang Ký gửi bất động sản, chuyên viên sẽ liên hệ để trao đổi về giá, hồ sơ pháp lý và cách giới thiệu tài sản.'],
];

/* ---- Dữ liệu có cấu trúc (AboutPage + tổ chức + câu hỏi thường gặp) ---- */
$pid = get_queried_object_id();
$org_id = home_url('/') . '#organization';
$org = ['@type' => 'RealEstateAgent', '@id' => $org_id, 'name' => $company, 'url' => home_url('/'), 'telephone' => nvr_hotline(), 'areaServed' => 'TP. Hồ Chí Minh', 'address' => ['@type' => 'PostalAddress', 'streetAddress' => $address, 'addressCountry' => 'VN']];
if ($email) $org['email'] = $email;
if ($founded) $org['foundingDate'] = (string) $founded;
if (nvr_opt('facebook') && nvr_opt('facebook') !== 'https://www.facebook.com/') $org['sameAs'] = [nvr_facebook_url()];
if (has_custom_logo()) { $logo = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full'); if ($logo) $org['logo'] = $logo; }
$faq_ld = [];
foreach ($faqs as $f) $faq_ld[] = ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]];
$ld = ['@context' => 'https://schema.org', '@graph' => [
	['@type' => 'AboutPage', '@id' => get_permalink($pid) . '#about', 'url' => get_permalink($pid), 'name' => get_the_title($pid), 'inLanguage' => 'vi', 'description' => nvr_page_description(), 'about' => ['@id' => $org_id]],
	$org,
	['@type' => 'FAQPage', 'mainEntity' => $faq_ld],
]];

while (have_posts()) : the_post(); ?>
<script type="application/ld+json"><?php echo wp_json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

<section class="page-hero about-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([[get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
	<p class="lead">Không gian sống tốt bắt đầu từ lựa chọn đúng. <?php echo esc_html($brand); ?> đồng hành cùng bạn từ lúc tìm hiểu đến khi hoàn tất giao dịch, bằng thông tin rõ ràng và sự tư vấn tận tâm.</p>
</div></section>

<section class="section about-intro">
	<div class="container about-split">
		<div>
			<div class="eyebrow dark">Câu chuyện của chúng tôi</div>
			<h2>Gom thông tin bất động sản về một nơi, rõ ràng và dễ kiểm chứng</h2>
			<p><?php echo esc_html($company); ?> là nền tảng giới thiệu và kết nối bất động sản tại TP. Hồ Chí Minh và các khu vực lân cận. Chúng tôi giúp người mua, người thuê và nhà đầu tư tìm đúng sản phẩm bằng thông tin minh bạch, dễ so sánh và đội ngũ tư vấn đồng hành đến khi quyết định.</p>
			<p>Tìm nhà là một trong những quyết định lớn nhất của mỗi gia đình. Thông tin rải rác, giá không thống nhất và pháp lý khó kiểm chứng khiến nhiều người mất rất nhiều thời gian. <?php echo esc_html($brand); ?> ra đời để giải quyết điều đó: tin đăng có đầy đủ địa chỉ, diện tích, giá, hướng nhà, pháp lý; dự án có chủ đầu tư và tiện ích; khu dân cư có đặc điểm hạ tầng rõ ràng.</p>
			<div class="about-actions">
				<a class="btn" href="<?php echo esc_url(nvr_page_url('lien-he')); ?>">Liên hệ tư vấn <?php echo nvr_icon('arrow', 16); ?></a>
				<a class="btn btn-outline" href="<?php echo esc_url(nvr_archive_url('property')); ?>">Xem bất động sản</a>
			</div>
		</div>
		<figure class="about-photo">
			<?php $u = nvr_pool_urls('city', 1, 0, 1200); echo nvr_img($u[0], 'Thành phố và các khu dân cư nhà ở', 1200, 900, ['sizes' => '(max-width: 760px) 100vw, 520px']); ?>
		</figure>
	</div>
</section>

<?php if ($stats) : ?>
<section class="about-stats" aria-label="Con số nổi bật"><div class="container">
	<div class="about-stats-grid">
		<?php foreach ($stats as $s) : ?>
			<div><strong><?php echo esc_html($s[0]); ?></strong><span><?php echo esc_html($s[1]); ?></span></div>
		<?php endforeach; ?>
	</div>
</div></section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<div class="section-head"><div><h2>Sứ mệnh, tầm nhìn &amp; cam kết</h2><p>Điều định hướng mọi việc chúng tôi làm mỗi ngày.</p></div></div>
		<div class="about-cards three">
			<article><span class="about-ico"><?php echo nvr_icon('award', 24); ?></span><h3>Sứ mệnh</h3><p>Đơn giản hóa hành trình tìm kiếm bất động sản, giúp khách hàng quyết định dựa trên thông tin minh bạch thay vì phỏng đoán.</p></article>
			<article><span class="about-ico"><?php echo nvr_icon('map', 24); ?></span><h3>Tầm nhìn</h3><p>Trở thành địa chỉ tìm kiếm và tư vấn bất động sản đáng tin cậy tại khu vực phía Nam, được khách hàng giới thiệu cho người thân.</p></article>
			<article><span class="about-ico"><?php echo nvr_icon('shield', 24); ?></span><h3>Cam kết</h3><p>Tư vấn rõ ràng, không hứa hẹn lợi nhuận, luôn nêu những điểm cần xác minh và tôn trọng quyết định cuối cùng của bạn.</p></article>
		</div>
	</div>
</section>

<?php if ($milestones) : ?>
<section class="section soft">
	<div class="container">
		<div class="section-head"><div><h2>Hành trình phát triển</h2><p>Những cột mốc đáng nhớ của <?php echo esc_html($brand); ?>.</p></div></div>
		<ol class="about-timeline">
			<?php foreach ($milestones as $m) : ?>
				<li><span class="about-year"><?php echo esc_html($m[0]); ?></span><div><h3><?php echo esc_html($m[1]); ?></h3><?php if ($m[2] !== '') : ?><p><?php echo esc_html($m[2]); ?></p><?php endif; ?></div></li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<section class="section<?php echo $milestones ? '' : ' soft'; ?>">
	<div class="container">
		<div class="section-head"><div><h2>Chúng tôi hỗ trợ bạn những gì?</h2><p>Từ tìm kiếm ban đầu đến thủ tục pháp lý, bạn đều có người đồng hành.</p></div></div>
		<div class="about-cards three">
			<?php foreach ($services as $sv) : ?>
				<article><span class="about-ico"><?php echo nvr_icon($sv[0], 24); ?></span><h3><a href="<?php echo esc_url($sv[3]); ?>"><?php echo esc_html($sv[1]); ?></a></h3><p><?php echo esc_html($sv[2]); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section<?php echo $milestones ? ' soft' : ''; ?>">
	<div class="container">
		<div class="section-head"><div><h2>Quy trình làm việc</h2><p>Năm bước rõ ràng để bạn luôn biết mình đang ở đâu.</p></div></div>
		<ol class="about-steps">
			<?php foreach ($steps as $i => $st) : ?>
				<li><span class="about-step-no"><?php echo (int) ($i + 1); ?></span><div><h3><?php echo esc_html($st[0]); ?></h3><p><?php echo esc_html($st[1]); ?></p></div></li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section<?php echo $milestones ? '' : ' soft'; ?>">
	<div class="container">
		<div class="section-head"><div><h2>Đội ngũ tư vấn</h2><p><?php echo $team_is_real ? 'Những người sẽ đồng hành cùng bạn trong từng giao dịch.' : 'Các vai trò chuyên môn sẽ đồng hành cùng bạn trong từng giao dịch.'; ?></p></div></div>
		<div class="about-team">
			<?php foreach ($team as $m) :
				$initial = $m['name'] !== '' ? mb_strtoupper(mb_substr(trim(preg_replace('/^(Anh|Chị|Ông|Bà)\s+/u', '', $m['name'])), 0, 1, 'UTF-8'), 'UTF-8') : '';
				$tel = $m['phone'] !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $m['phone']) : ''; ?>
				<article class="team-card">
					<div class="team-avatar"><?php
						if ($m['photo'] !== '' && preg_match('#^https?://#i', $m['photo'])) echo nvr_img($m['photo'], $m['name'], 240, 240, ['sizes' => '120px']);
						elseif ($initial !== '') echo '<span>' . esc_html($initial) . '</span>';
						else echo nvr_icon('user', 34); ?></div>
					<?php if ($m['name'] !== '') : ?><h3><?php echo esc_html($m['name']); ?></h3><?php endif; ?>
					<div class="team-role"><?php echo esc_html($m['role']); ?></div>
					<?php if ($m['bio'] !== '') : ?><p><?php echo esc_html($m['bio']); ?></p><?php endif; ?>
					<?php if ($tel) : ?><a class="text-link" href="<?php echo esc_attr($tel); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 16); ?> <?php echo esc_html($m['phone']); ?></a>
					<?php else : ?><a class="text-link" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 16); ?> Gọi hotline</a><?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ($testimonials) : ?>
<section class="section<?php echo $milestones ? ' soft' : ''; ?>">
	<div class="container">
		<div class="section-head"><div><h2>Khách hàng nói gì về chúng tôi</h2><p>Nhận xét được đăng với sự đồng ý của khách hàng.</p></div></div>
		<div class="about-cards three testimonials">
			<?php foreach ($testimonials as $t) : ?>
				<article>
					<span class="about-ico"><?php echo nvr_icon('quote', 22); ?></span>
					<blockquote><?php echo esc_html($t[2]); ?></blockquote>
					<footer><strong><?php echo esc_html($t[0]); ?></strong><?php if ($t[1] !== '') : ?><span><?php echo esc_html($t[1]); ?></span><?php endif; ?></footer>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section soft">
	<div class="container">
		<div class="section-head"><div><h2>Giá trị cốt lõi</h2><p>Bốn nguyên tắc chúng tôi giữ vững trong mọi giao dịch.</p></div></div>
		<div class="about-cards four">
			<?php foreach ($values as $v) : ?>
				<article><span class="about-check"><?php echo nvr_icon('check', 18); ?></span><h3><?php echo esc_html($v[0]); ?></h3><p><?php echo esc_html($v[1]); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container about-split about-info">
		<div>
			<div class="eyebrow dark">Thông tin doanh nghiệp</div>
			<h2><?php echo esc_html($company); ?></h2>
			<p>Hãy ghé văn phòng hoặc liên hệ qua các kênh dưới đây. Chúng tôi luôn sẵn sàng lắng nghe nhu cầu của bạn.</p>
			<ul class="about-contact">
				<?php if ($address) : ?><li><?php echo nvr_icon('pin', 18); ?><span><strong>Địa chỉ</strong><?php echo esc_html($address); ?></span></li><?php endif; ?>
				<?php if ($hours) : ?><li><?php echo nvr_icon('clock', 18); ?><span><strong>Giờ làm việc</strong><?php echo esc_html($hours); ?></span></li><?php endif; ?>
				<li><?php echo nvr_icon('phone', 18); ?><span><strong>Hotline</strong><a href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo esc_html(nvr_hotline()); ?></a></span></li>
				<?php if ($email) : ?><li><?php echo nvr_icon('mail', 18); ?><span><strong>Email</strong><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></span></li><?php endif; ?>
				<li><?php echo nvr_icon('zalo', 18); ?><span><strong>Zalo</strong><a href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo">Nhắn tin tư vấn</a></span></li>
			</ul>
		</div>
		<div>
			<div class="eyebrow dark">Câu hỏi thường gặp</div>
			<div class="about-faq">
				<?php foreach ($faqs as $i => $f) : ?>
					<details<?php echo $i === 0 ? ' open' : ''; ?>><summary><?php echo esc_html($f[0]); ?></summary><p><?php echo esc_html($f[1]); ?></p></details>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container"><div class="cta-band">
		<div class="cta-copy">
			<div class="eyebrow">Nhận tư vấn</div>
			<h2>Bạn đang tìm một tổ ấm phù hợp?</h2>
			<p>Để lại thông tin, chuyên viên sẽ liên hệ tư vấn theo đúng nhu cầu và ngân sách của bạn.</p>
			<ul class="cta-points"><li><?php echo nvr_icon('check', 16); ?> Tư vấn miễn phí, không ràng buộc</li><li><?php echo nvr_icon('check', 16); ?> Phản hồi trong thời gian sớm nhất</li><li><?php echo nvr_icon('check', 16); ?> Gợi ý theo đúng nhu cầu và ngân sách</li></ul>
			<p class="cta-contact"><?php echo nvr_icon('phone', 16); ?> <a href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo esc_html(nvr_hotline()); ?></a> · <a href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo">Chat Zalo</a></p>
		</div>
		<?php echo nvr_contact_form_html(['source' => 'Trang Về chúng tôi', 'compact' => true]); ?>
	</div></div>
</section>

<div class="container narrow"><p class="disclaimer about-note">Lưu ý: giá, diện tích và tình trạng pháp lý trên website mang tính tham khảo tại thời điểm đăng. Vui lòng kiểm tra giấy tờ, quy hoạch và hợp đồng từ nguồn có thẩm quyền trước khi giao dịch.</p></div>

<?php endwhile; get_footer(); ?>
