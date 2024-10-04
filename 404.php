<?php

/* Template for 404 Page */

// Full Width Layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

function brassbound_404_body($classes) {
	
	$classes[] = 'page-template-page_builder';
	
	return $classes;
	
} add_filter( 'body_class', 'brassbound_404_body' );

function brassbound_404() {
	block_template_part( '404' );
}

// Replace Default Content
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action('genesis_loop', 'brassbound_404');


genesis();