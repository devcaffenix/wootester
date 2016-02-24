<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Meta_Boxes
{
	
	function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'register' ), 0 );
		add_action( 'save_post',  array( $this, 'filter_save' ), 10, 2 );
		add_action( 'save_post',  array( $this, 'product_save' ), 10, 2 );
	}

	function register(){
		add_meta_box( 'wft-product-cat', __( 'Choose Product Category', 'wft-filter-tax' ), array( $this, 'product_cat_callback' ), WFT_POST_TYPE );

		add_meta_box( 'wft-product-filter', __( 'Filters', 'wft-filter-tax' ), array( $this, 'product_filter_callback' ), 'product', 'advanced', 'low' );
	}

	function product_cat_callback( $post ) {

		$post_metas = get_post_meta( $post->ID );
		include WFT_BASE . '/views/admin/filters-fields.php';
	}

	function product_filter_callback(){
		global $post;
		echo '<div id="wft-product-filter-section">';
			echo '<div class="woocommerce_options_panel">';
			$wft_old_filters = get_post_meta( $post->ID, '_wft_old_filters', true );

			if( !empty( $wft_old_filters ) ){
				foreach ($wft_old_filters as $filter => $filter_id) {
					$get_taxonomy = get_taxonomy( $filter );
					if( $get_taxonomy ):
						echo '<p class="form-field coupon_amount_field ">';
							$_filter = ucfirst( str_replace( array('wft-', '-'), array(' ',' ' ), $filter ) );
							echo '<label for="coupon_amount">'.$_filter.'</label>';
								$args = array(
									'show_option_all'    => '',
									'show_option_none'   => __( 'Select Filter', 'wft-filter-tax' ),
									'option_none_value'  => '-1',
									'hide_empty'         => 0, 
									'selected'           => $filter_id,
									'hierarchical'       => 1, 
									'name'               => 'wft_filter['.$filter.']',
									'id'                 => 'wft_product_cat',
									'class'              => 'short select',
									'taxonomy'           => $filter,
									'hide_if_empty'      => false,
									'value_field'	     => 'term_id',	
								);

							wp_dropdown_categories( $args );
							echo '<span class="woocommerce-help-tip"></span>';
						echo '</p>';
					endif;
				}
			}
			echo '</div>';
		echo '</div>';
		return;

		$product_cats = wp_get_post_terms($post->ID, 'product_cat', array("fields" => "ids"));
		$filter_terms = wp_get_post_terms($post->ID, $wft_old_filters, array("fields" => "ids"));
		$args['post_type'] = WFT_POST_TYPE;
		$args['meta_query'] = array(
			array(
				'key'     => 'wft_product_cat',
				'value'   => $product_cats,
				'compare'   => 'IN'
				)
			);

		$results = new WP_Query( $args );

		echo '<div class="woocommerce_options_panel">';
		if( $results->have_posts() ):
			while( $results->have_posts() ):
				$results->the_post();
			
			$post_metas = get_post_meta( get_the_ID() );
			if( isset( $post_metas['wft_filter'][0] ) ){
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
									'selected'           => (in_array( $post_metas['wft_product_cat'][0], $filter_terms))?$post_metas['wft_product_cat'][0]:"",
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
			}

			endwhile;
			wp_reset_postdata();
		endif;
		echo '</div>';
		echo '</div>';
	}

	function filter_save( $post_id, $post ){

		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// If this isn't a 'book' post, don't update it.
	    if ( WFT_POST_TYPE != $post->post_type ) {
	        return;
	    }

	    // Update the post's metadata.
	    if ( isset( $_POST['wft_product_cat'] ) ) {
	        update_post_meta( $post_id, 'wft_product_cat', sanitize_text_field( $_POST['wft_product_cat'] ) );
	    }

	    if ( isset( $_POST['wft_filter'] ) && !empty( $_POST['wft_filter'] ) ) {
	        update_post_meta( $post_id, 'wft_filter', $_POST['wft_filter'] );
	    }

	}

	function product_save( $post_id, $post ){
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// If this isn't a 'book' post, don't update it.
	    if ( 'product' != $post->post_type ) {
	        return;
	    }

	    if( empty( $_POST['wft_filter'] ) )
	    	return;

	    $wft_filters = get_post_meta( $post_id, '_wft_old_filters', true );
    	$wft_new_filters = $_POST['wft_filter'];

	    if( !empty( $wft_filter ) ){
	    	wp_delete_object_term_relationships( $post_id, $wft_filters );
	    }

	    update_post_meta( $post_id, '_wft_old_filters', $wft_new_filters );
	    foreach ( $_POST['wft_filter'] as $key => $filter) {
	    	if( $filter ){
	    		wp_set_post_terms( $post_id, $filter, $key );
	    	}
	    }
	}
}

new WFT_Meta_Boxes();