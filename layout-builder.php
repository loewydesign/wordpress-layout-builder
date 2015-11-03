<?php
/*
Plugin Name: WordPress Layout Builder
Description: Integration of Layout Builder into WordPress in the form of a plugin. Layout Builder simplifies the placement of elements into sophisticated grids.
Plugin URI: https://github.com/loewydesign/wordpress-layout-builder
Author: Loewy
Author URI: http://loewy.com
*/

// do not run if called directly
if (!defined('ABSPATH'))
{
	return;
}

// do not run if WordPress is installing/upgrading
if (defined('WP_INSTALLING') && WP_INSTALLING)
{
    return;
}

// make LayoutBuilder\Element class available to themes before WordPress init hook
require_once __DIR__ . '/layout-builder/lib/Element.php';

require_once __DIR__ . '/inc/post_type.php';
require_once __DIR__ . '/inc/shortcode.php';
require_once __DIR__ . '/inc/admin.php';
