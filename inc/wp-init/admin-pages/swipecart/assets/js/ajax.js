(function($){
	let ajaxurl = swipecart.ajax_url;
	$('#swipecart-reveal-token-btn').on('click', function(e) {
		e.preventDefault();
		var $thisButton = $(this);
		$thisButton.removeClass('success').addClass('loading');
		$.post(ajaxurl, {
			action: 'swipecart_reveal_tokens',
			_nonce: $thisButton.data('nonce')
		}).done(function (resp) {
			$thisButton.removeClass('loading');
			if(resp.success) {
				$thisButton.addClass('success');
				$thisButton.parent().find("#StoreFrontToken").val(resp.data.woo_token);
			}
		});
	});
})(jQuery);