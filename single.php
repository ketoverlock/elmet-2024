<?php


function brassbound_post_hero() {
	block_template_part( 'postheader' );
} add_action('genesis_after_header', 'brassbound_post_hero');

function brassbound_post_footer() {
	block_template_part( 'postfooter' );
} add_action('genesis_before_footer', 'brassbound_post_footer', 5);

// Remove Entry Header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

// Remove Entry Footer
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

genesis();

