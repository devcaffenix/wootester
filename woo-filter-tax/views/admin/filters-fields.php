<div class="woocommerce_options_panel">
<p class="form-field coupon_amount_field ">
	<label for="coupon_amount">Product Category</label>
	<?php
		$args = array(
		'show_option_all'    => '',
		'show_option_none'   => __( 'Select Category', 'wft-filter-tax' ),
		'option_none_value'  => '-1',
		'hide_empty'         => 0, 
		'selected'           => isset($post_metas['wft_product_cat'][0])?$post_metas['wft_product_cat'][0]:"",
		'hierarchical'       => 1, 
		'name'               => 'wft_product_cat',
		'id'                 => 'wft_product_cat',
		'class'              => 'short select',
		'taxonomy'           => 'product_cat',
		'hide_if_empty'      => false,
		'value_field'	     => 'term_id',	
	);

	wp_dropdown_categories( $args );
	?>
	 <span class="woocommerce-help-tip"></span>
</p>
<p class="form-field coupon_amount_field ">
	<label for="coupon_amount">Filters</label>
	<ul id="wft-filters-list">
	<?php
	if( isset( $post_metas['wft_filter'][0] ) ):
		$filters = unserialize( $post_metas['wft_filter'][0] );
	?>
		<?php
		if( !empty( $filters ) ):
			$index = 0;
			foreach( $filters as $filter ): 
		?>
		<li>
			<input type="text" value="<?php echo $filter; ?>" name="wft_filter[]" id="wft_filter_<?php echo $index; ?>" class="short" />
			<span><a href="javascript:void(0);" class="wft-remove-filter">[remove]</a></span>
			<span><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=wft-'.sanitize_title( $filter ).'&post_type=product');?>">[Add filter items]</a></span>
		</li>
		<?php
				$index++;
				endforeach;
			endif;
		endif;
		?>
	</ul>
	<button type="button" id="wft-add-filter">+ Add</button>
</p>
</div>