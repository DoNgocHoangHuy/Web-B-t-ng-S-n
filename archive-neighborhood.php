<?php get_header(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Khu dân cư']]); ?>
	<h1>Khu dân cư</h1>
	<p>Thông tin cơ bản, đặc điểm nổi bật và tình hình thị trường nhà đất.</p>
</div></section>
<section class="section"><div class="container">
	<?php if (have_posts()) : ?>
		<div class="neighborhood-grid"><?php while (have_posts()) : the_post(); ?>
			<a class="neighborhood-card" href="<?php the_permalink(); ?>"><?php echo nvr_card_image(get_the_ID(), 640, 480); ?><span><strong><?php the_title(); ?></strong><small><?php echo esc_html(nvr_meta('neighborhood_district', get_the_ID(), 'TP. Hồ Chí Minh')); ?></small></span></a>
		<?php endwhile; ?></div>
		<nav class="pagination" aria-label="Phân trang"><?php echo wp_kses_post(paginate_links(['prev_text' => '‹', 'next_text' => '›'])); ?></nav>
	<?php else : ?><div class="empty-state">Chưa có khu dân cư.</div><?php endif; ?>
</div></section>
<?php get_footer(); ?>
