<?php
/**
 * Trang Liên hệ - Glassmorphism + Layered layout.
 * Ảnh nền: có thể đổi bằng filter 'nvr_contact_bg' hoặc sửa URL bên dưới.
 */
get_header();
$bg      = apply_filters('nvr_contact_bg', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=2200&q=80');
$address = nvr_opt('address');
$q       = rawurlencode($address ?: 'Thành phố Hồ Chí Minh');
$dir_url = 'https://www.google.com/maps/dir/?api=1&destination=' . $q;
$view_url = 'https://www.google.com/maps/search/?api=1&query=' . $q;
while (have_posts()) : the_post();
	$intro = trim(wp_strip_all_tags(get_the_content()));
?>
<div class="lh-stage">
	<div class="lh-bg" data-parallax style="background-image:url('<?php echo esc_url($bg); ?>')" aria-hidden="true"></div>
	<div class="lh-shade" aria-hidden="true"></div>

	<div class="lh-wrap">
		<!-- ===== KHỐI TRÊN: thông tin + form ===== -->
		<section class="lh-glass lh-top">
			<div class="lh-info">
				<?php nvr_breadcrumbs([['Liên hệ']]); ?>
				<h1 class="lh-title">NhàViệt<span>Liên hệ</span></h1>
				<p class="lh-lead"><?php echo esc_html($intro ?: 'Đội ngũ tư vấn của NhàViệt luôn sẵn sàng đồng hành cùng bạn trên hành trình tìm nơi an cư và cơ hội đầu tư phù hợp.'); ?></p>

				<div class="lh-cards">
					<a class="lh-card" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline">
						<span class="lh-ico"><?php echo nvr_icon('phone', 22); ?></span>
						<strong>Hotline</strong><span class="lh-sub"><?php echo esc_html(nvr_hotline()); ?></span>
					</a>
					<a class="lh-card" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo">
						<span class="lh-ico"><?php echo nvr_icon('zalo', 22); ?></span>
						<strong>Zalo</strong><span class="lh-sub">Chat ngay với chuyên viên</span>
					</a>
					<a class="lh-card" href="<?php echo esc_url(nvr_facebook_url()); ?>" target="_blank" rel="noopener" data-track="click_facebook">
						<span class="lh-ico"><?php echo nvr_icon('facebook', 22); ?></span>
						<strong>Facebook</strong><span class="lh-sub">Nhắn tin fanpage</span>
					</a>
					<a class="lh-card" href="mailto:<?php echo esc_attr(nvr_contact_email()); ?>">
						<span class="lh-ico"><?php echo nvr_icon('mail', 22); ?></span>
						<strong>Email</strong><span class="lh-sub"><?php echo esc_html(nvr_contact_email()); ?></span>
					</a>
				</div>
			</div>

			<div class="lh-formbox">
				<h2>Gửi yêu cầu tư vấn</h2>
				<p>Để lại thông tin, chúng tôi phản hồi trong thời gian sớm nhất.</p>
				<?php echo nvr_floating_form_html(); ?>
			</div>
		</section>

		<!-- ===== KHỐI DƯỚI: bản đồ ===== -->
		<section class="lh-glass lh-mapbox">
			<div class="lh-maphead">
				<div class="lh-addr">
					<h2>Văn phòng NhàViệt</h2>
					<?php if ($address) : ?><p><?php echo nvr_icon('pin', 16); ?><span><?php echo esc_html($address); ?></span></p><?php endif; ?>
					<?php if (nvr_opt('hours')) : ?><p><?php echo nvr_icon('clock', 16); ?><span>Giờ làm việc: <?php echo esc_html(nvr_opt('hours')); ?></span></p><?php endif; ?>
				</div>
				<div class="lh-mapbtns">
					<a class="lh-pill lh-pill-solid" href="<?php echo esc_url($dir_url); ?>" target="_blank" rel="noopener">Chỉ đường</a>
					<a class="lh-pill" href="<?php echo esc_url($view_url); ?>" target="_blank" rel="noopener">Mở Google Maps</a>
				</div>
			</div>
			<div class="lh-map">
				<iframe src="<?php echo esc_url(nvr_map_embed_url($address)); ?>" title="Bản đồ văn phòng NhàViệt" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</div>
		</section>
	</div>
</div>
<?php endwhile; get_footer(); ?>
