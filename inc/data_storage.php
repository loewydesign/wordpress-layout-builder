<?php

namespace LayoutBuilder\WP;

require_once __DIR__ . '/../layout-builder/app/config.php';

require_once LB_LIB . 'DataStorage.php';
require_once LB_LIB . 'Exception.php';

class DataStorage extends \LayoutBuilder\DataStorage
{
	public function store($data)
	{
		$id = isset($data->id) ? (int)$data->id : null;
		$meta = isset($data->meta) ? $data->meta : new \stdClass();
		$state = isset($data->state) ? $data->state : new \stdClass();

		$post = get_post($id);

		if ($post)
		{
			// update existing post
			$meta->ID = $id;

			// update post title, slug, etc.
			$error = wp_update_post($meta, true);

			if (is_wp_error($error))
			{
				throw new \LayoutBuilder\Exception('Could not update WordPress Layout post! Error: ' . $id->get_error_message());
			}
		}
		else
		{
			$meta->post_status = 'publish';
			$meta->post_type = 'layout';

			// create new post
			$id = wp_insert_post($meta, true);

			if (is_wp_error($id))
			{
				throw new \LayoutBuilder\Exception('Could not create WordPress Layout post! Error: ' . $id->get_error_message());
			}
		}

		// store the layout data as a JSON meta field
		update_post_meta($id, 'layout_json', json_encode($state));

		return $id;
	}
}
