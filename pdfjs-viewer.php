<?php
/**
 * Plugin Name:       PDFjs Viewer
 * Plugin URI:        https://byterevel.com
 * Description:       Embed PDFs with the gorgeous PDF.js viewer
 * Version:           1.4
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Author:            Ben Lawson
 * Author URI:        https://byterevel.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       pdfjs-viewer-shortcode
 * Domain Path:       /languages
 *
 * @package           PdfJsViewer
 */

/**
 * Exit early if directly accessed via URL.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The full path and filename of this plugin bootstrap file with symlinks resolved.
 *
 * @var string PDFJS_FILE
 */
define( 'PDFJS_FILE', __FILE__ );

/**
 * Load plugin textdomain.
 */
function pdfjs_load_plugin_textdomain() {
	load_plugin_textdomain(
		'pdfjs-viewer-shortcode',
		false,
		basename( dirname( PDFJS_FILE ) ) . '/languages'
	);
}

add_action( 'plugins_loaded', 'pdfjs_load_plugin_textdomain' );

/**
 * PdfJs Shortcode callback.
 *
 * @param array  $attr    The shortcode attributes.
 * @param string $content The shortcode content (if any).
 * @param string $tag     The name of the shortcode.
 *
 * @return string
 */
function pdfjs_shortcode_handler( $attr = array(), $content = '', $tag = '' ) {

	/**
	 * Combine user attributes with known attributes and fill in defaults when needed.
	 */
	$attr = shortcode_atts(
		array(
			'url'           => '',
			'title'         => __( 'Embedded PDF Document', 'pdfjs-viewer-shortcode' ),
			'viewer_height' => '1360px',
			'viewer_width'  => '100%',
			'fullscreen'    => true,
			'download'      => true,
			'print'         => true,
			'openfile'      => false,
			'page'          => 1,
			'zoom'          => null,
			'nameddest'     => null,
			'pagemode'      => 'none',
		),
		$attr
	);

	return pdfjs_generator( $attr );
}

add_shortcode( 'pdfjs-viewer', 'pdfjs_shortcode_handler' );

/**
 * Construct the markup for the shortcode to return.
 *
 * @param array $attr The shortcode attributes.
 *
 * @return string
 */
function pdfjs_generator( $attr ) {
	/**
	 * The URL to the PDF.js viewer.
	 *
	 * @var string $viewer_url
	 */
	$viewer_url = add_query_arg(
		array(
			'file'     => pdfjs_encode_uri_component( $attr['url'] ),
			'download' => pdfjs_bool_to_string( $attr['download'] ),
			'print'    => pdfjs_bool_to_string( $attr['print'] ),
			'openfile' => pdfjs_bool_to_string( $attr['openfile'] ),
		),
		plugins_url( 'resources/js/pdfjs/web/viewer.php', PDFJS_FILE )
	);

	/**
	 * Fragment Identifiers to append to the Viewer URL.
	 *
	 * @var array $fragments
	 */
	$fragments = array();

	/**
	 * Array of whitelisted fragment identifier keys.
	 *
	 * @var array $fragment_identifier_keys
	 */
	$fragment_identifier_keys = array(
		'page',
		'zoom',
		'nameddest',
		'pagemode',
	);

	/**
	 * Loop through the frag id keys to see if we have an attribute value.
	 */
	foreach ( $fragment_identifier_keys as $fragment_identifier_key ) {
		if ( isset( $attr[ $fragment_identifier_key ] ) && ! empty( $attr[ $fragment_identifier_key ] ) ) {
			$fragments[ $fragment_identifier_key ] = $attr[ $fragment_identifier_key ];
		}
	}

	/**
	 * Edge case for Gutenberg block,
	 * allowing a "custom zoom" to be set
	 * if a standard zoom level isn't sufficient.
	 */
	if ( isset( $attr['zoom'] ) && 'custom' === $attr['zoom'] && isset( $attr['customZoom'] ) ) {
		$fragments['zoom'] = $attr['customZoom'];
	}

	/**
	 * Build the fragment identifier string in the way that PDF.js viewer expects.
	 *
	 * Multiple values of either type can be combined by
	 * separating with an ampersand (&) after the hash (for example: #page=2&zoom=200).
	 *
	 * @link https://github.com/mozilla/pdf.js/wiki/Viewer-options
	 */
	if ( ! empty( $fragments ) ) {
		$viewer_url .= '#' . http_build_query( $fragments );
	}

	/**
	 * Anchor markup to the fullscreen viewer.
	 *
	 * @var $fullscreen_link
	 */
	$fullscreen_link = '';

	if ( true === pdfjs_string_to_bool( $attr['fullscreen'] ) ) {
		$fullscreen_link = sprintf(
			'<a href="%1$s">%2$s</a><br />',
			esc_url( $viewer_url ),
			esc_html__( 'View Fullscreen', 'pdfjs-viewer-shortcode' )
		);
	}

	return sprintf(
		'%1$s<iframe width="%2$s" height="%3$s" src="%4$s" title="%5$s"></iframe>',
		$fullscreen_link,
		esc_attr( $attr['viewer_width'] ),
		esc_attr( $attr['viewer_height'] ),
		esc_url( $viewer_url ),
		esc_attr( $attr['title'] )
	);
}

/**
 * Add the media button.
 *
 * Priority is 12 since default button is 10.
 */
function pdfjs_media_button() {
	echo '<a href="#" id="insert-pdfjs" class="button">' . esc_html__( 'Add PDF', 'pdfjs-viewer-shortcode' ) . '</a>';
}

add_action( 'media_buttons', 'pdfjs_media_button', 12 );

/**
 * Include the media button handler script.
 */
function pdfjs_include_media_button_js_file() {
	wp_enqueue_script(
		'media_button',
		plugins_url( 'resources/js/pdfjs-media-button.js', PDFJS_FILE ),
		array( 'jquery' ),
		'1.0',
		true
	);
}

add_action( 'wp_enqueue_media', 'pdfjs_include_media_button_js_file' );

/**
 * Encode URL compatible with JavaScript's encodeURIComponent.
 *
 * PHP's rawurlencode escapes all non-alphanumeric characters except,
 * -_.~
 *
 * JavaScript's encodeURIComponent escapes all non-alphanumeric characters except the following,
 * -_.!~*'()
 *
 * According to PDF.js documentation,
 * "the path of the PDF file to use (must be on the same server due to JavaScript limitations).
 * Please notice that the path/URL must be encoded using encodeURIComponent."
 *
 * @link https://github.com/mozilla/pdf.js/wiki/Viewer-options
 *
 * @param string $url The URL to encode. This should be a raw, unencoded URL.
 *
 * @return string
 */
function pdfjs_encode_uri_component( $url ) {
	if ( empty( $url ) ) {
		return '';
	}

	/**
	 * Characters that JS' encodeURIComponent does not encode,
	 * but PHP's rawurlencode does.
	 *
	 * @var array $revert
	 */
	$unescape = array(
		'%21' => '!',
		'%2A' => '*',
		'%27' => "'",
		'%28' => '(',
		'%29' => ')',
	);

	return strtr( rawurlencode( $url ), $unescape );
}

/**
 * Get the string 'true' or 'false from a true boolean value.
 *
 * @param bool $variable Value to evaluate as 'true' or 'false'.
 *
 * @return string Returns 'true' for true. Returns 'false' otherwise.
 */
function pdfjs_bool_to_string( $variable ) {
	return ( true === pdfjs_string_to_bool( $variable ) ) ? 'true' : 'false';
}

/**
 * Get the true boolean value of 'true' or 'false' strings.
 *
 * @param mixed $variable Value to evaluate as true or false.
 *
 * @return bool Returns true for "1", "true", "on" and "yes". Returns false otherwise.
 */
function pdfjs_string_to_bool( $variable ) {
	return filter_var( $variable, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Full path to the PDF.js block init file.
 */
$pdfjs_block_include = __DIR__ . '/resources/js/blocks/pdfjs.php';

/**
 * Require the PDF.js block init file if it is readable.
 */
if ( is_readable( $pdfjs_block_include ) ) {
	require $pdfjs_block_include;
}
