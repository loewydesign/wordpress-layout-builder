<?php

namespace LayoutBuilder\WP;

require_once __DIR__ . '/../layout-builder/app/config.php';
require_once LB_LIB . 'ElementProvider.php';

$elementProvider = new \LayoutBuilder\ElementProvider();

do_action('layout_builder_elements', $elementProvider);

return $elementProvider;
