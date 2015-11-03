<?php

namespace LayoutBuilder\WP;

function getLabels($singular, $plural = '')
{
	if (!$plural)
	{
		$plural = $singular . 's';
	}

	return array(
		'name' => __($plural),
		'singular_name' => __($singular),
		'add_new_item' => __('Add ' . $singular),
		'edit_item' => __('Edit ' . $singular),
		'new_item' => __('New ' . $singular),
		'view_item' => __('View ' . $singular),
		'search_items' => __('Search ' . $plural),
		'not_found' => __('No ' . strtolower($plural) . ' found'),
		'not_found_in_trash' => __('No ' . strtolower($plural) . ' found in Trash')
	);
}

/**
 * Register the Layout post type.
 */
function registerPostType()
{
	register_post_type('layout', array(
		'labels' => getLabels('Layout'),

		// do not allow searching, etc. on the front-end, but show the admin UI
		'public' => false,
		'show_ui' => true,

		'supports' => array(
			'title'
		),

		// do not rewrite URLs on the front-end
		'rewrite' => false
	));
}

add_action('init', 'LayoutBuilder\WP\registerPostType');
