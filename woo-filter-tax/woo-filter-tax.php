<?php
/**
* Plugin Name: WooCommerce Filters
* Description: WooCommerce  filter plugin
* Plugin URI: http://kodiary.com
* Author: caffenix
* Author URI: http://kodairy.com
* Version: 1.0
* License: GPL2
* Text Domain: woo-filter-tax
* Domain Path: /languages/
*/

/*
Copyright (C) 2016  Kodiary inof@kodiary.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'plugins_loaded', array( 'woo_filter_tax', 'get_instance' ) );
register_activation_hook( __FILE__, array( 'woo_filter_tax', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'woo_filter_tax', 'deactivate' ) );
// register_uninstall_hook( __FILE__, array( 'woo_filter_tax', 'uninstall' ) );

class woo_filter_tax {

	private static $instance = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self;

		return self::$instance;
	}

	private function __construct() {

		$this->define_constants();
		$this->includes();

	}

	private function includes(){

		if( $this->is_request( 'admin' ) ){
			include WFT_BASE . '/inc/admin/class-wft-meta-boxes.php';
			include WFT_BASE . '/inc/admin/class-wft-admin-ajax.php';
		}
		
		include WFT_BASE . '/inc/wft-functions.php';
		include WFT_BASE . '/inc/class-wft-assets.php';
		include WFT_BASE . '/inc/class-wft-register.php';
		include WFT_BASE . '/inc/class-wft-tax-slug.php';
		include WFT_BASE . '/inc/class-wft-ajax.php';
		include WFT_BASE . '/inc/class-wft-widget.php';
		include WFT_BASE . '/inc/wft-template-tags.php';
	}

	private function define_constants(){

		$this->define( 'WFT_BASE', dirname( __FILE__ ) );
		$this->define( 'WFT_URL', plugins_url( '/', __FILE__ ) );
		$this->define( 'WFT_POST_TYPE', 'wft_filter_tax' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	public static function activate() {}

	public static function deactivate() {}

/*
	public static function uninstall() {
		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
			return;
	}
*/
}
