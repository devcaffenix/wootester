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
	}

	public function admin(){
		wp_enqueue_script( 'wft-admin', WFT_URL . 'assets/js/admin/wft-admin.js' );
	}

	public function front(){

	}
}

new WFT_Assets();