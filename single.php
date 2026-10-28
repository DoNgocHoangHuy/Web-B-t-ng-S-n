<?php get_header(); while (have_posts()) : the_post(); $cat = get_the_category(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Tin tức', nvr_posts_page_url()], [get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
	<p class="meta"><?php echo esc_html(get_the_date()); ?><?php if ($cat) echo ' · ' . esc_html($cat[0]->name); ?></p>
</div></section>
<section class="section"><article class="container inner-container narrow">
	<?php $img = nvr_card_image_url(get_the_ID(), 'large'); if ($img) echo '<div class="post-hero">' . nvr_img($img, get_the_title(), 1200, 675, ['priority' => true, 'sizes' => '(max-width: 900px) 100vw, 860px']) . '</div>'; ?>
	<div class="entry-content"><?php the_content(); ?></div>
</article></section>
<?php
$rel = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => [get_the_ID()], 'no_found_rows' => true, 'category__in' => wp_get_post_categories(get_the_ID())]);
if ($rel->have_posts()) : ?>
<section class="section soft"><div class="container"><div class="section-head"><h2>Bài viết liên quan</h2></div>
	<div class="post-grid"><?php while ($rel->have_posts()) : $rel->the_post(); ?>
		<article class="post-card"><a class="post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo nvr_card_image(get_the_ID(), 640, 440); ?></a><div class="post-body"><small><?php echo esc_html(get_the_date()); ?></small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2></div></article>
	<?php endwhile; wp_reset_postdata(); ?></div></div></section>
<?php endif; endwhile; get_footer(); ?>
