/* Gallery picker for property / project / neighborhood edit screens. */
jQuery(function ($) {
	var $input = $('.nvr-gallery-ids');
	if (!$input.length) return;
	var $preview = $('.nvr-gallery-preview');
	function sync() {
		var ids = $preview.children().map(function () { return $(this).data('id'); }).get();
		$input.val(ids.join(','));
	}
	$preview.sortable({ update: sync });
	$('.nvr-gallery-pick').on('click', function (e) {
		e.preventDefault();
		var frame = wp.media({ title: 'Chọn ảnh thư viện', button: { text: 'Dùng ảnh đã chọn' }, multiple: 'add', library: { type: 'image' } });
		frame.on('open', function () {
			var sel = frame.state().get('selection');
			($input.val() || '').split(',').filter(Boolean).forEach(function (id) {
				var att = wp.media.attachment(id); att.fetch(); sel.add(att);
			});
		});
		frame.on('select', function () {
			$preview.empty();
			frame.state().get('selection').each(function (att) {
				var a = att.toJSON();
				var url = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
				$preview.append($('<span>').attr('data-id', a.id).data('id', a.id).append($('<img>').attr('src', url)));
			});
			sync();
		});
		frame.open();
	});
	$('.nvr-gallery-clear').on('click', function (e) { e.preventDefault(); $preview.empty(); sync(); });
});
