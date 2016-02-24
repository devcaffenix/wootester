<?php
function wft_show_filter(){
	global $post;
	$wft_old_filters = get_post_meta( $post->ID, '_wft_old_filters', true );
	if( !empty( $wft_old_filters ) ){
		$filters_count = count( $wft_old_filters );
		foreach ( $wft_old_filters as $taxonomy => $term_id ) {
			$get_term = get_term( (int) $term_id, $taxonomy );
			$term_link = get_term_link( (int) $term_id, $taxonomy );
			$get_taxonomy = get_taxonomy( $taxonomy );
			if( ! is_wp_error( $term_link ) ):
				echo '<div class="product_meta">';
					echo '<span class="posted_in">' . $get_taxonomy->labels->name . ': ';
						echo '<a href="'.esc_url($term_link).'" rel="tag">'.$get_term->name.'</a>';
					echo '</span>';
				echo '</div>';
			endif;
		}
	}

}

add_action( 'woocommerce_single_product_summary', 'wft_show_filter', 45 );