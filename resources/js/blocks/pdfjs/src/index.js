( function ( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its
	 * behavior.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	const { registerBlockType } = wp.blocks

	/**
	 * Returns a new element of given type. Element is an abstraction layer
	 * atop React.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-element/
	 */
	const { createElement, Fragment } = wp.element

	/**
	 * Retrieves the translation of text.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-i18n/
	 */
	const { __ } = wp.i18n

	const { MediaUpload, MediaUploadCheck, InspectorControls } = wp.editor

	const {
		Button,
		PanelBody,
		SelectControl,
		ServerSideRender,
		TextControl,
		ToggleControl,
	} = wp.components

	const PdfJsBlockName = 'pdfjs-viewer-shortcode/pdfjs';

	/**
	 * The edit function describes the structure of your block in the
	 * context of the editor. This represents what the editor will
	 * render when the block is used.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
	 *
	 * @param {Object} [props] Properties passed from the editor.
	 * @return {Element}       Element to render.
	 */
	const edit = ( { attributes, className, setAttributes, setState, error, validated } ) => {

		const {
			url,
			title,
			viewer_height,
			viewer_width,
			fullscreen,
			download,
			print,
			openfile,
			page,
			zoom,
			customZoom,
			nameddest,
			pagemode,
		} = attributes

		const ALLOWED_MEDIA_TYPES = ['application/pdf']

		const onSelectMedia = ( media ) => {
			setAttributes( {
				url: media.url,
			} )
		}

		const onChangeURL = ( newUrl ) => {
			setAttributes( { url: newUrl } )
		}

		const onChangeTitle = ( newTitle ) => {
			setAttributes( { title: newTitle } )
		}

		const onChangeHeight = ( newHeight ) => {
			setAttributes( { viewer_height: newHeight } )
		}

		const onChangeWidth = ( newWidth ) => {
			setAttributes( { viewer_width: newWidth } )
		}

		const onChangeFullscreen = () => {
			setAttributes( { fullscreen: ! fullscreen } )
		}

		const onChangeDownload = () => {
			setAttributes( { download: ! download } )
		}

		const onChangePrint = () => {
			setAttributes( { print: ! print } )
		}

		const onChangeOpenFile = () => {
			setAttributes( { openfile: ! openfile } )
		}

		const onChangePage = ( newPage ) => {
			setAttributes( { page: newPage } )
		}

		const onChangeZoom = ( newZoom ) => {
			setAttributes( { zoom: newZoom } )
			if ( 'custom' !== newZoom ) {
				setAttributes( { customZoom: '' } )
			}
		}

		const onChangeCustomZoom = ( newCustomZoom ) => {
			setAttributes( { customZoom: newCustomZoom } )
		}

		const onChangeNamedDestination = ( newNamedDest ) => {
			setAttributes( { nameddest: newNamedDest } )
		}

		const onChangePagemode = ( newPageMode ) => {
			setAttributes( { pagemode: newPageMode } )
		}

		return (
			<Fragment>

				<InspectorControls>
					<PanelBody title={ __( 'PDF.js Viewer Block Settings', 'pdfjs-viewer-shortcode' ) }>

						<TextControl
							label={ __( 'Title', 'pdfjs-viewer-shortcode' ) }
							placeholder={ __( 'Embedded PDF Document', 'pdfjs-viewer-shortcode' ) }
							value={ title }
							type="text"
							onChange={ onChangeTitle }
						/>

						<TextControl
							label={ __( 'Viewer Height', 'pdfjs-viewer-shortcode' ) }
							placeholder={ __( '1360px' ) }
							value={ viewer_height }
							type="text"
							onChange={ onChangeHeight }
						/>

						<TextControl
							label={ __( 'Viewer Width', 'pdfjs-viewer-shortcode' ) }
							placeholder={ __( '100%' ) }
							value={ viewer_width }
							type="text"
							onChange={ onChangeWidth }
						/>

						<ToggleControl
							label={ __( 'Display link to full screen viewer', 'pdfjs-viewer-shortcode' ) }
							checked={ fullscreen }
							onChange={ onChangeFullscreen }
						/>

						<ToggleControl
							label={ __( 'Display "Download" button', 'pdfjs-viewer-shortcode' ) }
							checked={ download }
							onChange={ onChangeDownload }
						/>

						<ToggleControl
							label={ __( 'Display "Print" button', 'pdfjs-viewer-shortcode' ) }
							checked={ print }
							onChange={ onChangePrint }
						/>

						<ToggleControl
							label={ __( 'Display "Open File" button', 'pdfjs-viewer-shortcode' ) }
							checked={ openfile }
							onChange={ onChangeOpenFile }
						/>

						<SelectControl
							multiple={ false }
							label={ __( 'Zoom Level', 'pdfjs-viewer-shortcode' ) }
							help={ __( 'Select one of the common zoom levels.', 'pdfjs-viewer-shortcode' ) }
							value={ zoom }
							options={
								[
									{
										value: 'auto',
										label: __( 'Automatic Zoom', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: 'page-actual',
										label: __( 'Actual Size', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: 'page-fit',
										label: __( 'Page Fit', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: 'page-width',
										label: __( 'Page Width', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: 'page-height',
										label: __( 'Page Height', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '50',
										label: __( '50%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '75',
										label: __( '75%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '100',
										label: __( '100%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '125',
										label: __( '125%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '150',
										label: __( '150%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '200',
										label: __( '200%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '300',
										label: __( '300%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: '400',
										label: __( '400%', 'pdfjs-viewer-shortcode' ),
									},
									{
										value: 'custom',
										label: __( 'Custom', 'pdfjs-viewer-shortcode' ),
									},
								]
							}
							onChange={ onChangeZoom }
						/>

						{ 'custom' === zoom && (
							<TextControl
								label={ __( 'Custom Zoom', 'pdfjs-viewer-shortcode' ) }
								help={ __( 'A PDF.js viewer accepted zoom format.', 'pdfjs-viewer-shortcode' ) }
								placeholder={ 100 }
								value={ customZoom }
								type="text"
								onChange={ onChangeCustomZoom }
							/>
						) }

						<SelectControl
							multiple={ false }
							label={ __( 'Sidebar Display', 'pdfjs-viewer-shortcode' ) }
							help={ __( 'Display no sidebar, or display the Thumbnails, Bookmarks, or Attachments sidebar.', 'pdfjs-viewer-shortcode' ) }
							value={ pagemode }
							options={
								[
									{
										label: __( 'None', 'pdfjs-viewer-shortcode' ),
										value: 'none',
									},
									{
										label: __( 'Thumbs', 'pdfjs-viewer-shortcode' ),
										value: 'thumbs',
									},
									{
										label: __( 'Bookmarks', 'pdfjs-viewer-shortcode' ),
										value: 'bookmarks',
									},
									{
										label: __( 'Attachments', 'pdfjs-viewer-shortcode' ),
										value: 'attachments',
									},
								]
							}
							onChange={ onChangePagemode }
						/>

						<TextControl
							label={ __( 'Page Number', 'pdfjs-viewer-shortcode' ) }
							help={ __( 'Go to a page number in the PDF.', 'pdfjs-viewer-shortcode' ) }
							placeholder={ 1 }
							value={ page }
							type="text"
							onChange={ onChangePage }
						/>

						<TextControl
							label={ __( 'Named Destination', 'pdfjs-viewer-shortcode' ) }
							help={ __( 'Go to a named destination in the PDF.', 'pdfjs-viewer-shortcode' ) }
							placeholder={ '' }
							value={ nameddest }
							type="text"
							onChange={ onChangeNamedDestination }
						/>

					</PanelBody>
				</InspectorControls>

				<div className={ className }>

					<MediaUpload
						onSelect={ onSelectMedia }
						allowedTypes={ ALLOWED_MEDIA_TYPES }
						render={ ( { open } ) => (
							<Button className={ 'button button-large' }
									onClick={ open }>
								{ __( 'Choose PDF', 'pdfjs-viewer-shortcode' ) }
							</Button>
						) }
					/>

					<TextControl
						label={ __( 'PDF URL', 'pdfjs-viewer-shortcode' ) }
						placeholder={ __( 'Type the URL of the PDF or use the Choose PDF button.', 'pdfjs-viewer-shortcode' ) }
						value={ url }
						type="url"
						onChange={ onChangeURL }
					/>

					{
						/*
						 * ServerSideRender allows us to use the existing
						 * PHP code to render the block.
						 * The PHP callback will recieve
						 * the attributes we have here.
						 * Also see the corresponding
						 * attributes in registerBlockType below,
						 * and the register_block_type() PHP code
						 * in pdfjs_block_init()
						 */
					}
					<ServerSideRender
						block={ PdfJsBlockName }
						attributes={ {
							url: url,
							title: title,
							viewer_height: viewer_height,
							viewer_width: viewer_width,
							fullscreen: fullscreen,
							download: download,
							print: print,
							openfile: openfile,
							page: page,
							zoom: zoom,
							customZoom: customZoom,
							nameddest: nameddest,
							pagemode: pagemode,
						} }
					/>

				</div>
			</Fragment>
		)
	}

	/**
	 * The save function defines the way in which the different
	 * attributes should be combined into the final markup, which is
	 * then serialized by Gutenberg into `post_content`.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
	 *
	 * @return {Element} Element to render.
	 */
	const save = ( props ) => {
		return null
	}

	/**
	 * Every block starts by registering a new block type definition.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	registerBlockType(
		PdfJsBlockName,
		{
			/**
			 * This is the display title for your block,
			 * which can be translated with `i18n` functions.
			 * The block inserter will show this name.
			 */
			title: __( 'PDF.js block', 'pdfjs-viewer-shortcode' ),

			/**
			 * An icon property should be specified to make it easier to
			 * identify a block. These can be any of WordPress’ Dashicons, or a
			 * custom svg element.
			 */
			icon: 'media-document',

			/**
			 * Blocks are grouped into categories to help users browse and
			 * discover them. The categories provided by core are `common`,
			 * `embed`, `formatting`, `layout` and `widgets`.
			 */
			category: 'widgets',

			/**
			 * Optional block extended support features.
			 */
			supports: {
				html: true,
			},

			attributes: {
				url: {
					type: 'string',
				},
				title: {
					type: 'string',
					default: __( 'Embedded PDF Document', 'pdfjs-viewer-shortcode' ),
				},
				viewer_height: {
					type: 'string',
					default: '1360px',
				},
				viewer_width: {
					type: 'string',
					default: '100%',
				},
				fullscreen: {
					type: 'boolean',
					default: true,
				},
				download: {
					type: 'boolean',
					default: true,
				},
				print: {
					type: 'boolean',
					default: true,
				},
				openfile: {
					type: 'boolean',
					default: false,
				},
				page: {
					type: 'number',
					default: 1,
				},
				zoom: {
					type: 'string',
					default: 'auto',
				},
				customZoom: {
					type: 'string',
					default: '',
				},
				nameddest: {
					type: 'string',
					default: '',
				},
				pagemode: {
					type: 'string',
					default: 'none',
				},
			},
			edit: edit,
			save: save,
		},
	)
} )(
	window.wp,
)
