<?php

namespace LayoutBuilder\WP;

require_once __DIR__ . '/../layout-builder/app/config.php';
require_once __DIR__ . '/util.php';

require_once LB_LIB . 'Output.php';

/**
 * Register the Layout shortcode.
 */
function registerShortcode()
{
	$elementProvider = require __DIR__ . '/element_provider.php';

	$output = new \LayoutBuilder\Output($elementProvider);

	add_shortcode('layout', function($attribs) use ($output) {
		$attribs = shortcode_atts(array(
			'id' => null,
			'slug' => null,
			'lang' => 'en'
		), $attribs);

		$id = $attribs['id'];

		if (!empty($attribs['slug']))
		{
			$id = getPostIdBySlug($attribs['slug'], 'layout');
			if (!$id)
			{
				return '<p>Invalid layout slug: ' . $attribs['slug'] . '</p>';
			}
		}

		$state = get_post_meta($id, 'layout_json', true);
		$state = json_decode($state);

		if (!is_object($state) || !isset($state->rows))
		{
			return '<p>Invalid layout with ID: ' . $id . '</p>';
		}
		
		return $output->render($state, $attribs['lang'], true);
	});
}

add_action('init', 'LayoutBuilder\WP\registerShortcode');
