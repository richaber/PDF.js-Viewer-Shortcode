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
 * PdfJs Shortcode callback.
 *
 * @param array  $attr    The shortcode attributes.
 * @param string $content The shortcode content (if any).
 * @param string $tag     The name of the shortcode.
 *
 * @return string
 */
function pdfjs_shortcode_handler( $attr, $content, $tag ) {

	/**
	 * Combine user attributes with known attributes and fill in defaults when needed.
	 */
	$attr = shortcode_atts(
		array(
			'url'           => '',
			'viewer_height' => '1360px',
			'viewer_width'  => '100%',
			'fullscreen'    => 'true',
			'download'      => 'true',
			'print'         => 'true',
			'openfile'      => 'false',
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
			'download' => ( 'true' === $attr['download'] ) ? 'true' : 'false',
			'print'    => ( 'true' === $attr['print'] ) ? 'true' : 'false',
			'openfile' => ( 'true' === $attr['openfile'] ) ? 'true' : 'false',
		),
		plugins_url( 'resources/js/pdfjs/web/viewer.html', PDFJS_FILE )
	);

	/**
	 * Anchor markup to the fullscreen viewer.
	 *
	 * @var $fullscreen_link
	 */
	$fullscreen_link = '';

	if ( 'true' === $attr['fullscreen'] ) {
		$fullscreen_link = sprintf(
			'<a href="%1$s">%2$s</a><br />',
			esc_url( $viewer_url ),
			esc_html__( 'View Fullscreen', 'pdfjs-viewer-shortcode' )
		);
	}

	return sprintf(
		'%1$s<iframe width="%2$s" height="%3$s" src="%4$s" ></iframe>',
		$fullscreen_link,
		esc_attr( $attr['viewer_width'] ),
		esc_attr( $attr['viewer_height'] ),
		esc_url( $viewer_url )
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

add_action('wp_enqueue_media', 'include_pdfjs_media_button_js_file');
function include_pdfjs_media_button_js_file() {
	wp_enqueue_script('media_button', plugins_url( 'resources/js/pdfjs-media-button.js', __FILE__ ), array('jquery'), '1.0', true);
}

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
