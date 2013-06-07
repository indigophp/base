<?php

namespace Fuel\Tasks;

class Theme
{

	public static function run()
	{
		\Cli::write(\Cli::color('Fuel Asset Installer', 'blue') . "\n");
		\Cli::write("Usage:\noil r asset::(un)install [theme]");
	}

	public static function install($theme = null)
	{
		$theme = static::get_theme($theme);
		$path = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		if ( ! is_array($theme))
		{
			return \Cli::error(\Cli::color('Theme not found', 'red'));
		}

		try {
			\File::copy_dir($theme['path'] . 'assets', $path . $theme['name']);
		}
		catch (\OutsideAreaException $e)
		{
			return \Cli::error(\Cli::color('Asset path not found', 'red'));
		}
		catch (\InvalidPathException $e)
		{
			return \Cli::error(\Cli::color('Asset path not found', 'red'));
		}
		catch (\FileAccessException $e)
		{
			return \Cli::error(\Cli::color('Asset path already exists', 'red'));
		}

		static::save_theme($theme);
		return \Cli::write(\Cli::color('Install successful!', 'blue'));
	}

	public static function uninstall($theme = null)
	{
		$theme = static::get_theme($theme);
		$path = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		if ( ! is_array($theme))
		{
			return \Cli::error(\Cli::color('Theme not found', 'red'));
		}

		try {
			\File::delete_dir($path . $theme['name']);
		}
		catch (\OutsideAreaException $e)
		{
			return \Cli::error(\Cli::color('Asset path not found', 'red'));
		}
		catch (\InvalidPathException $e)
		{
			return \Cli::error(\Cli::color('Asset path not found', 'red'));
		}
		catch (\FileAccessException $e)
		{
			return \Cli::error(\Cli::color('Asset path not exists', 'red'));
		}

		static::save_theme($theme, false);
		return \Cli::write(\Cli::color('Uninstall successful!', 'blue'));
	}

	public static function reinstall($theme = null)
	{
		static::uninstall();
		return static::install();
	}

	private static function get_theme($theme = null)
	{
		$instance = \Theme::instance();
		$path = \Config::get('theme.asset_path', 'themes');

		if ($theme === null)
		{
			$theme = $instance->active();
		}
		elseif($instance->find($theme))
		{
			$theme = array(
				'name' => $theme,
				'path' => $instance->find($theme)
			);
		}
		else
		{
			return false;
		}

		return $theme;
	}

	private static function save_theme($theme, $method = true)
	{
		$instance = \Theme::instance();
		$info = $instance->load_info($theme['name']);

		\Arr::set($info, 'installed', (bool)$method);

		return \Config::save($theme['path'] . \Config::get('theme.info_file_name', 'themeinfo.php'), $info);
	}
}