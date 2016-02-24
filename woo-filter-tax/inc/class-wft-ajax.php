<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Ajax
{
	
	function __construct()
	{
		add_action( 'wp_ajax_nopriv_wft_listener', array( $this, 'listener' ) );
    	add_action( 'wp_ajax_wft_listener', array( $this, 'listener' ) );
	}

	/**
     * Widget ajax listener
     */
    public static function listener(){
        global $wp_query, $wp_rewrite;

        $args = array();

        if( ! isset($args['post__in']) ) {
            $args['post__in'] = array();
        }
        
        $args['post_status'] = 'publish';
        $args['post_type'] = 'product';
        $args['posts_per_page'] = get_option( 'posts_per_page' );

        if( isset($_POST['product_taxonomy']) && $_POST['product_taxonomy'] != '-1' && strpos( $_POST['product_taxonomy'], '|' ) !== FALSE ) {
            $product_taxonomy = explode( '|', $_POST['product_taxonomy'] );
            $args['taxonomy'] = $product_taxonomy[0];
            $args['term'] = $product_taxonomy[1];
        }

        $wp_query = new WP_Query( $args );

        // here we get max products to know if current page is not too big
        if ( $wp_rewrite->using_permalinks() and preg_match( "~/page/([0-9]+)~", $_POST['location'], $mathces ) or preg_match( "~paged?=([0-9]+)~", $_POST['location'], $mathces ) ) {
            $args['paged'] = min( $mathces[1], $wp_query->max_num_pages );
            $wp_query = new WP_Query( $args );
        }
        unset( $args );

        if( @ ! $br_options['ajax_request_load'] ) {
            ob_start();

            if ( $wp_query->have_posts() ) {

                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();
                woocommerce_product_subcategories();

                while ( have_posts() ) {
                    the_post();
                    wc_get_template_part( 'content', 'product' );
                }

                woocommerce_product_loop_end();

                do_action('woocommerce_after_shop_loop');

                wp_reset_postdata();

                $_RESPONSE['products'] = ob_get_contents();
            } else {

                $_RESPONSE['no_products'] = ob_get_contents();
            }
            ob_end_clean();
        }

        echo json_encode( $_RESPONSE );

        die();
    }
}

new WFT_Ajax();