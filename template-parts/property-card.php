<?php
/**
 * Property card. Must be used inside The Loop.
 * Shows: bold title, detailed address, "70 m² | 2 PN | 2 WC", concrete price, status badge, favourite heart (top-right).
 */
$pid = get_the_ID();
[$status, $status_class] = nvr_status($pid);
$specs = nvr_specs($pid);
?>
<article <?php post_class('property-card'); ?> data-id="<?php echo esc_attr($pid); ?>">
	<div class="property-image">
		<a href="<?php the_permalink(); ?>" aria-label="Xem <?php the_title_attribute(); ?>" tabindex="-1"><?php echo nvr_card_image($pid); ?></a>
		<span class="tag tag-<?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></span>
		<button class="favorite" type="button" data-favorite="<?php echo esc_attr($pid); ?>" aria-pressed="false" aria-label="Thêm vào yêu thích"><?php echo nvr_icon('heart', 20); ?></button>
	</div>
	<div class="property-content">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="location"><?php echo nvr_icon('pin', 15); ?><span><?php echo esc_html(nvr_merge_address(nvr_meta('address', $pid, trim(strip_tags(get_the_term_list($pid, 'property_location', '', ', ')) ?: 'TP. Hồ Chí Minh')))); ?></span></p>
		<?php if ($specs) : ?>
			<p class="property-meta"><?php foreach ($specs as $s) echo '<span>' . esc_html($s) . '</span>'; ?></p>
		<?php endif; ?>
		<div class="price"><?php echo esc_html(nvr_price_text($pid)); ?></div>
	</div>
</article>
