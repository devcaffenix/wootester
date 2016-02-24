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
	}

	function get_filter(){
		if( !defined( 'DOING_AJAX' ) || !DOING_AJAX )
			return false;

		$ids = array_unique( $_POST['cat_ids'] );
		if( empty( $ids ) )
			return false;

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