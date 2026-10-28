<?php
/**
 * Nhà Việt Realty - theme bootstrap.
 */
if (!defined('ABSPATH')) exit;

define('NVR_VERSION', '5.4.1');
define('NVR_DIR', get_template_directory());
define('NVR_URI', get_template_directory_uri());

foreach (['helpers', 'template-tags', 'post-types', 'meta-boxes', 'settings', 'query', 'leads', 'ajax', 'seo', 'tracking', 'performance', 'demo-data', 'setup'] as $nvr_module) {
	require_once NVR_DIR . '/inc/' . $nvr_module . '.php';
}
unset($nvr_module);
