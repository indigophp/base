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

		// Invalid path, STOP HACKING
		if(false !== strpos($file, '..'))
		{
			throw new \HttpForbiddenException();
		}

		// Adds loaded packages to search paths
		foreach (\Package::loaded() as $package => $path)
		{
			$search_paths[] = $this->asset_path($theme, $path);
		}

		// Adds loaded modules to search paths
		foreach (\Module::loaded() as $module => $path)
		{
			$search_paths[] = $this->asset_path($theme, $path);
		}

		// Looks for file and returns it
		foreach ($search_paths as $path)
		{
			if (file_exists($file_path = $path.DS.$file))
			{
				return new \Response(\File::read($file_path, true), 200, array('Content-type' => $this->mime_content_type($file_path)));
			}
		}

		// Nothing found
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

	/**
	 * Returns MIME type of file
	 *
	 * Checks extension in internal mime list
	 * Uses finfo
	 * Returns default mime type
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	protected function mime_content_type($filename)
	{
		\Config::load('mimes', true);

		$mime_types = \Config::get('mimes');

		$ext = \Arr::get(\File::file_info($filename), 'extension');

		if (array_key_exists($ext, $mime_types))
		{
			$mimetype = $mime_types[$ext];
		}
		elseif (function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
		}
		else
		{
			$mimetype = 'application/octet-stream';
		}

		return $mimetype;
	}
}
