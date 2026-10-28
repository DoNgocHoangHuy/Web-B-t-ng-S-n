<?php
get_header();
while (have_posts()) : the_post();
	$id = get_the_ID();
	$projects = new WP_Query(['post_type' => 'project', 'posts_per_page' => 6, 'no_found_rows' => true, 'meta_query' => [['key' => '_nvr_project_developer', 'value' => get_the_title()]]]);
	$url = nvr_meta('developer_url', $id);
?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Chủ đầu tư', nvr_archive_url('developer')], [get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
</div></section>
<div class="container"><div class="single-layout">
	<article>
		<?php if (has_post_thumbnail()) the_post_thumbnail('large'); ?>
		<div class="entry-content"><?php the_content(); ?></div>
		<?php if ($url) echo '<p><a class="btn" href="' . esc_url($url) . '" target="_blank" rel="noopener nofollow">Website chính thức ↗</a></p>'; ?>
		<section class="content-block"><h2>Dự án liên quan</h2>
			<?php if ($projects->have_posts()) : ?>
				<div class="project-grid cols-2"><?php while ($projects->have_posts()) { $projects->the_post(); get_template_part('template-parts/project-card'); } wp_reset_postdata(); ?></div>
			<?php else : ?><p>Chưa có dự án liên kết với chủ đầu tư này.</p><?php endif; ?>
		</section>
	</article>
	<?php get_template_part('template-parts/contact-box', null, ['title' => 'Liên hệ tư vấn', 'ref' => $id, 'source' => get_the_title()]); ?>
</div></div>
<?php endwhile; get_footer(); ?>
