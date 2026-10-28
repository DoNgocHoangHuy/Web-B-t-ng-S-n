<?php
/** Image gallery: args['urls'], args['title']. */
$urls = $args['urls'] ?? [];
$title = $args['title'] ?? '';
if (!$urls) $urls = [nvr_placeholder_url()];
$n = count($urls);
?>
<div class="media-gallery" data-gallery>
	<div class="gallery-stage">
		<?php foreach ($urls as $i => $u) : ?>
			<?php echo nvr_img($u, $title . ' - ảnh ' . ($i + 1), 1200, 675, ['class' => 'gallery-slide' . ($i === 0 ? ' is-active' : ''), 'priority' => $i === 0, 'sizes' => '(max-width: 900px) 100vw, 800px']); ?>
		<?php endforeach; ?>
		<?php if ($n > 1) : ?>
			<button type="button" class="gallery-arrow prev" aria-label="Ảnh trước">‹</button>
			<button type="button" class="gallery-arrow next" aria-label="Ảnh tiếp theo">›</button>
			<span class="gallery-count">1 / <?php echo (int) $n; ?></span>
		<?php endif; ?>
	</div>
	<?php if ($n > 1) : ?>
		<div class="gallery-thumbs">
			<?php foreach ($urls as $i => $u) : ?>
				<button type="button" class="gallery-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" aria-label="Xem ảnh <?php echo (int) ($i + 1); ?>"><?php echo nvr_img($u, '', 160, 110, ['sizes' => '90px']); ?></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
