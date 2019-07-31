<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the Gutenberg block.
 *
 * @package PdfJsViewer
 */

/**
 * Exit early if directly accessed via URL.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function pdfjs_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$dir = dirname( __FILE__ );

	$index_js = 'pdfjs/build/index.js';

	wp_register_script(
		'pdfjs-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-editor',
			'wp-element',
			'wp-i18n',
			'wp-polyfill',
		),
		filemtime( "$dir/$index_js" ),
		true
	);

	$editor_css = 'pdfjs/src/editor.css';

	wp_register_style(
		'pdfjs-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'dashicons',
		),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'pdfjs/src/style.css';

	wp_register_style(
		'pdfjs-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type(
		'pdfjs-viewer-shortcode/pdfjs',
		array(
			'editor_script'   => 'pdfjs-block-editor',
			'editor_style'    => 'pdfjs-block-editor',
			'style'           => 'pdfjs-block',
			'render_callback' => 'pdfjs_generator',
			'attributes'      => array(
				'url'           => array(
					'type'    => 'string',
					'default' => '',
				),
				'title'         => array(
					'type'    => 'string',
					'default' => __( 'Embedded PDF Document', 'pdfjs-viewer-shortcode' ),
				),
				'viewer_height' => array(
					'type'    => 'string',
					'default' => '1360px',
				),
				'viewer_width'  => array(
					'type'    => 'string',
					'default' => '100%',
				),
				'fullscreen'    => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'download'      => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'print'         => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'openfile'      => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'page'          => array(
					'type'    => 'integer',
					'default' => 1,
				),
				'zoom'          => array(
					'type'    => 'string',
					'default' => 'auto',
				),
				'customZoom'    => array(
					'type'    => 'string',
					'default' => '',
				),
				'nameddest'     => array(
					'type'    => 'string',
					'default' => '',
				),
				'pagemode'      => array(
					'type'    => 'string',
					'default' => 'none',
				),
			),
		)
	);

	wp_set_script_translations( 'pdfjs-block-editor', 'pdfjs-viewer-shortcode' );
}

add_action( 'init', 'pdfjs_block_init' );
