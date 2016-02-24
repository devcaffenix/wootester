jQuery( function( $ ){

	var filter_field_template = '<li style="display:none;"><input type="text" value="" name="wft_filter[]" id="wft_filter_0" class="short" /><span><a href="javascript:void(0);" class="wft-remove-filter">[remove]</a></span></li>';

	$(document).on( 'click', '#wft-add-filter', function(){
		var count_li = $('#wft-filters-list li').length;
		filter_field_template = filter_field_template.replace( 'wft_filter_0',"wft_filter_"+count_li );
		$('#wft-filters-list').append(filter_field_template);
		$('#wft-filters-list li:last').slideDown();
	});

	$(document).on( 'click', '.wft-remove-filter', function(event){
		event.preventDefault();
		var parent =$(this).parent().parent();
		parent.slideUp('slow', function(){
			parent.remove();	
		})
	});

	$("#side-sortables [id^=wft-]").hide();

	$(document).on( 'click', '#side-sortables .inside #taxonomy-product_cat label.selectit', function(event){
		event.stopPropagation();
		var data =  [];
		$('#side-sortables .inside #taxonomy-product_cat label.selectit input').each( function(){
			if( $(this).is(':checked')){
				data.push($(this).val());
			}
		});
		$.post( ajaxurl + "?action=get_filter", {cat_ids:data}, function( res ){
			if( '' != res )
				$("#wft-product-filter-section").html( res );
			else
				$("#wft-product-filter-section").html( '<p>No filters were found!!</p>' );
		} );
	});
});