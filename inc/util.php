<?php

namespace LayoutBuilder\WP;

function getPostIdBySlug($slug, $postType = 'post')
{
	global $wpdb;

	$sql = <<<SQL
SELECT
	`ID`
FROM
	$wpdb->posts
WHERE
	`post_name` = %s
	AND `post_type` = %s
SQL;

	$query = $wpdb->prepare($sql, $slug, $postType);

	return $wpdb->get_var($query);
}
