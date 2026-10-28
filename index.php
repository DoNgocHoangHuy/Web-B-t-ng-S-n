<?php
/** Blog / news listing, category archives and fallback. */
get_header();
$is_blog = is_home() || is_category() || is_tag() || is_archive();
$title = is_home() ? 'Tin tức bất động sản' : (is_archive() ? wp_strip_all_tags(preg_replace('/^[^:]+:\s*/u', '', get_the_archive_title())) : get_bloginfo('name'));
?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs(is_home() ? [['Tin tức']] : [['Tin tức', nvr_posts_page_url()], [$title]]); ?>
	<h1><?php echo esc_html($title); ?></h1>
	<p>Kiến thức, kinh nghiệm và xu hướng thị trường nhà đất.</p>
</div></section>
<section class="section"><div class="container inner-container">
	<?php $cats = get_categories(['hide_empty' => true]); if ($cats) : ?>
		<div class="chips"><a class="chip<?php echo is_home() ? ' active' : ''; ?>" href="<?php echo esc_url(nvr_posts_page_url()); ?>">Tất cả</a>
		<?php foreach ($cats as $c) echo '<a class="chip' . (is_category($c->term_id) ? ' active' : '') . '" href="' . esc_url(get_category_link($c)) . '">' . esc_html($c->name) . '</a>'; ?></div>
	<?php endif; ?>
	<?php if (have_posts()) : ?>
		<div class="post-grid"><?php while (have_posts()) : the_post(); ?>
			<article class="post-card">
				<a class="post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo nvr_card_image(get_the_ID(), 640, 440); ?></a>
				<div class="post-body"><small><?php echo esc_html(get_the_date()); ?></small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Đọc tiếp <?php echo nvr_icon('arrow', 16); ?></a></div>
			</article>
		<?php endwhile; ?></div>
		<nav class="pagination" aria-label="Phân trang"><?php echo wp_kses_post(paginate_links(['prev_text' => '‹', 'next_text' => '›'])); ?></nav>
	<?php else : ?><div class="empty-state">Chưa có bài viết.</div><?php endif; ?>
</div></section>
<?php get_footer(); ?>
