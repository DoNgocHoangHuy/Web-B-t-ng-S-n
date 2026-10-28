<?php
/** Sidebar contact card: args title, text, ref, source, phone, price. */
$title = $args['title'] ?? 'Nhận tư vấn';
$text = $args['text'] ?? 'Để lại thông tin, chuyên viên sẽ liên hệ với bạn sớm nhất.';
$phone = !empty($args['phone']) ? $args['phone'] : nvr_hotline();
$tel = 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
?>
<aside class="detail-card">
	<?php if (!empty($args['name'])) : ?><div class="detail-eyebrow">Bất động sản</div><h2 class="detail-name"><?php echo esc_html($args['name']); ?></h2><?php endif; ?>
	<?php if (!empty($args['price'])) : ?><div class="price"><?php echo esc_html($args['price']); ?></div><?php endif; ?>
	<h2 class="card-title"><?php echo esc_html($title); ?></h2>
	<p><?php echo esc_html($text); ?></p>
	<?php echo nvr_contact_form_html(['ref' => $args['ref'] ?? 0, 'source' => $args['source'] ?? '', 'compact' => true]); ?>
	<?php if (!empty($args['price_numeric'])) : ?><a class="text-link calc-link" href="<?php echo esc_url(add_query_arg('price', (int) $args['price_numeric'], nvr_page_url('tinh-vay'))); ?>"><?php echo nvr_icon('calc', 16); ?> Tính khoản vay cho căn này</a><?php endif; ?>
	<div class="quick-contact">
		<a class="btn btn-outline" href="<?php echo esc_attr($tel); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 18); ?> <?php echo esc_html($phone); ?></a>
		<a class="btn btn-outline" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php echo nvr_icon('zalo', 18); ?> Chat Zalo</a>
	</div>
</aside>
