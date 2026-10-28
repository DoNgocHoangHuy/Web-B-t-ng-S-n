<?php
/** Project card (inside The Loop). */
$pid = get_the_ID();
$status = nvr_meta('project_status_text', $pid, 'Đang mở bán');
$soon = mb_stripos($status, 'Sắp') !== false;
?>
<article class="project-card">
	<a class="project-image" href="<?php the_permalink(); ?>" aria-label="Xem dự án <?php the_title_attribute(); ?>" tabindex="-1">
		<?php echo nvr_card_image($pid, 640, 440); ?>
		<span class="tag <?php echo $soon ? 'tag-soon' : 'tag-sale'; ?>"><?php echo esc_html($status); ?></span>
		<?php if (nvr_is_featured($pid)) : ?><span class="tag tag-featured">Nổi bật</span><?php endif; ?>
	</a>
	<div class="project-content">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="price"><?php echo esc_html(nvr_meta('project_price', $pid, 'Đang cập nhật')); ?></div>
		<p class="location"><?php echo nvr_icon('pin', 15); ?><span><?php echo esc_html(nvr_meta('project_area', $pid, 'TP. Hồ Chí Minh')); ?></span></p>
		<a class="text-link" href="<?php the_permalink(); ?>">Xem chi tiết <?php echo nvr_icon('arrow', 16); ?></a>
	</div>
</article>
