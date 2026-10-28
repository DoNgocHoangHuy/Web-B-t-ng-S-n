<?php get_header(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Dự án']]); ?>
	<h1>Dự án bất động sản</h1>
	<p>Thông tin tham khảo về dự án và trạng thái mở bán.</p>
</div></section>
<section class="section"><div class="container">
	<?php if (have_posts()) : ?>
		<div class="project-grid cols-3"><?php while (have_posts()) { the_post(); get_template_part('template-parts/project-card'); } ?></div>
		<nav class="pagination" aria-label="Phân trang"><?php echo wp_kses_post(paginate_links(['prev_text' => '‹', 'next_text' => '›'])); ?></nav>
	<?php else : ?><div class="empty-state">Chưa có dự án.</div><?php endif; ?>
</div></section>
<?php get_footer(); ?>
