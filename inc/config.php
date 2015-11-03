<?php

namespace LayoutBuilder\WP;

require_once __DIR__ . '/../layout-builder/app/config.php';
require_once LB_LIB . 'Config.php';

$config = new \LayoutBuilder\Config();

$adminUrl = 'admin.php?page=layout_builder';

// base URL of the generic Layout Builder codebase
$config->setBaseUrl(plugin_dir_url(__DIR__) . 'layout-builder/');

// route handler URL (uses our admin URL)
$config->setRouteHandlerUrl(get_admin_url(null, $adminUrl . '&nonce=' . wp_create_nonce('routeHandler')));

$styles = apply_filters('layout_builder_styles', array());
foreach ($styles as $style)
{
	$config->addStyle($style);
}

$scripts = apply_filters('layout_builder_scripts', array());
foreach ($scripts as $script)
{
	$config->addScript($script);
}

return $config;
