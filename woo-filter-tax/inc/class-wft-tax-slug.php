<?php
/**
* Regsiter post type and tax
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFT_Tax_Slug
{
	
	function __construct()
	{
		
	}

	public static function get( $name ){
		return 'wft-' . sanitize_title( $name );
	}
}