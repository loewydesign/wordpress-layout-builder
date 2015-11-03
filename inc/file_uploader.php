<?php

namespace LayoutBuilder\WP;

require_once __DIR__ . '/../layout-builder/app/config.php';
require_once LB_LIB . 'FileUploader.php';
require_once LB_LIB . 'Exception.php';

class FileUploader extends \LayoutBuilder\FileUploader
{
	protected function _getExistingAttachmentByFilename($fileName)
	{
		$query = new \WP_Query(array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_wp_attached_file',
					'value' => $fileName
				)
			)
		));
		
		if (isset($query->posts[0]))
		{
			return $query->posts[0];
		}

		return null;
	}

	protected function _getTitleFromFilename($fileName)
	{
		$fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

		// replace anything other than letters and dashes with dashes
		$title = preg_replace('#[^a-z\-]+#', '-', strtolower($fileNameWithoutExt));

		// replace multiple dashes with a single dash
		$title = preg_replace('#-+#', '-', $title);

		return $title;
	}

	protected function _uploadFile($fileName, $data)
	{
		$uploadDir = wp_upload_dir();

		$uploadFileName = $uploadDir['path'] . '/' . $fileName;

		// check if file already exists and if it is exactly the same as the new data
		if (file_exists($uploadFileName) && md5_file($uploadFileName) === md5($data))
		{
			// if so, return the existing URL, don't try to upload again
			return $uploadDir['url'] . '/' . $fileName;
		}

		// upload the file into the wp-content/uploads directory
		// note that this will rename the file if another file with the same name already exists
		$uploadedFile = wp_upload_bits($fileName, null, $data);

		if (!empty($uploadedFile['error']))
		{
			throw new \LayoutBuilder\Exception('Could not upload file ' . $fileName . ': ' . $uploadedFile['error']);
		}

		// grab the new file name, in case it was renamed due to a duplicate
		$uploadedFileName = basename($uploadedFile['file']);

		// grab the mime type of the file
		$fileType = wp_check_filetype($uploadedFile['file']);

		// create the attachment post
		// note that this is necessary in order for the item to show up in WordPress's media gallery
		$attachmentId = wp_insert_attachment(array(
			// use uploaded file name to generate guid
			'guid' => $uploadDir['url'] . '/' . $uploadedFileName,

			// use original file name to generate title, as per default WP media upload functionality
			'post_title' => $this->_getTitleFromFilename($fileName),

			'post_content' => '',
			'post_status' => 'inherit',
			'post_mime_type' => $fileType['type']
		), $uploadedFile['file']);

		if (!function_exists('wp_generate_attachment_metadata') ||
			!function_exists('wp_update_attachment_metadata'))
		{
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// generate the attachment meta data
		$attachmentMetaData = wp_generate_attachment_metadata($attachmentId, $uploadedFile['file']);

		// update the attachment's meta data
		wp_update_attachment_metadata($attachmentId, $attachmentMetaData);

		return $uploadedFile['url'];
	}

	public function upload($info)
	{
		$fileName = $info['fileName'];

		$data = @file_get_contents($info['tmpFileName']);

		if (!$data)
		{
			throw new \LayoutBuilder\Exception('Could not read uploaded file!');
		}

		return $this->_uploadFile($fileName, $data);
	}
}
