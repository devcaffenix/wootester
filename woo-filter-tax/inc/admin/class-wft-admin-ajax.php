<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Admin_Ajax
{
	
	function __construct()
	{
		add_action( 'wp_ajax_get_filter', array( $this, 'get_filter' ) );
		add_action( 'wp_ajax_nopriv_get_filter', array( $this, 'get_filter' ) );
        add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 20, 2 );
        add_action( 'wp_ajax_save_bulk_edit_filter', array($this, 'save_bulk_edit_filter') );	
        add_action( 'wp_ajax_nopriv_save_bulk_edit_filter', array( $this, 'save_bulk_edit_filter' ) );
	}
    
    function save_bulk_edit_filter() {
        
	// TODO perform nonce checking
	// get our variables
	$post_ids           = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
	$book_author  = ( ! empty( $_POST[ 'book_author' ] ) ) ? $_POST[ 'book_author' ] : null;
	$inprint = !! empty( $_POST[ 'inprint' ] );

	// if everything is in order
	if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
		foreach( $post_ids as $post_id ) {
			update_post_meta( $post_id, 'book_author', $book_author );
			update_post_meta( $post_id, 'inprint', $inprint );
            
    
    	    if ( isset( $_POST['wft_filter'] ) && !empty( $_POST['wft_filter'] ) ) {
    	        update_post_meta( $post_id, 'wft_filter', $_POST['wft_filter'] );
    	    }
    		}
	}

	die();
}
   	public function bulk_edit( $column_name, $post_type ) {
        
       
		if ( 'product_type' != $column_name || 'product' != $post_type ) {
			return;
		}
   
        echo "<div class='wtf-bulk-filter'>";
        echo "<legend class='inline-edit-legend'>Select Filter</legend>";
        echo "<div id='wft-product-filter-section'>Please Choose a category to list filters.</div>";
        echo "</div>";

		

	
	}
	function get_filter(){
		if( !defined( 'DOING_AJAX' ) || !DOING_AJAX )
			return false;

		if( empty( $_POST['cat_ids'] ) || !is_array( $_POST['cat_ids'] ) ){
			echo "Please Choose a category to list filters.";
			exit;
		}

		$ids = array_unique( $_POST['cat_ids'] );
		if( empty( $ids ) ){
			echo "Please Choose a category to list filters.";
			exit;
		}

		$args['post_type'] = WFT_POST_TYPE;
		$args['meta_query'] = array(
			array(
				'key'     => 'wft_product_cat',
				'value'   => $ids,
				'compare'   => 'IN'
				)
			);

		$results = new WP_Query( $args );

					echo '<div class="woocommerce_options_panel">';
		if( $results->have_posts() ):

			while( $results->have_posts() ):
				$results->the_post();
			
			$post_metas = get_post_meta( get_the_ID() );
			$filters = unserialize( $post_metas['wft_filter'][0] );
			if( !empty( $filters ) ){
				foreach ($filters as $key => $filter) {
					$slug = WFT_Tax_Slug::get( $filter );
						echo '<p class="form-field coupon_amount_field ">';
							echo '<label for="coupon_amount">'.$filter.'</label>';
								$args = array(
									'show_option_all'    => '',
									'show_option_none'   => __( 'Select Filter', 'wft-filter-tax' ),
									'option_none_value'  => '-1',
									'hide_empty'         => 0, 
									// 'selected'           => $post_metas['wft_product_cat'][0],
									'hierarchical'       => 1, 
									'name'               => 'wft_filter['.$slug.']',
									'id'                 => 'wft_product_cat',
									'class'              => 'short select',
									'taxonomy'           => $slug,
									'hide_if_empty'      => false,
									'value_field'	     => 'term_id',	
								);

							wp_dropdown_categories( $args );
							echo '<span class="woocommerce-help-tip"></span>';
						echo '</p>';
				}

			}

			endwhile;
		endif;
					echo '</div>';

		exit;
	}
}

new WFT_Admin_Ajax();