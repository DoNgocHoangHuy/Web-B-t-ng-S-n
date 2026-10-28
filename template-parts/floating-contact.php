<?php
/**
 * Floating contact buttons.
 * Desktop: vertical stack on the right edge. Mobile (<= 768px): fixed bottom bar split into 3 equal buttons (CSS only).
 */
?>
<div class="floating-contact" role="complementary" aria-label="Liên hệ nhanh">
	<a class="fc fc-phone" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline" aria-label="Gọi hotline <?php echo esc_attr(nvr_hotline()); ?>">
		<span class="fc-icon"><?php echo nvr_icon('phone', 22); ?></span><span class="fc-label">Hotline</span>
	</a>
	<a class="fc fc-zalo" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo" aria-label="Chat Zalo">
		<span class="fc-icon"><?php echo nvr_icon('zalo', 22); ?></span><span class="fc-label">Zalo</span>
	</a>
	<a class="fc fc-fb" href="<?php echo esc_url(nvr_facebook_url()); ?>" target="_blank" rel="noopener" data-track="click_facebook" aria-label="Facebook">
		<span class="fc-icon"><?php echo nvr_icon('facebook', 22); ?></span><span class="fc-label">Facebook</span>
	</a>
</div>
