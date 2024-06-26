<?php
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
    $the_theme = wp_get_theme();
    $theme_version = $the_theme->get( 'Version' );
    $css_version = $theme_version . '.' . filemtime(get_stylesheet_directory() . '/css/child-theme.min.css');
    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $css_version );
    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/js/popper.min.js', array(), false);
    wp_enqueue_script( 'typed', get_stylesheet_directory_uri() . '/js/typed.js', array(), false);
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
    wp_enqueue_script( 'custom-scripts', get_stylesheet_directory_uri() . '/js/custom.js', array(), false);
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

function add_child_theme_textdomain() {
    load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

// Exclude images from search results - WordPress
add_action( 'init', 'exclude_images_from_search_results' );
function exclude_images_from_search_results() {
	global $wp_post_types;
 
	$wp_post_types['attachment']->exclude_from_search = true;
}

/* CUSTOMIZE EXCERPT READ MORE CONTENT
================================================== */

function understrap_all_excerpts_get_more_link( $post_excerpt ) {

	return $post_excerpt;
}

add_filter( 'wp_trim_excerpt', 'understrap_all_excerpts_get_more_link' );

// Filter to add teal class to body element on specific page templates
add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
    if ( is_home() || is_search() || is_page_template('page-templates/search-page.php') ) {
        $classes[] = 'teal';
    }
    elseif (is_archive() || is_page('calendar') || is_singular('event') || is_page_template('page-templates/no-hero.php') || is_page_template('page-templates/no-hero-advanced.php')){
        $classes[] = 'peach';
    }
    elseif (is_page_template('page-templates/storefront.php') || is_page_template('page-templates/podcast.php') || is_singular('post') || is_page_template('page-templates/no-hero-advanced-white.php') ) {
        $classes[] = 'white-flourish';
    }
    return $classes;
}

add_filter( 'su/data/shortcodes', 'remove_su_shortcodes' );

/**
 * Filter to modify original shortcodes data
 *
 * @param array   $shortcodes Default shortcodes
 * @return array Modified array
 */
function remove_su_shortcodes( $shortcodes ) {

	// Remove button shortcode
    unset( $shortcodes['tabs'] );
    unset( $shortcodes['tab'] );
    unset( $shortcodes['frame'] );
    unset( $shortcodes['column'] );
    unset( $shortcodes['lightbox_content'] );
    unset( $shortcodes['screenr'] );
    unset( $shortcodes['spoiler'] );
    unset( $shortcodes['accordion'] );
    unset( $shortcodes['divider'] );
    unset( $shortcodes['spacer'] );
    unset( $shortcodes['highlight'] );
    unset( $shortcodes['label'] );
    unset( $shortcodes['quote'] );
    unset( $shortcodes['pullquote'] );
    unset( $shortcodes['dropquote'] );
    unset( $shortcodes['row'] );
    unset( $shortcodes['list'] );
    unset( $shortcodes['dropcap'] );
    unset( $shortcodes['service'] );
    unset( $shortcodes['expand'] );
    unset( $shortcodes['lightbox'] );
    unset( $shortcodes['private'] );
    unset( $shortcodes['youtube'] );
    unset( $shortcodes['vimeo'] );
    unset( $shortcodes['dailymotion'] );
    unset( $shortcodes['audio'] );
    unset( $shortcodes['video'] );
    unset( $shortcodes['table'] );
    unset( $shortcodes['permalink'] );
    unset( $shortcodes['members'] );
    unset( $shortcodes['guests'] );
    unset( $shortcodes['menu'] );
    unset( $shortcodes['siblings'] );
    unset( $shortcodes['document'] );
    unset( $shortcodes['slider'] );
    unset( $shortcodes['carousel'] );
    unset( $shortcodes['custom_gallery'] );
    unset( $shortcodes['youtube_advanced'] );
    unset( $shortcodes['feed'] );
    unset( $shortcodes['subpages'] );
    unset( $shortcodes['animate'] );
    unset( $shortcodes['gmap'] );
    unset( $shortcodes['posts'] );
    unset( $shortcodes['dummy_text'] );
    unset( $shortcodes['dummy_image'] );
    unset( $shortcodes['meta'] );
    unset( $shortcodes['user'] );
    unset( $shortcodes['footnote'] );
    unset( $shortcodes['scheduler'] );
    unset( $shortcodes['post'] );
    unset( $shortcodes['template'] );
    unset( $shortcodes['qrcode'] );
    unset( $shortcodes['tooltip'] );
    unset( $shortcodes['note'] );
    unset( $shortcodes['box'] );
    

	// Return modified data
	return $shortcodes;

}

// https://gist.github.com/jlengstorf/ce2470df87fd9a892f68

function setup_theme(  ) {
    // Theme setup code...
    
    // Filters the oEmbed process to run the responsive_embed() function
    add_filter('embed_oembed_html', 'responsive_embed', 10, 3);
}
add_action('after_setup_theme', 'setup_theme');
/**
 * Adds a responsive embed wrapper around oEmbed content
 * @param  string $html The oEmbed markup
 * @param  string $url  The URL being embedded
 * @param  array  $attr An array of attributes
 * @return string       Updated embed markup
 */
function responsive_embed($html, $url, $attr) {
    return $html!=='' ? '<div class="embed-container">'.$html.'</div>' : '';
}

function custom_query_vars_filter($vars) {
    $vars[] .= 'topic';
    return $vars;
  }
  add_filter( 'query_vars', 'custom_query_vars_filter' );
