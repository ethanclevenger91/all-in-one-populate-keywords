jQuery(document).ready(function($) {
	$('.remove-this-key').click(function() {
		$removeThis = $(this).parent('.pop-key-input-wrap');
		$removeThis.addClass('sliding-in');
		setTimeout(function() { $removeThis.remove(); }, 500);
	});
});
function addInput() {
	var $input = jQuery('<div class="pop-key-input-wrap sliding-in"><label class="keyword-label">Keyword:</label><input class="keyword-input" name="keywords[]" type="text" value=""><button type="button" class="remove-this-key">Remove This Key</button></div>');
	jQuery('.keywords-wrapper').append($input);
	setTimeout(function() { $input.removeClass('sliding-in'); }, 0);
}
function removeAllKeys() {
	jQuery('.pop-key-input-wrap').addClass('sliding-in');
	setTimeout(function() { jQuery('.pop-key-input-wrap').remove(); }, 500);
}