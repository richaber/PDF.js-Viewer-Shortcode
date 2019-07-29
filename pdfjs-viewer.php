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

function pdfjs_generator($incoming_from_handler) {
  $viewer_base_url= plugins_url( 'resources/js/pdfjs/web/viewer.html', __FILE__ );


  $file_name = $incoming_from_handler["url"];
  $viewer_height = $incoming_from_handler["viewer_height"];
  $viewer_width = $incoming_from_handler["viewer_width"];
  $fullscreen = $incoming_from_handler["fullscreen"];
  $download = $incoming_from_handler["download"];
  $print = $incoming_from_handler["print"];
  $openfile = $incoming_from_handler["openfile"];

  if ($download != 'true') {
      $download = 'false';
  }

  if ($print != 'true') {
      $print = 'false';
  }

  if ($openfile != 'true') {
      $openfile = 'false';
  }

  $final_url = $viewer_base_url."?file=".$file_name."&download=".$download."&print=".$print."&openfile=".$openfile;

  $fullscreen_link = '';
  if($fullscreen == 'true'){
       $fullscreen_link = '<a href="'.$final_url.'">View Fullscreen</a><br>';
  }
  $iframe_code = '<iframe width="'.$viewer_width.'" height="'.$viewer_height.'" src="'.$final_url.'" ></iframe> ';

  return $fullscreen_link.$iframe_code;
}

//==== Media Button ====

//priority is 12 since default button is 10
add_action('media_buttons', 'pdfjs_media_button', 12);
function pdfjs_media_button() {
    echo '<a href="#" id="insert-pdfjs" class="button">Add PDF</a>';
}

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
