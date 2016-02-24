<?php
function wft_get_filter_fields( $product_cat, $taxonomies = array() ){
	$product_cat = absint( $product_cat );
    $args['post_type'] = WFT_POST_TYPE;
    $args['meta_query'] = array(
        array(
            'key'     => 'wft_product_cat',
            'value'   => $product_cat,
            )
        );
    $results = get_posts( $args );
    if( !empty( $results ) ):
        foreach ($results as $key => $result) {
            $filters = get_post_meta( $result->ID, 'wft_filter', true );
            if( !empty( $filters ) ){
                foreach ($filters as $key => $filter) {
                        $slug = WFT_Tax_Slug::get( $filter );
                        echo '<div class="wft-widget-field">';
                            echo '<label for="'.$slug.'">' . $filter . '</label>';
                            $filter_args = array(
                                'show_option_all'    => '',
                                'show_option_none'   => __( 'Select Filter', 'wft-filter-tax' ),
                                'option_none_value'  => '-1',
                                'hide_empty'         => 0, 
                                'selected'           => array_key_exists( $slug, $taxonomies)?$taxonomies[$slug]:"",
                                'hierarchical'       => 1, 
                                'name'               => 'wft_filter_'.$slug,
                                'id'                 => $slug,
                                'class'              => 'short select',
                                'taxonomy'           => $slug,
                                'hide_if_empty'      => false,
                                'value_field'        => 'term_id',  
                            );

                            wp_dropdown_categories( $filter_args );
                        echo '</div>';
                }
            }
        }

        echo '<div class="wft-widget-field">';
        echo '<input type="submit" name="filter" value="Filter" />';
        echo '</div>';


    else:
        echo '<div class="wft-widget-no-filters">';
        _e( 'No filters found', 'woo-filter-tax' );
        echo '</div>';
    endif;
}

function wft_get_tax_id( $array ){
	if( empty( $array ) )
		return;
	$taxonomies = array();
	foreach ($array as $key => $value) {
		if( $value > 0 ){
			$pos = strpos($key, 'wft_filter_');
			if( $pos !== false ){
				$taxonomy = str_replace( 'wft_filter_', '', $key );
				$taxonomies[ $taxonomy ] = $value;
			}
		}
	}
	return $taxonomies;
}