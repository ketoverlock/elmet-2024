<?php

/****************************************************************

	WooCommerce Template
	
****************************************************************/

// Remove standard post content output
remove_action( 'genesis_loop', 'genesis_do_loop');

// Remove Archive Description & Title
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
add_filter( 'woocommerce_show_page_title', '__return_null' );

// Remove Product Meta
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Remove Product Title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

// Remove Archive Description
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

function brassbound_woo_header() {
	if (!is_singular('product')):
		block_template_part( 'shop-header' );
	endif;
} add_action('genesis_after_header', 'brassbound_woo_header');

// Add WooCommerce Content
function brassbound_woocommerce_output() {
    	woocommerce_content();
} add_action( 'genesis_loop', 'brassbound_woocommerce_output' );

genesis();