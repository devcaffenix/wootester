<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Assets
{
	
	function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front' ) );
	}

	public function admin(){
		wp_enqueue_script( 'wft-admin', WFT_URL . 'assets/js/admin/wft-admin.js' );
        wp_enqueue_style( 'wft-front-style', WFT_URL . 'assets/css/admin/wft-style.css' );
	}

	public function front(){
		wp_register_script( 'wft-front', WFT_URL . 'assets/js/front/wft-front.js' );

		$wft_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'wft-front', 'wft', $wft_array );

		wp_enqueue_script( 'wft-front' );

		wp_enqueue_style( 'wft-front-style', WFT_URL . 'assets/css/front/wft-style.css' );
	}
}

new WFT_Assets();