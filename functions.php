<?php

/****************************************************************

    Genesis Basics

****************************************************************/

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Elmet 2024' );
define( 'CHILD_THEME_URL', 'https://brassbound.com' );
define( 'CHILD_THEME_VERSION', '1.0.1' );


/****************************************************************

    Enqueue Scripts & Styles

****************************************************************/

// no-js class on load
function brassbound_no_js($classes) {
  
  $classes[] = 'no-js';
  
  return $classes;
  
} add_filter( 'body_class', 'brassbound_no_js' );

// disable no-js
function brassbound_html_js_class () {
    echo '<script>document.querySelector(\'body\').classList.remove(\'no-js\');</script>'. "\n";
}
add_action( 'genesis_before_header', 'brassbound_html_js_class', 1 );

// Enqueue Scripts & Styles
function brassbound_enqueue_scripts_styles() {

    wp_enqueue_style( 'adobe-fonts', '//use.typekit.net/qek6ydb.css', array(), CHILD_THEME_VERSION );
    wp_enqueue_style( 'brassbound-css', get_stylesheet_directory_uri() . '/main.css', array(), CHILD_THEME_VERSION );
    wp_enqueue_script( 'brassbound-scripts', get_stylesheet_directory_uri() . '/js/brassbound-scripts-min.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
    
    // Remove Superfish
    wp_deregister_script( 'superfish' );
    wp_deregister_script( 'superfish-args' );
    wp_dequeue_script( 'superfish' );
    wp_dequeue_script( 'superfish-args' );

} add_action( 'wp_enqueue_scripts', 'brassbound_enqueue_scripts_styles' );

// Admin Scripts & Styles
function brassbound_enqueue_editor() {
  wp_enqueue_style( 'adobe-fonts', '//use.typekit.net/qek6ydb.css', array(), CHILD_THEME_VERSION );
    wp_enqueue_style( 'editor-styles', get_stylesheet_directory_uri() . '/editor-style.css', array(), CHILD_THEME_VERSION );
} add_action( 'enqueue_block_editor_assets', 'brassbound_enqueue_editor' );

// Noscript tags
function brassbound_noscript() { ?>
  <noscript>
    <style>
    </style>
  </noscript>
  <svg class="clip-hidden screen-reader-text" aria-hidden="true">
    <clipPath id="arrow-clip" clipPathUnits="objectBoundingBox"><path d="M0.515,0.555 L0.566,0.5 L0.515,0.445,0.154,0.055 L0.102,0,0,0.11 L0.051,0.165 L0.362,0.5,0.051,0.835,0,0.89,0.102,1 L0.153,0.945,0.515,0.555 M0.949,0.555 L1,0.5 L0.949,0.445,0.587,0.055 L0.536,0,0.434,0.11 L0.485,0.165 L0.795,0.5,0.485,0.835,0.434,0.89,0.536,1 L0.587,0.945,0.949,0.555"></clipPath>
  </svg>
<?php } add_action('wp_footer', 'brassbound_noscript');

// Add onclick to Body to fix iPad hover issue
function brassbound_body_attr( $attributes ) {
 $attributes['onclick'] = true; 
 $attributes['onkeypress'] = 'handleEnter(event)';
 return $attributes;
} add_filter( 'genesis_attr_body', 'brassbound_body_attr' );


/****************************************************************

    Supports & Outputs

****************************************************************/
// Adds support for HTML5 markup structure.
add_theme_support(
	'html5', array(
		'caption',
		'comment-form',
		'comment-list',
		'gallery',
		'search-form',
	)
);

// Adds support for accessibility.
add_theme_support(
	'genesis-accessibility', array(
		'404-page',
		'drop-down-menu',
		'rems',
		'search-form',
		'skip-links',
	)
);

// Adds viewport meta tag for mobile browsers.
add_theme_support('genesis-responsive-viewport');

// Add Gutenberg Alignments
function brassbound_alignments() {
  add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'brassbound_alignments' );

// Adds custom logo in Customizer > Site Identity.
add_theme_support(
	'custom-logo', array(
		'height'      => 100,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
	)
);

// Displays custom logo.
add_action( 'genesis_site_title', 'the_custom_logo', 0 );

// Removes Dual-Sidebar Layouts
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'sidebar-content-sidebar' );

// Remove Blog & Archive Page Templates
function brassbound_remove_page_templates( $templates ) {
	unset( $templates['page_blog.php'] );
	unset( $templates['page_archive.php'] );
	return $templates;
} add_filter( 'theme_page_templates', 'brassbound_remove_page_templates' );

// Block-based template parts
add_theme_support( 'block-template-parts' );

// Remove category extras
remove_action( 'admin_init', 'genesis_add_taxonomy_archive_options' );


/****************************************************************

    Navigation

****************************************************************/

// Renames primary and secondary navigation menus.
add_theme_support(
    'genesis-menus', array(
        'primary'   => __( 'Primary Menu', 'brassbound' ),
        'secondary'   => __( 'Mobile Menu', 'brassbound' )
    )
);

// Custom Nav Walker
class BB_Desktop_Walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth=0, $args=[], $id=0) {
    $menu_section = get_field('menu_section', $item);
    if ($menu_section) {
      $item->classes[] = 'menu-item-has-children';
      $item->classes[] = 'has-mega-menu';
    }
    
    $output .= "<li class='" .  implode(" ", $item->classes) . "'>";
    
    if ($item->url && $item->url != '#') {
      if ($item->target != null) {
        $output .= '<a href="' . $item->url . '" target="' . $item->target .'">';
      } else {
        $output .= '<a href="' . $item->url . '">';
      }
    } else {
      $output .= '<span class="linkless" tabindex="0">';
    }
 
    $output .= $item->title; 
    
    if ($args->walker->has_children || $menu_section && $depth == 0) {
      $output .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.385 10" class="down-icon"><path id="angle-down-shprfc-solid" d="M26.892,124.7l.9-.9,6.385-6.385.906-.906L33.277,114.7l-.9.9-5.483,5.483-5.483-5.479-.9-.906L18.7,116.508l.9.9,6.385,6.385Z" transform="translate(-18.7 -114.7)"/></svg>';
    }
 
    if ($item->url && $item->url != '#') {
      $output .= '</a>';
    } else {
      $output .= '</span>';
    }
    
    if ($menu_section) {
      $output .= '<div class="sub-menu sub-menu--mega">';
      $output .= apply_filters( 'the_content', get_the_content(null,false,$menu_section));
      $output .= '</div>';
    }
    
  }
}

// Custom Nav Walker
class BB_Mobile_Walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth=0, $args=[], $id=0) {
    $menu_section = get_field('menu_section', $item);
    if ($menu_section) {
      $item->classes[] = 'menu-item-has-children';
      $item->classes[] = 'has-mega-menu';
    }
    
    $output .= "<li class='" .  implode(" ", $item->classes) . "'>";

    if ($args->walker->has_children || $menu_section) {
      $output .= '<span class="menu-item-wrapper">';
    }
    
    if ($item->url && $item->url != '#') {
      if ($item->target != null) {
        $output .= '<a href="' . $item->url . '" target="' . $item->target .'">';
      } else {
        $output .= '<a href="' . $item->url . '">';
      }
    } else {
      $output .= '<span class="linkless" tabindex="0">';
    }
 
    $output .= $item->title; 
 
    if ($item->url && $item->url != '#') {
      $output .= '</a>';
    } else {
      $output .= '</span>';
    }
    
    if ($args->walker->has_children || $menu_section) {
      $output .= '<button class="submenu-toggle"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.385 10" class="submenu-icon"><path id="angle-down-shprfc-solid" d="M26.892,124.7l.9-.9,6.385-6.385.906-.906L33.277,114.7l-.9.9-5.483,5.483-5.483-5.479-.9-.906L18.7,116.508l.9.9,6.385,6.385Z" transform="translate(-18.7 -114.7)"/></svg><span class="screen-reader-text">' .  __( 'Toggle Sub-Menu', 'brassbound' ). '</span></button></span>';
    }
    if ($menu_section) {
      $output .= '<div class="sub-menu sub-menu--mega">';
      $output .= apply_filters( 'the_content', get_the_content(null,false,$menu_section));
      $output .= '</div>';
    }
  }
}

// Removes output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

// Apply Walker to Menu
function brassbound_menu_args( $args ) {
  if( 'primary' == $args['theme_location'] ) {
    $args['walker'] = new BB_Desktop_Walker();
  }
  if( 'secondary' == $args['theme_location'] ) {
    $args['walker'] = new BB_Mobile_Walker();
    $args['menu_class'] = 'menu genesis-nav-menu menu-mobile';
  }
  return $args;
}
add_filter( 'wp_nav_menu_args', 'brassbound_menu_args' );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav' );

// Repositions secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'brassbound_mobile_menu', 'genesis_do_subnav');

// Inset Mobile Menu Button
function brassbound_mobile_toggle() { ?>
    <button class="menu-toggle"><svg width="100%" height="100%" viewBox="0 0 500 300" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" class="toggle-icon" aria-hidden="true"> <g transform="matrix(1,0,0,1,-52.9449,-103.528)"> <g transform="matrix(1.03536,0,0,0.991213,-5.91155,-29.1244)"> <rect x="56.846" y="133.829" width="482.925" height="50.443"/> </g> <g transform="matrix(1.03536,0,0,0.991213,-5.91155,220.145)"> <rect x="56.846" y="133.829" width="482.925" height="50.443"/> </g> <g transform="matrix(1.03536,0,0,0.991213,-5.91155,95.5102)"> <rect x="56.846" y="133.829" width="482.925" height="50.443"/> </g> </g> </svg><span class="screen-reader-text"><?php _e( 'Open Menu', 'brassbound' ) ?></span></button>
<?php } add_action('genesis_header', 'brassbound_mobile_toggle', 11);

// Insert Mobile Menu Container
function brassbound_mobile_container() { ?>
    <div class="mobile-nav">
        <button class="menu-close"><svg width="100%" height="100%" viewBox="0 0 389 389" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" class="toggle-icon" aria-hidden="true"> <g transform="matrix(1,0,0,1,-96.4586,-97.8764)"> <g transform="matrix(0.732109,-0.732109,0.700893,0.700893,-38.9589,399.248)"> <rect x="56.846" y="133.829" width="482.925" height="50.443"> </g> <g transform="matrix(-0.732109,-0.732109,-0.700893,0.700893,620.785,399.248)"> <rect x="56.846" y="133.829" width="482.925" height="50.443"/> </g> </g> </svg><?php _e( 'Close Menu', 'brassbound' ) ?></button>
        <?php do_action('brassbound_mobile_menu'); ?>
    </div>
<?php } add_action('genesis_after', 'brassbound_mobile_container');


/****************************************************************

    Widgets

****************************************************************/

//* Enable the block-based widget editor
add_filter( 'use_widgets_block_editor', '__return_true' );

// Remove header right
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Superheader Template Output
function brassbound_superheader() { ?>
  <div class="superheader">
    <?php block_template_part( 'superheader' ); ?>
  </div>
<?php } add_action('genesis_before', 'brassbound_superheader');

// Footer Template Output
function brassbound_footer() { ?>
  <div class="footer-wrap">
    <?php block_template_part( 'footer' ); ?>
    <div class="site-credits">
      Copyright &copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?> <span class="dev">Developed by <a href="https://brassbound.com" target="_blank" aria-label="Brassbound (Opens in New Tab)">Brassbound</a></span>
    </div>
  </div>
<?php } add_action('genesis_before_footer', 'brassbound_footer');

remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

/****************************************************************

    Custom Blocks

****************************************************************/

function brassbound_register_acf_blocks() {
  
    $containers = array('core/cover','core/group');
    $titles = array('core/heading','core/post-title', 'core/query-title');
    $fade_els = array_merge($containers,$titles);
    array_push($fade_els, 'core/image', 'core/post-image', 'core/column', 'core/button', 'core/columns', 'core/buttons', 'core/paragraph', 'core/quote');
    
    foreach($containers as $el) {
      register_block_style($el, [
        'name' => 'page-header',
        'label' => __('Page Header', 'brassbound'),
      ]);
      register_block_style($el, [
        'name' => 'arrow-right',
        'label' => __('Arrow Right', 'brassbound'),
      ]);
      register_block_style($el, [
        'name' => 'arrow-left',
        'label' => __('Arrow Left', 'brassbound'),
      ]);
    }
    
    foreach($titles as $el) {
      register_block_style($el, [
        'name' => 'page-title',
          'label' => __('Page Title', 'brassbound'),
      ]);
      register_block_style($el, [
        'name' => 'angle-left',
          'label' => __('Angle Left', 'brassbound'),
      ]);
      register_block_style($el, [
        'name' => 'angle-right',
          'label' => __('Angle Right', 'brassbound'),
      ]);
    }
    
    foreach($fade_els as $el) {
      register_block_style($el, [
        'name' => 'fade-in',
          'label' => __('Fade In', 'brassbound'),
      ]);
      
      register_block_style($el, [
        'name' => 'fade-in--left',
          'label' => __('Fade In Left', 'brassbound'),
      ]);
      
      register_block_style($el, [
        'name' => 'fade-in--right',
          'label' => __('Fade In Right', 'brassbound'),
      ]);
    }
    
    register_block_style('core/group', [
      'name' => 'site-footer',
      'label' => __('Site Footer', 'brassbound'),
    ]);
    
    register_block_style('core/column', [
      'name' => 'image-column',
      'label' => __('Image Column', 'brassbound'),
    ]);
    
    register_block_style('core/column', [
      'name' => 'vert-center',
      'label' => __('Vertical Centered', 'brassbound'),
    ]);
    
    register_block_style('core/query', [
      'name' => 'blog-grid',
      'label' => __('Blog Grid', 'brassbound'),
    ]);
    
    register_block_style('core/navigation-link', [
      'name' => 'column-header',
      'label' => __('Column Header', 'brassbound'),
    ]);
    
    
    $button_vars = array('yellow-button', 'white-button', 'accent-link');
    foreach ($button_vars as $var) {
      $label = ucwords(str_replace('-',' ',$var));
      register_block_style('core/button', [
        'name' => $var,
        'label' => __($label, 'brassbound'),
      ]);
    }
    
    // Register Blocks
    register_block_type( __DIR__ . '/blocks/credits' );
    
} add_action( 'init', 'brassbound_register_acf_blocks', 5 );

// Register Block Scripts
function brassbound_register_block_script() {
 //wp_register_script( 'script-name', get_stylesheet_directory_uri() . '', [ 'jquery', 'acf' ] );
} add_action( 'init', 'brassbound_register_block_script' ); 

// Disable Front-End ACF InnerBlocks Wrapper
function acf_should_wrap_innerblocks( $wrap, $name ) {
    return false;
} add_filter( 'acf/blocks/wrap_frontend_innerblocks', 'acf_should_wrap_innerblocks', 10, 2 );

// Remove Default Patterns
function brassbound_remove_patterns() {
    remove_theme_support('core-block-patterns');
} add_action('after_setup_theme', 'brassbound_remove_patterns');

/****************************************************************

    ACF Customizations

****************************************************************/

// Filter ACF Index to Start at 0
add_filter('acf/settings/row_index_offset', '__return_zero');

/****************************************************************

    WooCommerce

****************************************************************/

// Declare WooCommerce Support
add_theme_support( 'woocommerce' );

// enable taxonomy fields for woocommerce with gutenberg on
function enable_taxonomy_rest( $args ) {
    $args['show_in_rest'] = true;
    return $args;
}
add_filter( 'woocommerce_taxonomy_args_product_cat', 'enable_taxonomy_rest' );
add_filter( 'woocommerce_taxonomy_args_product_tag', 'enable_taxonomy_rest' );

add_filter('woocommerce_register_post_type_product', function( $args ) {
    unset( $args['template'] );
    return $args;
});

// Enqueue Woo Scripts
function brassbound_woo_scripts() {
    if (class_exists( 'WooCommerce' ) && is_woocommerce()) {
        wp_enqueue_script( 'brassbound-woo-scripts', get_stylesheet_directory_uri() . '/js/brassbound-woo-scripts-min.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
    }
} 
add_action('wp_enqueue_scripts', 'brassbound_woo_scripts');
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


// Setup Woo Functions
function brassbound_woo_setup() {
    if (class_exists( 'WooCommerce' )) {
        if (is_woocommerce() || is_account_page() || is_cart() || is_checkout()) {
            add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
        }
    }
} add_action('genesis_meta', 'brassbound_woo_setup');

/****************************************************************

    Misc.

****************************************************************/

// Add Image Sizes
add_image_size( 'square', 950, 950, true );
add_image_size( 'banner', 2500, 600, true );

// Add Custom Sizes to Block Editor Drop Down
function brassbound_custom_image_size($sizes){
    $custom_sizes = array(
      'square' => __( 'Square', 'brassbound' ),
      'banner' => __( 'Banner', 'brassbound' )
    );
    return array_merge( $sizes, $custom_sizes );
}
add_filter('image_size_names_choose', 'brassbound_custom_image_size');


// Set Max upload size
function filter_site_upload_size_limit( $size ) {
  $size = 25 * 1048576;
  return $size;
} add_filter( 'upload_size_limit', 'filter_site_upload_size_limit', 20 );


// Filter Archive Titles
function brassbound_archive_title( $title ) {

    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    }

    return $title;
}

add_filter( 'get_the_archive_title', 'brassbound_archive_title' );

// More Link
function brassbound_more_link() {
	return '... <a class="wp-block-post-excerpt__more-link" href="' . get_permalink() . '">' . __( 'Read More', 'brassbound' ) . '</a>';
} add_filter( 'get_the_content_more_link', 'brassbound_more_link' );

// Remove Entry Header Meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );


// Author Gravatar
function brassbound_author_box_gravatar( $size ) {

	return 120;

} add_filter( 'genesis_author_box_gravatar_size', 'brassbound_author_box_gravatar' );

// Re-Arrange Comment Fields
function brassbound_move_comment_field_to_bottom( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;
    return $fields;
} add_filter( 'comment_form_fields', 'brassbound_move_comment_field_to_bottom' );

// Gravatar Size in Comments
function brassbound_comments_gravatar( $args ) {

	$args['avatar_size'] = 90;
	return $args;

} add_filter( 'genesis_comment_list_args', 'brassbound_comments_gravatar' );

/** Remove the edit link */
add_filter ( 'genesis_edit_post_link' , '__return_false' );

