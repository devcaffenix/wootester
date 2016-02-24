<?php

class WFT_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'wft-widget',
			'description' => 'WFT Widget',
		);
		parent::__construct( 'wft_widget', 'WFT Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$action_url = get_permalink( woocommerce_get_page_id( 'shop' ) );

		echo '<div class="wft-widget-wrapper">';
			echo '<form name="" action="' .$action_url. '" method="get">';
				if( isset( $_GET['orderby'] ) && '' != $_GET['orderby'] ){
					echo '<input type="hidden" name="orderby" value="'.$_GET['orderby'].'" /> ';
				}

				echo '<div class="wft-widget-field">';
					echo '<label for="product_cat">' . __( 'Category', 'woo-filter-tax' ) . '</label>';
					$product_cat_args = array(
						'show_option_all'    => '',
						'show_option_none'   => __( 'Select cateogry', 'wft-filter-tax' ),
						'option_none_value'  => '-1',
						'hide_empty'         => 0, 
						'selected'           => isset( $_GET['wft_filter_product_cat'] )?absint( $_GET['wft_filter_product_cat'] ):"",
						'hierarchical'       => 1, 
						'name'               => 'wft_filter_product_cat',
						'id'                 => 'product_cat',
						'class'              => 'short select',
						'taxonomy'           => 'product_cat',
						'hide_if_empty'      => false,
						'value_field'	     => 'term_id',	
					);

					wp_dropdown_categories( $product_cat_args );
				echo '</div>';

				echo '<div id="wft-widget-response">';
				if( isset( $_GET['wft_filter_product_cat'] ) && '' !== $_GET['wft_filter_product_cat']  ){
					$taxonomies = wft_get_tax_id( $_GET );
					wft_get_filter_fields( $_GET['wft_filter_product_cat'], $taxonomies );
				}

				echo '</div>';
			echo '</form>';
		echo '</div>';
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}


add_action( 'widgets_init', 'wft_register_wiget');

function wft_register_wiget(){
	register_widget( 'WFT_Widget' );
}


function exclude_category( $query ) {
    if ( is_shop() && $query->is_main_query() ) {
    	if( !empty( $_GET ) ){
    		$tax_query = array();
    		$taxonomies = wft_get_tax_id( $_GET );
    		if( !empty( $taxonomies ) ){
    			foreach ($taxonomies as $key => $value) {
    				$tax_query[] = array(
							'taxonomy' => $key,
							'field'    => 'id',
							'terms'    => $value,
						);
    			}
    			$query->set( 'tax_query', $tax_query );
    		}
    	}
    }
}
add_action( 'pre_get_posts', 'exclude_category' );