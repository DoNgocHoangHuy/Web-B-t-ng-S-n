<?php
/* Chỉ dùng menu tùy chỉnh của WordPress khi bật rõ ràng (tránh menu của theme khác, vd. Technova, đè lên menu NhàViệt). */
if (!function_exists('nvr_use_wp_menu')) {
	function nvr_use_wp_menu() {
		return (bool) apply_filters('nvr_use_wp_menu', get_option('nvr_use_wp_menu') === '1') && has_nav_menu('primary');
	}
}

/**
 * Site header: sticky, compact on scroll, with a collapsible compact search bar.
 */
$archive = nvr_archive_url('property');

$types = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
$locations = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#126b50">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main">Bỏ qua đến nội dung chính</a>

<header class="site-header" id="site-header">
	<div class="container inner-container header-main">
		<button class="icon-btn menu-toggle" type="button" aria-label="Mở menu" aria-expanded="false" aria-controls="mobile-menu"><?php echo nvr_icon('menu', 24); ?></button>

		<?php if (has_custom_logo()) : ?>
			<div class="brand brand-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> - Trang chủ">NHÀ<span>VIỆT</span></a>
		<?php endif; ?>

		<nav class="main-nav" aria-label="Menu chính">
			<?php if (nvr_use_wp_menu()) :
				wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-list', 'depth' => 2]);
			else : ?>
				<ul class="nav-list">
					<li class="has-mega"><a href="<?php echo esc_url(nvr_archive_url('property', ['deal' => 'sale'])); ?>" aria-haspopup="true">Mua bán <?php echo nvr_icon('chevron', 14); ?></a><?php nvr_mega_menu('sale'); ?></li>
					<li class="has-mega"><a href="<?php echo esc_url(nvr_archive_url('property', ['deal' => 'rent'])); ?>" aria-haspopup="true">Cho thuê <?php echo nvr_icon('chevron', 14); ?></a><?php nvr_mega_menu('rent'); ?></li>
					<li><a href="<?php echo esc_url(nvr_archive_url('project')); ?>">Dự án</a></li>
					<li><a href="<?php echo esc_url(nvr_archive_url('neighborhood')); ?>">Khu dân cư</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('ve-chung-toi')); ?>">Về chúng tôi</a></li>
					<li><a href="<?php echo esc_url(nvr_posts_page_url()); ?>">Tin tức</a></li>
					<li><a href="<?php echo esc_url(nvr_page_url('lien-he')); ?>">Liên hệ</a></li>
				</ul>
			<?php endif; ?>
		</nav>

		<div class="header-actions">
			<button class="icon-btn search-toggle" type="button" aria-label="Tìm kiếm bất động sản" aria-expanded="false" aria-controls="header-search"><?php echo nvr_icon('search', 22); ?></button>
			<a class="icon-btn fav-link" href="<?php echo esc_url(nvr_page_url('yeu-thich')); ?>" aria-label="Bất động sản yêu thích"><?php echo nvr_icon('heart', 22); ?><span class="fav-count" data-fav-count hidden>0</span></a>
			<a class="btn btn-hotline" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 18); ?><span><?php echo esc_html(nvr_hotline()); ?></span></a>
		</div>
	</div>

	<div class="header-searchbar" id="header-search">
		<div class="container">
			<form class="compact-search" action="<?php echo esc_url($archive); ?>" method="get" role="search">
				<label class="screen-reader-text" for="hs-type">Loại bất động sản</label>
				<select id="hs-type" name="type"><option value="">Loại BĐS</option><?php foreach ($types as $t) echo '<option value="' . esc_attr($t->slug) . '">' . esc_html($t->name) . '</option>'; ?></select>
				<label class="screen-reader-text" for="hs-loc">Khu vực</label>
				<select id="hs-loc" name="location"><option value="">Khu vực</option><?php foreach ($locations as $t) echo '<option value="' . esc_attr($t->slug) . '">' . esc_html($t->name) . '</option>'; ?></select>
				<label class="screen-reader-text" for="hs-kw">Từ khóa</label>
				<input id="hs-kw" type="search" name="keyword" placeholder="Từ khóa, tên dự án, đường, phường…">
				<button class="btn" type="submit"><?php echo nvr_icon('search', 18); ?><span>Tìm kiếm</span></button>
			</form>
		</div>
	</div>

	<nav class="mobile-menu" id="mobile-menu" aria-label="Menu di động" hidden>
		<div class="container">
			<?php if (nvr_use_wp_menu()) :
				wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'mobile-list', 'depth' => 2]);
			else :
				foreach (['sale' => 'Mua bán', 'rent' => 'Cho thuê'] as $deal => $label) : ?>
					<details class="mobile-acc">
						<summary><?php echo esc_html($label); ?> <?php echo nvr_icon('chevron', 16); ?></summary>
						<div class="mobile-acc-body">
							<a href="<?php echo esc_url(nvr_archive_url('property', ['deal' => $deal])); ?>">Tất cả <?php echo esc_html(mb_strtolower($label, 'UTF-8')); ?></a>
							<?php foreach (nvr_nav_terms('property_type', 6) as $t) echo '<a href="' . esc_url(nvr_archive_url('property', ['deal' => $deal, 'type' => $t->slug])) . '">' . esc_html($t->name) . '</a>'; ?>
							<?php foreach (nvr_nav_terms('property_location', 7) as $t) echo '<a href="' . esc_url(nvr_archive_url('property', ['deal' => $deal, 'location' => $t->slug])) . '">' . esc_html($t->name) . '</a>'; ?>
						</div>
					</details>
				<?php endforeach; ?>
				<a href="<?php echo esc_url(nvr_archive_url('project')); ?>">Dự án</a>
				<a href="<?php echo esc_url(nvr_archive_url('neighborhood')); ?>">Khu dân cư</a>
				<a href="<?php echo esc_url(nvr_page_url('ve-chung-toi')); ?>">Về chúng tôi</a>
				<a href="<?php echo esc_url(nvr_page_url('ky-gui')); ?>">Ký gửi bất động sản</a>
				<a href="<?php echo esc_url(nvr_page_url('tinh-vay')); ?>">Tính khoản vay</a>
				<a href="<?php echo esc_url(nvr_posts_page_url()); ?>">Tin tức</a>
				<a href="<?php echo esc_url(nvr_page_url('lien-he')); ?>">Liên hệ</a>
			<?php endif; ?>
		</div>
	</nav>
</header>

<main id="main" class="site-main">
