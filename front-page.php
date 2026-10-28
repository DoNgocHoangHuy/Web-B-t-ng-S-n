<?php
get_header();
$archive = nvr_archive_url('property');
$types = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
$locs = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);
$hero_img = nvr_pool_urls('hero', 1, 0, 1600)[0];
?>
<section class="hero">
	<?php echo nvr_img($hero_img, '', 1600, 900, ['priority' => true, 'class' => 'hero-bg', 'sizes' => '100vw']); ?>
	<div class="container inner-container hero-inner">
		<div class="eyebrow">Không gian sống · Cơ hội đầu tư</div>
		<h1>Tìm kiếm bất động sản mơ ước của bạn</h1>
		<p>Khám phá căn hộ, nhà phố và dự án phù hợp với nhu cầu sống cũng như kế hoạch đầu tư.</p>
		<form class="search-panel" action="<?php echo esc_url($archive); ?>" method="get" role="search">
			<label class="screen-reader-text" for="h-deal">Nhu cầu</label>
			<select id="h-deal" name="deal"><option value="">Mua & Thuê</option><option value="sale">Mua bán</option><option value="rent">Cho thuê</option></select>
			<label class="screen-reader-text" for="h-type">Loại bất động sản</label>
			<select id="h-type" name="type"><option value="">Loại BĐS</option><?php foreach ($types as $t) echo '<option value="' . esc_attr($t->slug) . '">' . esc_html($t->name) . '</option>'; ?></select>
			<label class="screen-reader-text" for="h-loc">Khu vực</label>
			<select id="h-loc" name="location"><option value="">Khu vực</option><?php foreach ($locs as $t) echo '<option value="' . esc_attr($t->slug) . '">' . esc_html($t->name) . '</option>'; ?></select>
			<label class="screen-reader-text" for="h-kw">Từ khóa</label>
			<input id="h-kw" type="search" name="keyword" placeholder="Từ khóa, dự án, đường, phường…">
			<button class="btn" type="submit"><?php echo nvr_icon('search', 18); ?> Tìm kiếm</button>
		</form>
		<div class="hero-suggestions">Gợi ý:
			<?php foreach (array_slice($locs, 0, 4) as $t) echo '<a href="' . esc_url(nvr_archive_url('property', ['location' => $t->slug])) . '">' . esc_html($t->name) . '</a>'; ?>
		</div>
	</div>
</section>

<section class="trust-strip"><div class="container inner-container trust-grid">
	<div><?php echo nvr_icon('shield', 26); ?><span>Cam kết xác thực</span></div>
	<div><?php echo nvr_icon('award', 26); ?><span>Dẫn đầu số lượng</span></div>
	<div><?php echo nvr_icon('handshake', 26); ?><span>Trọn hỗ trợ, chi phí thấp</span></div>
	<div><?php echo nvr_icon('gift', 26); ?><span>Nhận nhiều ưu đãi</span></div>
</div></section>

<section class="section">
	<div class="container">
		<div class="section-head"><div><h2>Dự án mới nổi bật</h2><p>Khám phá thông tin các dự án đang được quan tâm.</p></div><a class="text-link" href="<?php echo esc_url(nvr_archive_url('project')); ?>">Xem tất cả <?php echo nvr_icon('arrow', 16); ?></a></div>
		<?php $projects = nvr_featured_projects(6); ?>
		<?php if ($projects->have_posts()) : ?>
			<div class="project-grid cols-3"><?php while ($projects->have_posts()) { $projects->the_post(); get_template_part('template-parts/project-card'); } wp_reset_postdata(); ?></div>
		<?php else : ?>
			<div class="empty-state">Chưa có dự án nổi bật. Vào Dự án → sửa dự án → tick “Nổi bật”.</div>
		<?php endif; ?>
	</div>
</section>

<section class="section soft">
	<div class="container">
		<div class="section-head"><div><h2>Bất động sản nổi bật</h2><p>Lựa chọn tham khảo được cập nhật trên hệ thống.</p></div><a class="text-link" href="<?php echo esc_url($archive); ?>">Xem tất cả <?php echo nvr_icon('arrow', 16); ?></a></div>
		<?php $q = nvr_featured_properties(12); ?>
		<?php if ($q->have_posts()) : ?>
			<div class="property-grid"><?php while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
		<?php else : ?>
			<div class="empty-state">Chưa có tin đăng. Vào Cài đặt → Nhà Việt Realty để nhập dữ liệu mẫu hoặc thêm tin mới.</div>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head"><div><div class="eyebrow dark">Không gian sống nổi bật</div><h2>Khám phá nhà đẹp & không gian sống</h2><p>Tuyển chọn căn hộ, nhà phố và biệt thự để bạn dễ hình dung không gian phù hợp.</p></div><a class="text-link" href="<?php echo esc_url(nvr_archive_url('property', ['type' => 'can-ho'])); ?>">Khám phá căn hộ <?php echo nvr_icon('arrow', 16); ?></a></div>
		<?php
		$insp = [
			['Căn hộ hiện đại', nvr_pool_urls('apartment', 1, 0, 1200)[0], nvr_archive_url('property', ['type' => 'can-ho']), 'inspiration-large'],
			['Nhà phố & biệt thự', nvr_pool_urls('house', 1, 0, 800)[0], nvr_archive_url('property', ['type' => 'nha-pho']), ''],
			['Đất nền đầu tư', nvr_pool_urls('land', 1, 0, 800)[0], nvr_archive_url('property', ['type' => 'dat-nen']), ''],
		]; ?>
		<div class="inspiration-grid">
			<?php foreach ($insp as $it) : ?>
				<a class="<?php echo esc_attr($it[3]); ?>" href="<?php echo esc_url($it[2]); ?>"><?php echo nvr_img($it[1], $it[0], $it[3] ? 900 : 600, $it[3] ? 700 : 450, ['sizes' => '(max-width: 650px) 100vw, 40vw']); ?><span><?php echo esc_html($it[0]); ?></span></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section soft">
	<div class="container">
		<div class="section-head"><div><h2>Chủ đầu tư nổi bật</h2><p>Thông tin cơ bản và danh mục dự án của các chủ đầu tư bất động sản uy tín hiện nay.</p></div><a class="text-link" href="<?php echo esc_url(nvr_archive_url('developer')); ?>">Xem thêm <?php echo nvr_icon('arrow', 16); ?></a></div>
		<div class="developer-grid">
			<?php $devs = new WP_Query(['post_type' => 'developer', 'posts_per_page' => 8, 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true]);
			while ($devs->have_posts()) : $devs->the_post(); ?>
				<a class="developer-card" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) the_post_thumbnail('medium'); else echo '<span class="developer-monogram">' . esc_html(mb_substr(get_the_title(), 0, 1)) . '</span>'; ?><strong><?php the_title(); ?></strong></a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head"><div><h2>Khu dân cư</h2><p>Thông tin cơ bản, đặc điểm nổi bật và tình hình thị trường nhà đất tại TP. Hồ Chí Minh.</p></div><a class="text-link" href="<?php echo esc_url(nvr_archive_url('neighborhood')); ?>">Xem tất cả <?php echo nvr_icon('arrow', 16); ?></a></div>
		<div class="neighborhood-grid">
			<?php $hoods = new WP_Query(['post_type' => 'neighborhood', 'posts_per_page' => 4, 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true]);
			while ($hoods->have_posts()) : $hoods->the_post(); ?>
				<a class="neighborhood-card" href="<?php the_permalink(); ?>">
					<?php echo nvr_card_image(get_the_ID(), 640, 480); ?>
					<span><strong><?php the_title(); ?></strong><small><?php echo esc_html(nvr_meta('neighborhood_district', get_the_ID(), 'TP. Hồ Chí Minh')); ?></small></span>
				</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<section class="section about-preview">
	<div class="container about-preview-inner">
		<div><div class="eyebrow dark">Về NhàViệt</div><h2>Đồng hành cùng bạn trên hành trình tìm tổ ấm</h2><p>NhàViệt giúp bạn khám phá nhà đất, căn hộ và dự án theo khu vực, nhu cầu và ngân sách. Chúng tôi hướng đến trải nghiệm tìm kiếm rõ ràng, thông tin dễ so sánh và hỗ trợ khách hàng chủ động đặt lịch tư vấn.</p><a class="btn" href="<?php echo esc_url(nvr_page_url('ve-chung-toi')); ?>">Tìm hiểu về chúng tôi <?php echo nvr_icon('arrow', 16); ?></a></div>
		<div class="about-preview-points"><div><strong>Thông tin có cấu trúc</strong><span>Địa chỉ, diện tích, loại hình và mức giá tham khảo được trình bày dễ theo dõi.</span></div><div><strong>Khám phá theo nhu cầu</strong><span>Lọc tin mua bán, cho thuê và xem danh mục dự án, khu dân cư.</span></div><div><strong>Kết nối tư vấn</strong><span>Gửi yêu cầu để đội ngũ phụ trách liên hệ và hỗ trợ bước tiếp theo.</span></div></div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head"><div><h2>Dịch vụ &amp; công cụ dành cho bạn</h2><p>Hỗ trợ cả người cần tìm nhà lẫn chủ nhà muốn bán hoặc cho thuê.</p></div></div>
		<div class="about-cards two">
			<article><span class="about-ico"><?php echo nvr_icon('home', 24); ?></span><h3><a href="<?php echo esc_url(nvr_page_url('ky-gui')); ?>">Ký gửi bất động sản</a></h3><p>Bạn là chủ nhà? Gửi thông tin để chuyên viên tư vấn định hướng giá và giới thiệu tài sản đến đúng người cần.</p><a class="text-link" href="<?php echo esc_url(nvr_page_url('ky-gui')); ?>">Gửi thông tin <?php echo nvr_icon('arrow', 16); ?></a></article>
			<article><span class="about-ico"><?php echo nvr_icon('calc', 24); ?></span><h3><a href="<?php echo esc_url(nvr_page_url('tinh-vay')); ?>">Tính khoản vay mua nhà</a></h3><p>Ước tính số tiền trả mỗi tháng, tổng lãi và lịch trả nợ theo năm để chuẩn bị tài chính trước khi xem nhà.</p><a class="text-link" href="<?php echo esc_url(nvr_page_url('tinh-vay')); ?>">Dùng công cụ <?php echo nvr_icon('arrow', 16); ?></a></article>
		</div>
	</div>
</section>

<section class="section soft">
	<div class="container"><div class="blog-columns">
		<?php foreach ([['thong-tin-nha-dat', 'Thông tin nhà đất'], ['tin-tuc-kien-truc', 'Tin tức / Kiến trúc']] as $ci) :
			$cat = get_category_by_slug($ci[0]);
			$posts = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3, 'no_found_rows' => true, 'cat' => $cat ? $cat->term_id : 0]); ?>
			<div>
				<div class="section-head"><h2><?php echo esc_html($ci[1]); ?></h2><a class="text-link" href="<?php echo esc_url($cat ? get_category_link($cat) : nvr_posts_page_url()); ?>">Xem thêm <?php echo nvr_icon('arrow', 16); ?></a></div>
				<div class="blog-list">
					<?php if ($posts->have_posts()) : while ($posts->have_posts()) : $posts->the_post(); ?>
						<article class="blog-item">
							<a href="<?php the_permalink(); ?>" class="blog-thumb" tabindex="-1" aria-hidden="true"><?php echo nvr_card_image(get_the_ID(), 240, 160); ?></a>
							<div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><small><?php echo esc_html(get_the_date()); ?></small><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 16)); ?></p></div>
						</article>
					<?php endwhile; else : echo '<p class="muted">Chưa có bài viết.</p>'; endif; wp_reset_postdata(); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div></div>
</section>

<section class="section">
	<div class="container"><div class="cta-band">
		<div>
			<h2>Đăng ký nhận thông tin BĐS mới nhất</h2>
			<p>Nhận tin phù hợp với nhu cầu của bạn hoặc liên hệ tư vấn trực tiếp.</p>
			<p class="cta-contact"><?php echo nvr_icon('phone', 16); ?> <a href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo esc_html(nvr_hotline()); ?></a> · <a href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo">Chat Zalo</a></p>
		</div>
		<?php echo nvr_contact_form_html(['source' => 'Trang chủ', 'compact' => true]); ?>
	</div></div>
</section>
<?php get_footer(); ?>
