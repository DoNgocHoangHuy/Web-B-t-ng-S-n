<?php get_header(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Chủ đầu tư']]); ?>
	<h1>Chủ đầu tư nổi bật</h1>
	<p>Thông tin cơ bản và danh mục dự án của các chủ đầu tư bất động sản.</p>
</div></section>
<section class="section"><div class="container">
	<?php if (have_posts()) : ?>
		<div class="developer-grid"><?php while (have_posts()) : the_post(); ?>
			<a class="developer-card" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) the_post_thumbnail('medium'); else echo '<span class="developer-monogram">' . esc_html(mb_substr(get_the_title(), 0, 1)) . '</span>'; ?><strong><?php the_title(); ?></strong></a>
		<?php endwhile; ?></div>
		<nav class="pagination" aria-label="Phân trang"><?php echo wp_kses_post(paginate_links(['prev_text' => '‹', 'next_text' => '›'])); ?></nav>
	<?php else : ?><div class="empty-state">Chưa có chủ đầu tư.</div><?php endif; ?>
</div></section>
<?php get_footer(); ?>
