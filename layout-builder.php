<?php
/*
Plugin Name: Layout Builder
Description: Simplifies the placement of elements into sophisticated grids.
Plugin URI: https://github.com/loewydesign/layout-builder
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

require_once 'inc/post_type.php';
require_once 'inc/shortcode.php';
require_once 'inc/admin.php';
