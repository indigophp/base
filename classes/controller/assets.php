<?php

/**
 * This controller assures.
 */
class Controller_Assets extends Controller
{

	public function action_theme()
	{
		$this->theme = Theme::instance();
		// We need the URL to know what to serve
		$segments = Uri::segments();
		array_shift($segments);
		array_shift($segments);

		$url = implode('/', $segments) . '.' . Input::extension();

		if(false !== strpos($url, '..')) {
			throw new \HttpForbiddenException();
		}

		$theme_name = \Arr::get($this->theme->active(), 'name');
		$theme_path = realpath(\Arr::get($this->theme->active(), 'path')) . DS;

		$search_paths = array(
			$theme_path
		);

		foreach (Package::loaded() as $package => $path) {
			$search_paths[] = $path.'themes'.DS.$theme_name.DS.'assets';
		}
		foreach (Module::loaded() as $module => $path) {
			$search_paths[] = $path.'themes'.DS.$theme_name.DS.'assets';
		}

		foreach ($search_paths as $path) {
			if (file_exists($file_path = $path.DS.$url)) {
				return new Response(File::read($file_path, true), 200, array('Content-type' => $this->mime_content_type($file_path)));
			}
		}

		throw new \HttpNotFoundException();

	}

	protected function mime_content_type($filename) {

		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

            // images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

            // archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

            // adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

            // ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

            // open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = Arr::get(File::file_info($filename), 'extension');
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}

}