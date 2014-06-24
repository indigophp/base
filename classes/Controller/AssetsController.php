<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Controller;

/**
 * Assets Controller
 *
 * This controller assures.
 *
 * @author Tamás Barta <barta.tamas.d@gmail.com>
 */
class AssetsController extends \Controller
{
	/**
	 * Theme instance
	 *
	 * @var Theme
	 */
	protected $theme;

	/**
	 * {@inheritdocs}
	 */
	public function before($data = null)
	{
		$this->theme = \Theme::instance('indigo');
	}

	/**
	 * {@inheritdocs}
	 *
	 * Search for a theme
	 */
	public function router($theme, $segments)
	{
		$search_paths = array(
			$this->theme->find($theme).'assets',
		);

		$file = implode('/', $segments) . '.' . \Input::extension();

		if(false !== strpos($file, '..'))
		{
			throw new \HttpForbiddenException();
		}

		foreach (\Package::loaded() as $package => $path)
		{
			$search_paths[] = $this->asset_path($theme, $path);
		}

		foreach (\Module::loaded() as $module => $path)
		{
			$search_paths[] = $this->asset_path($theme, $path);
		}

		foreach ($search_paths as $path)
		{
			if (file_exists($file_path = $path.DS.$file))
			{
				return new \Response(\File::read($file_path, true), 200, array('Content-type' => $this->mime_content_type($file_path)));
			}
		}

		throw new \HttpNotFoundException();
	}

	/**
	 * Returns an asset path
	 *
	 * @param string $theme
	 * @param string $path
	 *
	 * @return string
	 */
	protected function asset_path($theme, $path)
	{
		return $path.'themes'.DS.$theme.DS.$this->theme->get_config('assets_folder');
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

		$ext = \Arr::get(\File::file_info($filename), 'extension');
		if (array_key_exists($ext, $mime_types))
		{
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else
		{
			return 'application/octet-stream';
		}
	}
}
