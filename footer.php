</main>

<footer class="site-footer">
	<div class="container inner-container">
		<div class="footer-grid">
			<div class="footer-about">
				<a class="brand brand-light" href="<?php echo esc_url(home_url('/')); ?>">NHÀ<span>VIỆT</span></a>
				<p>Nền tảng tìm kiếm bất động sản giúp bạn chọn nơi an cư và cơ hội đầu tư phù hợp tại TP. Hồ Chí Minh.</p>
				<div class="footer-social">
					<a href="<?php echo esc_url(nvr_facebook_url()); ?>" target="_blank" rel="noopener" aria-label="Facebook" data-track="click_facebook"><?php echo nvr_icon('facebook', 20); ?></a>
					<a href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" aria-label="Zalo" data-track="click_zalo"><?php echo nvr_icon('zalo', 20); ?></a>
				</div>
			</div>
			<div>
				<h4>Khám phá</h4>
				<ul>
					<li><a href="<?php echo esc_url(nvr_archive_url('property', ['deal' => 'sale'])); ?>">Nhà đất bán</a></li>
					<li><a href="<?php echo esc_url(nvr_archive_url('property', ['deal' => 'rent'])); ?>">Nhà đất cho thuê</a></li>
					<li><a href="<?php echo esc_url(nvr_archive_url('project')); ?>">Dự án</a></li>
					<li><a href="<?php echo esc_url(nvr_archive_url('neighborhood')); ?>">Khu dân cư</a></li>
					<li><a href="<?php echo esc_url(nvr_posts_page_url()); ?>">Tin tức thị trường</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('ky-gui')); ?>">Ký gửi bất động sản</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('tinh-vay')); ?>">Tính khoản vay</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('ve-chung-toi')); ?>">Về chúng tôi</a></li>
				</ul>
			</div>
			<div>
				<h4>Pháp lý</h4>
				<ul>
					<li><a href="<?php echo esc_url(nvr_page_url('chinh-sach-bao-mat')); ?>">Chính sách bảo mật</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('dieu-khoan-su-dung')); ?>">Điều khoản sử dụng</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('quy-che-hoat-dong')); ?>">Quy chế hoạt động</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('lien-he')); ?>">Liên hệ</a></li>
				</ul>
			</div>
			<div>
				<h4>Kết nối với chúng tôi</h4>
				<ul class="footer-contact">
					<li><?php echo nvr_icon('phone', 16); ?> <a href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline">Hotline: <?php echo esc_html(nvr_hotline()); ?></a></li>
					<?php if (nvr_public_email()) : ?><li><?php echo nvr_icon('mail', 16); ?> <a href="mailto:<?php echo esc_attr(nvr_public_email()); ?>"><?php echo esc_html(nvr_public_email()); ?></a></li><?php endif; ?>
					<li><?php echo nvr_icon('pin', 16); ?> <span><?php echo esc_html(nvr_opt('address')); ?></span></li>
					<?php if (nvr_opt('hours')) : ?><li><?php echo nvr_icon('clock', 16); ?> <span><?php echo esc_html(nvr_opt('hours')); ?></span></li><?php endif; ?>
				</ul>
			</div>
		</div>
		<div class="footer-bottom">
			<span>© <?php echo esc_html(date_i18n('Y')); ?> <?php echo esc_html(nvr_opt('company')); ?>. Thông tin bất động sản mang tính tham khảo, vui lòng xác minh trước khi giao dịch.</span>
			<span class="footer-legal">
				<a href="<?php echo esc_url(nvr_page_url('chinh-sach-bao-mat')); ?>">Chính sách bảo mật</a>
				<a href="<?php echo esc_url(nvr_page_url('dieu-khoan-su-dung')); ?>">Điều khoản sử dụng</a>
				<a href="<?php echo esc_url(nvr_page_url('quy-che-hoat-dong')); ?>">Quy chế hoạt động</a>
			</span>
		</div>
	</div>
</footer>

<?php get_template_part('template-parts/floating-contact'); ?>
<?php wp_footer(); ?>
</body>
</html>
