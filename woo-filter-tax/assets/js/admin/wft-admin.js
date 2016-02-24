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

	$(document).on( 'click', '#side-sortables .inside #taxonomy-product_cat label.selectit, .inline-edit-col .product_cat-checklist li label.selectit', function(event){
		event.stopPropagation();
		var data =  [];
		$('#side-sortables .inside #taxonomy-product_cat label.selectit input, .inline-edit-col .product_cat-checklist li label.selectit input').each( function(){
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
    

	$( '#bulk_edit' ).live( 'click', function() {
	  
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids = new Array();
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});

		// get the data
		var $book_author = $bulk_row.find( 'terxtarea[name="book_author"]' ).val();
		var $inprint = $bulk_row.find( 'input[name="inprint"]' ).attr('checked') ? 1 : 0;
        var $wft_filter = $bulk_row.find( '.select' ).serialize();
        
		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'save_bulk_edit_filter', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				book_author: $book_author,
				inprint: $inprint,
                wft_filter: $wft_filter
			}
		});
	});
});