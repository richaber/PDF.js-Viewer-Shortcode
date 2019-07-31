=== PDF.js Viewer Shortcode ===
Contributors: falconerweb, twistermc, richaber
Tags: pdf, pdf.js, viewer, reader, embed, mozilla, shortcode
Requires at least: 4.9
Tested up to: 5.2
Stable tag: 1.4
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Embed a beautiful PDF viewer into pages with a simple shortcode.

== Description ==

Incorporate Mozilla's PDF.js viewer into your pages and posts with a simple shortcode. PDF.js is a javascript library for displaying pdf pages within browsers.

Features:

* Gutenberg block for even easier PDF.js embedding
* Elegant speckled gray theme
* Customizable buttons
* Page navigation drawer
* Advanced search functionality
* Language support for all languages
* Protected PDF password entry
* Loading bar & displays partially loaded PDF (great for huge PDFs!)
* Document outline
* Advanced zoom settings
* Easy to use editor media button that generates the shortcode for you
* Support for mobile devices

Shortcode Syntax:

[pdfjs-viewer url=http://www.website.com/test.pdf title="My Embedded PDF Document" viewer_width=600px viewer_height=700px fullscreen=true download=true print=true]

*   url (required): direct url to pdf file
*   title (optional): Title attribute to use on the embedded iframe for accessibility (default: 'Embedded PDF Document')
*   viewer_width (optional): width of the viewer (default: 100%)
*   viewer_height (optional): height of the viewer (default: 1360px)
*   fullscreen (optional): true/false, displays fullscreen link above viewer (default: true)
*   download (optional): true/false, enables or disables download button (default: true)
*   print (optional): true/false, enables or disables print button (default: true)

== Installation ==

=== From within WordPress ===

1. Go to "Plugins" -> "Add New" in the WordPress Dashboard
1. Search for "PDF.js Viewer Shortcode" in the Plugins Add New Screen
1. Click the "Install Now" button
1. Click the "Activate" button

=== Manually ===

1. Download the plugin as a .zip archive from the WordPress Plugin Repository
1. Upload and expand the archive into your site's wp-content/plugins directory
1. Go to "Plugins" in the WordPress Dashboard
1. Click the "Activate" link under "PDFjs Viewer"

== Screenshots ==
1. Viewer example with default size.
2. Location of media button (Pre-Gutenberg / Classic Editor)

== Changelog ==

= 1.4 =

Release Date: CHANGE THIS BEFORE RELEASE

Enhancements:

* Introduce a Gutenberg block for even easier PDF.js embedding
* Upgrade Mozilla's PDF.js to stable version 2.1.266 [https://github.com/mozilla/pdf.js/releases/tag/v2.1.266](https://github.com/mozilla/pdf.js/releases/tag/v2.1.266)
* Update license to GPL v3 for compatibility with both WordPress (GPL v2) and Mozilla PDF.js (Apache v2)
* Internationalized strings
* Add support for iframe title attribute for improved accessibility
* Added support for PDF.js viewer's `page`, `zoom`, `nameddest`, and `pagemode` [fragment identifier options](https://github.com/mozilla/pdf.js/wiki/Viewer-options#options-after-the-)
