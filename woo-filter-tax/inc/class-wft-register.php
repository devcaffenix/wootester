<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Register
{
	
	function __construct()
	{
		add_action( 'init', array( $this, 'post_types' ), 5 );
		add_action( 'init', array( $this, 'taxonomies' ), 5 );
	}

	function post_types(){
		register_post_type( WFT_POST_TYPE,
			array(
				'labels'              => array(
						'name'               => __( 'Filters', 'woocommerce' ),
						'singular_name'      => __( 'Filter', 'woocommerce' ),
						'menu_name'          => _x( 'Filters', 'Admin menu name', 'woocommerce' ),
						'add_new'            => __( 'Add Filter', 'woocommerce' ),
						'add_new_item'       => __( 'Add New Filter', 'woocommerce' ),
						'edit'               => __( 'Edit', 'woocommerce' ),
						'edit_item'          => __( 'Edit Filter', 'woocommerce' ),
						'new_item'           => __( 'New Filter', 'woocommerce' ),
						'view'               => __( 'View Filters', 'woocommerce' ),
						'view_item'          => __( 'View Filter', 'woocommerce' ),
						'search_items'       => __( 'Search Filters', 'woocommerce' ),
						'not_found'          => __( 'No Filters found', 'woocommerce' ),
						'not_found_in_trash' => __( 'No Filters found in trash', 'woocommerce' ),
						'parent'             => __( 'Parent Filter', 'woocommerce' )
					),
				'description'         => __( 'This is where you can add new Filters that customers can use in your store.', 'woocommerce' ),
				'public'              => false,
				'show_ui'             => true,
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true
			)
		);
	}

	function taxonomies(){

		$args['post_type'] = WFT_POST_TYPE;
		$args['posts_per_page'] = -1;

		$results = new WP_Query( $args );

		if( $results->have_posts() ):

			while( $results->have_posts() ):
				$results->the_post();
			
			$post_metas = get_post_meta( get_the_ID() );
			$filters = unserialize( $post_metas['wft_filter'][0] );
			if( !empty( $filters ) ){
				foreach ($filters as $key => $filter) {
					$slug = "wft-".sanitize_title( $filter );
					register_taxonomy(
				        $slug,
				        'product',
				        array(
				            'label' => __( $filter, 'wft-filter-tax' ),
				            'public' => true,
				            'show_in_menu' => false,
				            'show_in_quick_edit' => false,
				            'rewrite' => true,
				            'hierarchical' => true,
				        )
				    );
				}

			}

			endwhile;
		endif;
	}
}

new WFT_Register();