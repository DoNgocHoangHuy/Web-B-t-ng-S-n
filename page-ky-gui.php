<?php
/**
 * Template: Ký gửi bất động sản (slug: ky-gui)
 * Chủ nhà gửi thông tin muốn bán / cho thuê. Dữ liệu được lưu vào Bất động sản > Khách hàng tiềm năng và gửi email như form liên hệ.
 */
get_header();

$steps = [
	['Gửi thông tin', 'Điền loại bất động sản, địa chỉ, diện tích và mức giá mong muốn vào biểu mẫu.'],
	['Chuyên viên liên hệ', 'Chúng tôi gọi lại trong thời gian sớm nhất để trao đổi chi tiết và hẹn lịch khảo sát.'],
	['Khảo sát & định hướng giá', 'Xem hiện trạng, rà soát pháp lý và tham khảo mặt bằng giá khu vực để đưa ra định hướng.'],
	['Thống nhất bằng văn bản', 'Các điều khoản, thời hạn và phí dịch vụ (nếu có) được thỏa thuận rõ ràng trước khi bắt đầu.'],
	['Giới thiệu & dẫn xem', 'Tài sản được giới thiệu tới khách có nhu cầu phù hợp và có chuyên viên đi cùng khi xem nhà.'],
];
$docs = [
	'Giấy chứng nhận quyền sử dụng đất, quyền sở hữu nhà (sổ hồng / sổ đỏ)',
	'Giấy tờ tùy thân của chủ sở hữu (CCCD/CMND)',
	'Hợp đồng thuê hiện tại, nếu tài sản đang cho thuê',
	'Hình ảnh thực tế và thông tin tiện ích, nội thất đi kèm',
];
$faqs = [
	['Tôi có phải trả phí khi gửi thông tin không?', 'Việc gửi thông tin và nhận tư vấn ban đầu không mất phí. Phí dịch vụ (nếu có) sẽ được trao đổi và thống nhất bằng văn bản trước khi bạn quyết định ký gửi.'],
	['Thông tin của tôi có bị công khai không?', 'Không. Thông tin liên hệ của bạn chỉ dùng để tư vấn. Tài sản chỉ được đăng công khai sau khi bạn đồng ý.'],
	['Tôi chưa có sổ hồng thì có ký gửi được không?', 'Bạn vẫn có thể gửi thông tin để được tư vấn. Tuy nhiên, hồ sơ pháp lý đầy đủ sẽ giúp việc giới thiệu cho khách nhanh và an toàn hơn.'],
];

while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([[get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
	<p class="lead">Bạn đang muốn bán hoặc cho thuê nhà đất? Gửi thông tin để chuyên viên tư vấn định hướng giá và giới thiệu tài sản đến đúng người cần.</p>
</div></section>

<div class="container"><div class="single-layout contact-layout">
	<article>
		<section class="content-block">
			<h2>Quy trình ký gửi</h2>
			<ol class="about-steps">
				<?php foreach ($steps as $i => $st) : ?>
					<li><span class="about-step-no"><?php echo (int) ($i + 1); ?></span><div><h3><?php echo esc_html($st[0]); ?></h3><p><?php echo esc_html($st[1]); ?></p></div></li>
				<?php endforeach; ?>
			</ol>
		</section>
		<section class="content-block">
			<h2>Hồ sơ nên chuẩn bị</h2>
			<ul class="check-list">
				<?php foreach ($docs as $d) : ?><li><?php echo nvr_icon('check', 18); ?><span><?php echo esc_html($d); ?></span></li><?php endforeach; ?>
			</ul>
			<p class="disclaimer">Chưa đủ hồ sơ cũng không sao, bạn cứ gửi thông tin trước, chuyên viên sẽ hướng dẫn thêm.</p>
		</section>
		<section class="content-block">
			<h2>Câu hỏi thường gặp</h2>
			<div class="about-faq">
				<?php foreach ($faqs as $i => $f) : ?>
					<details<?php echo $i === 0 ? ' open' : ''; ?>><summary><?php echo esc_html($f[0]); ?></summary><p><?php echo esc_html($f[1]); ?></p></details>
				<?php endforeach; ?>
			</div>
		</section>
	</article>
	<aside class="detail-card">
		<h2 class="card-title">Gửi thông tin ký gửi</h2>
		<p>Các ô có dấu * là bắt buộc. Chúng tôi liên hệ lại trong thời gian sớm nhất.</p>
		<?php echo nvr_contact_form_html(['source' => 'Ký gửi bất động sản', 'consign' => true, 'button' => 'Gửi thông tin ký gửi']); ?>
		<div class="quick-contact">
			<a class="btn btn-outline" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 18); ?> <?php echo esc_html(nvr_hotline()); ?></a>
			<a class="btn btn-outline" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php echo nvr_icon('zalo', 18); ?> Chat Zalo</a>
		</div>
	</aside>
</div></div>
<?php endwhile; get_footer(); ?>
