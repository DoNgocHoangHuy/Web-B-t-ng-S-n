<?php
/**
 * Floating contact buttons.
 * Desktop: vertical stack on the right edge. Mobile (<= 768px): fixed bottom bar split into 3 equal buttons (CSS only).
 */
?>
<div class="floating-contact" role="complementary" aria-label="Liên hệ nhanh">
	<button type="button" class="fc fc-book" data-open-booking data-track="click_booking" aria-haspopup="dialog" aria-controls="nvr-booking" aria-label="Đặt lịch hẹn tư vấn">
		<span class="fc-icon"><?php echo nvr_icon('calendar', 22); ?></span><span class="fc-label">Đặt lịch</span>
	</button>
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

<div class="nvr-modal" id="nvr-booking" role="dialog" aria-modal="true" aria-labelledby="nvr-booking-title" hidden>
	<div class="nvr-modal-backdrop" data-close-booking></div>
	<div class="nvr-modal-panel">
		<button type="button" class="nvr-modal-close" data-close-booking aria-label="Đóng"><?php echo nvr_icon('close', 20); ?></button>
		<div class="nvr-modal-head">
			<span class="nvr-modal-badge"><?php echo nvr_icon('calendar', 22); ?></span>
			<h2 id="nvr-booking-title">Đặt lịch hẹn tư vấn</h2>
			<p>Chọn thời gian thuận tiện, chuyên viên sẽ gọi xác nhận lại với bạn.</p>
		</div>
		<div class="nvr-modal-body"><?php echo nvr_booking_form_html(); ?></div>
	</div>
</div>
