<?php

namespace LayoutBuilder\WP;

if (!is_admin())
{
	return;
}

require_once __DIR__ . '/../layout-builder/app/config.php';
require_once LB_LIB . 'Builder.php';
require_once LB_LIB . 'RouteHandler.php';

require_once __DIR__ . '/data_storage.php';
require_once __DIR__ . '/file_uploader.php';

class Admin
{
	protected $_config;
	protected $_elementProvider;

	public function __construct()
	{
		add_action('admin_menu', array($this, 'menu'));

		// http://wordpress.stackexchange.com/a/115164/77293
		add_action('load-post.php', array($this, 'post'));
	}

	protected function _redirect($path)
	{
		wp_redirect(admin_url($path));
		exit;
	}

	protected function _init()
	{
		$this->_config = require __DIR__ . '/config.php';
		$this->_elementProvider = require __DIR__ . '/element_provider.php';
	}

	protected function _builder()
	{
		$this->_init();

		$id = null;
		$meta = null;
		$metaFields = array(
			array(
				'label' => 'Title',
				'code' => 'post_title',
				'type' => 'text'
			),
			array(
				'label' => 'Slug',
				'code' => 'post_name',
				'type' => 'text'
			)
		);
		$data = null;

		if (isset($_GET['post']))
		{
			$id = filter_var($_GET['post'], FILTER_VALIDATE_INT, array(
				'options' => array(
					'default' => null,
					'min_range' => 0
				)
			));

			if ($id !== null)
			{
				$post = get_post($id);

				if ($post)
				{
					$meta = array(
						'post_title' => $post->post_title,
						'post_name' => $post->post_name
					);
					$data = json_decode(get_post_meta($id, 'layout_json', true));
				}
			}
		}		

		$builder = new \LayoutBuilder\Builder(
			$this->_config,
			$this->_elementProvider
		);

		$builder->render($id, $meta, $metaFields, $data);
	}

	protected function _routeHandler()
	{
		$this->_init();

		$dataStorage = new DataStorage();
		$fileUploader = new FileUploader();

		$routeHandler = new \LayoutBuilder\RouteHandler(
			$this->_elementProvider,
			$dataStorage,
			$fileUploader
		);

		$routeHandler->dispatch();
	}

	/**
	 * Register the menu item and our corresponding page logic.
	 */
	public function menu()
	{
		// https://codex.wordpress.org/Function_Reference/add_submenu_page
		$hookName = add_submenu_page(
			// $parent_slug
			null, // we don't want a menu item for this page

			// $page_title
			__('Layout Builder'),

			// $menu_title
			__('Layout Builder'),

			// $capability
			// http://codex.wordpress.org/Roles_and_Capabilities
			'edit_posts',

			// $menu_slug
			'layout_builder',

			// $function
			//array($this, 'page')
			// we will manually render earlier than WordPress's page() call
			function() {}
		);

		// render as soon as the admin page loads
		add_action('load-' . $hookName, array($this, 'page'));
	}

	/**
	 * Replace WordPress's post.php with our own page for editing layouts.
	 */
	public function post()
	{
		$screen = get_current_screen();

		$action = $screen->action;
		if (!$action && isset($_GET['action']))
		{
			$action = $_GET['action'];
		}

		if (!$screen ||
			$screen->post_type !== 'layout' ||
			!in_array($action, array('edit')))
		{
			return;
		}

		$this->_builder();

		// prevent further WordPress processing
		exit;
	}

	/**
	 * Handle WordPress admin requests. Uses a nonce to determine which action to apply.
	 */
	public function page()
	{
		if (empty($_GET['nonce']))
		{
			return;
		}

		$nonce = $_GET['nonce'];

		if (wp_verify_nonce($nonce, 'routeHandler'))
		{
			$this->_routeHandler();
		}

		// prevent further WordPress processing
		exit;
	}
}

$admin = new Admin();
