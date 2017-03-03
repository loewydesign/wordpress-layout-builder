# WordPress Layout Builder

**WIP! Feel free to experiment, but don't expect it to be at 100%! It is very likely that there are various bugs, glitches, and potentially even security holes.**

## Overview

Integration of Layout Builder into WordPress in the form of a plugin. Layout Builder simplifies the placement of elements into sophisticated grids.

## Installation

Download the repository and drop its contents into `wp-content/plugins/layout-builder`.

Via WP-CLI:

    wp plugin install https://github.com/loewydesign/wordpress-layout-builder/archive/develop.zip

## Usage

In the WordPress admin, go to Layouts > Add New. Name your layout, then save it. Once the layout is saved, you'll be in the Layout Builder interface.

From the Layout Builder interface, click Save to save your layout. Currently, there is no link back to the WordPress admin, so you'll need to do it via the URL.

To render a layout inside of a page (or anywhere else, really), use the `layout` shortcode:

By ID: `[layout id=123]`

By slug: `[layout slug=my-layout-name]`

## Creating Elements

Elements are created via the element provider class. In WordPress, this class is available via the `layout_builder_elements` hook.

	function register($elementProvider)
	{
		// example image element
		$elementProvider->register('image', function($values) {
			return '<img' .
				' src="' . (isset($values->src) ? $values->src : '') . '"' .
				' alt="' . (isset($values->alt) ? $values->alt : '') . '"' .
				' title="' . (isset($values->title) ? $values->title : '') . '"' .
				'/>';
		}, array(
			'label' => 'Image',
			'fields' => array(
				array(
					'label' => 'Source',
					'code' => 'src',
					'type' => 'image'
				),
				array(
					'label' => 'Alt',
					'code' => 'alt',
					'type' => 'text'
				),
				array(
					'label' => 'Title',
					'code' => 'title',
					'type' => 'text'
				)
			)
		));
	}

	add_action('layout_builder_elements', 'register');

## Registering Stylesheets and Scripts

WIP

## Integration Details

### Uploads

Uploads are handled as WordPress attachments. If an upload matches an existing file in `wp-content/uploads` both by filename and by MD5 hash, the existing file is used. Otherwise, WordPress's regular attachment renaming kicks in (e.g. foo.png, foo1.png, foo2.png, etc.).

### Output Filtering

Layout Builder output can apply the WordPress hook filter for 'the_content' by setting a constant in your `wp-config.php` file.

	// will perform apply_filters('the_content', $output) to the output of Layout Builder
	define('LB_CONTENT_FILTER_OUTPUT', true);

## License

WordPress Layout Builder is licensed under the GPLv2. See LICENSE file for the full GPLv2 license text.