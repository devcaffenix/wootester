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
        add_action( 'wp_ajax_nopriv_wft_load_filter', array( $this, 'load_filter' ) );
        add_action( 'wp_ajax_wft_load_filter', array( $this, 'load_filter' ) );
	}

    public function load_filter(){
        $id = absint( $_POST['cat_id'] );
        if( empty( $id ) )
            return false;

        wft_get_filter_fields( $id );

        exit;
    }
}

new WFT_Ajax();