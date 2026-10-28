<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Liên hệ']]); ?>
	<h1><?php the_title(); ?></h1>
	<div class="entry-content lead"><?php the_content(); ?></div>
</div></section>
<div class="container"><div class="single-layout contact-layout">
	<article>
		<div class="contact-cards">
			<a class="contact-card" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 24); ?><strong>Hotline</strong><span><?php echo esc_html(nvr_hotline()); ?></span></a>
			<a class="contact-card" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php echo nvr_icon('zalo', 24); ?><strong>Zalo</strong><span>Chat ngay</span></a>
			<a class="contact-card" href="<?php echo esc_url(nvr_facebook_url()); ?>" target="_blank" rel="noopener" data-track="click_facebook"><?php echo nvr_icon('facebook', 24); ?><strong>Facebook</strong><span>Nhắn tin fanpage</span></a>
			<a class="contact-card" href="mailto:<?php echo esc_attr(nvr_contact_email()); ?>"><?php echo nvr_icon('mail', 24); ?><strong>Email</strong><span><?php echo esc_html(nvr_contact_email()); ?></span></a>
		</div>
		<section class="content-block"><h2>Văn phòng</h2>
			<p class="location"><?php echo nvr_icon('pin', 16); ?><span><?php echo esc_html(nvr_opt('address')); ?></span></p>
			<?php if (nvr_opt('hours')) : ?><p class="location"><?php echo nvr_icon('clock', 16); ?><span>Giờ làm việc: <?php echo esc_html(nvr_opt('hours')); ?></span></p><?php endif; ?>
			<iframe class="map-frame" src="<?php echo esc_url(nvr_map_embed_url(nvr_opt('address'))); ?>" title="Bản đồ văn phòng" loading="lazy" allowfullscreen></iframe>
		</section>
	</article>
	<aside class="detail-card"><h2 class="card-title">Gửi yêu cầu tư vấn</h2><p>Chúng tôi phản hồi trong thời gian sớm nhất.</p><?php echo nvr_contact_form_html(['source' => 'Trang liên hệ', 'compact' => true]); ?></aside>
</div></div>
<?php endwhile; get_footer(); ?>
