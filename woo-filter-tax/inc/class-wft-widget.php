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
		$_args = array();
		$_args['post_type'] = WFT_POST_TYPE;
		$_args['posts_per_page'] = -1;
		$results = get_posts( $_args );
		
		$product_cat_args = array(
			'show_option_all'    => '',
			'show_option_none'   => __( 'Select cateogry', 'wft-filter-tax' ),
			'option_none_value'  => '-1',
			'hide_empty'         => 0, 
			// 'selected'           => $post_metas['wft_product_cat'][0],
			'hierarchical'       => 1, 
			'name'               => 'wft_filter[product_cat]',
			'id'                 => 'product_cat',
			'class'              => 'short select',
			'taxonomy'           => 'product_cat',
			'hide_if_empty'      => false,
			'value_field'	     => 'term_id',	
		);

		wp_dropdown_categories( $product_cat_args );

		if( !empty( $results ) ):
			foreach ($results as $key => $result) {
				$filters = get_post_meta( $result->ID, 'wft_filter', true );
				if( !empty( $filters ) ){
					foreach ($filters as $key => $filter) {
						echo $filter;
						$slug = WFT_Tax_Slug::get( $filter );
						$filter_args = array(
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

						wp_dropdown_categories( $filter_args );
					}
				}
			}

		endif;

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