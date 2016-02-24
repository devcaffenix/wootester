jQuery( function($){

	$(document).on( 'change', ".wft-widget-wrapper select#product_cat", function(event){
		var data = {
			'action': 'wft_load_filter',
			'cat_id': $(this).val()
		};

		$.post( wft.ajax_url, data, function(res){

			$(".wft-widget-wrapper #wft-widget-response").html(res);
		});

	});

});