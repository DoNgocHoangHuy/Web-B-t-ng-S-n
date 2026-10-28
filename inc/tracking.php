<?php
/**
 * GA4 + Meta Pixel.
 *
 * SDKs are loaded lazily (first user interaction, or ~3s after page load) so they do not hurt PageSpeed.
 * Events are queued by gtag()/fbq() stubs defined immediately, so nothing is lost.
 *
 * Events (fired from assets/js/main.js):
 *   click_hotline, click_zalo, click_facebook   -> links with data-track
 *   submit_form                                  -> contact form submit
 *   generate_lead (GA4) / Lead (Pixel)           -> thank-you page (/cam-on/)
 */
if (!defined('ABSPATH')) exit;

function nvr_valid_ga4($id) { return (bool) preg_match('/^(G|GT|AW)-[A-Z0-9]{4,20}$/', $id); }
function nvr_valid_pixel($id) { return (bool) preg_match('/^\d{8,20}$/', $id); }

function nvr_tracking_head() {
	if (is_admin() || (function_exists('wp_is_serving_rest_request') && wp_is_serving_rest_request())) return;
	$ga = nvr_opt('ga4_id');
	$px = nvr_opt('meta_pixel_id');
	$ga = nvr_valid_ga4($ga) ? $ga : '';
	$px = nvr_valid_pixel($px) ? $px : '';
	if (!$ga && !$px) return;
	?>
<script id="nvr-tracking">
window.dataLayer=window.dataLayer||[];
<?php if ($ga): ?>function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',<?php echo wp_json_encode($ga); ?>);
<?php endif; ?>
<?php if ($px): ?>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];}(window,document);fbq('init',<?php echo wp_json_encode($px); ?>);fbq('track','PageView');
<?php endif; ?>
(function(){var loaded=false;function load(){if(loaded)return;loaded=true;
<?php if ($ga): ?>var g=document.createElement('script');g.async=true;g.src='https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js($ga); ?>';document.head.appendChild(g);
<?php endif; ?>
<?php if ($px): ?>var p=document.createElement('script');p.async=true;p.src='https://connect.facebook.net/en_US/fbevents.js';document.head.appendChild(p);
<?php endif; ?>
}
['scroll','click','touchstart','keydown','mousemove'].forEach(function(e){window.addEventListener(e,load,{once:true,passive:true})});
window.addEventListener('load',function(){setTimeout(load,3500)});})();
</script>
<?php if ($px): ?><noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=<?php echo esc_attr($px); ?>&ev=PageView&noscript=1"></noscript>
<?php endif;
}
add_action('wp_head', 'nvr_tracking_head', 2);
