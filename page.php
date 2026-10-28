<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([[get_the_title()]]); ?>
	<h1><?php the_title(); ?></h1>
</div></section>
<section class="section"><div class="container inner-container narrow"><div class="entry-content"><?php the_content(); ?></div></div></section>
<?php endwhile; get_footer(); ?>
