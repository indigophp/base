<?php

namespace Fuel\Tasks;

class Theme
{

	public static function run()
	{
		\Cli::write(\Cli::color('Fuel Asset Installer', 'blue') . "\n");
		\Cli::write("Usage:\noil r asset::(un)install [theme[,theme,...]]");
		\Cli::write("Advanced:\noil r asset::(un)install [theme[,theme,...]] -m, --manual");
	}

	public function install($themes = null)
	{
		if (\Cli::option('manual', \Cli::option('m'))) {
			$this->install_manual($themes);
		}
		else
		{
			$this->install_auto($themes);
		}
	}

	public function uninstall($themes = null)
	{
		if (\Cli::option('manual', \Cli::option('m'))) {
			$this->uninstall_manual($themes);
		}
		else
		{
			$this->uninstall_auto($themes);
		}
	}

	public function reinstall($themes = null)
	{
		if (\Cli::option('manual', \Cli::option('m'))) {
			$this->install_manual($themes);
			$this->uninstall_manual($themes);
		}
		else
		{
			$this->install_auto($themes);
			$this->uninstall_auto($themes);
		}
	}

	private function install_manual($themes = null)
	{

		if (is_string($themes))
		{
			$themes = explode(',', $themes);
		}
		else
		{
			$themes = \Theme::instance()->all();
		}

		$assets_folder = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		foreach ($themes as $theme)
		{
			$asset_path = $assets_folder . $theme;
			$path = \Theme::instance()->find($theme);
			if ($path && ! is_readable($asset_path))
			{
				try {
					\File::copy_dir($path . 'assets', $asset_path);
					\Cli::write(\Cli::color($theme . ' installed successfully!', 'green'));
				}
				catch (\OutsideAreaException $e)
				{
					\Cli::error(\Cli::color('Asset path not found', 'red'));
				}
				catch (\InvalidPathException $e)
				{
					\Cli::error(\Cli::color('Asset path not found', 'red'));
				}
				catch (\FileAccessException $e)
				{
					\Cli::error(\Cli::color('Asset path already exists', 'red'));
				}
			}
			elseif($path && is_readable($asset_path))
			{
				\Cli::error(\Cli::color($theme . ' theme is already installed', 'red'));
			}
			else
			{
				\Cli::error(\Cli::color($theme . ' theme cannot be installed', 'red'));
			}
		}
	}

	private function uninstall_manual($themes = null)
	{

		if (is_string($themes))
		{
			$themes = explode(',', $themes);
		}
		else
		{
			$themes = \Theme::instance()->all();
		}

		$assets_folder = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		foreach ($themes as $theme)
		{
			$asset_path = $assets_folder . $theme;
			if (is_dir($asset_path))
			{
				try {
					\File::delete_dir($asset_path);
					\Cli::write(\Cli::color($theme . ' uninstalled successfully!', 'green'));
				}
				catch (\OutsideAreaException $e)
				{
					\Cli::error(\Cli::color('Asset path not found', 'red'));
				}
				catch (\InvalidPathException $e)
				{
					\Cli::error(\Cli::color('Asset path not found', 'red'));
				}
				catch (\FileAccessException $e)
				{
					\Cli::error(\Cli::color('Something went wrong', 'red'));
				}
			}
			else
			{
				\Cli::error(\Cli::color($theme . ' theme cannot be uninstalled', 'red'));
			}
		}
	}

	private function install_auto($themes = null)
	{

		if (is_string($themes))
		{
			$themes = explode(',', $themes);
		}
		else
		{
			$themes = \Theme::instance()->all();
		}

		$assets_folder = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		foreach ($themes as $theme)
		{
			$asset_path = $assets_folder . $theme;
			$path = \Theme::instance()->find($theme);
			if ($path && ! is_readable($asset_path))
			{
				$return = symlink($path . 'assets', $asset_path);

				if ($return)
				{
					\Cli::write(\Cli::color($theme . ' theme is successfully installed', 'green'));
				}
				else
				{
					\Cli::error(\Cli::color($theme . ' theme cannot be installed', 'red'));
				}
			}
			elseif($path && is_readable($asset_path))
			{
				\Cli::error(\Cli::color($theme . ' theme is already installed', 'red'));
			}
			else
			{
				\Cli::error(\Cli::color($theme . ' theme cannot be installed', 'red'));
			}
		}
	}

	private function uninstall_auto($themes = null)
	{

		if (is_string($themes))
		{
			$themes = explode(',', $themes);
		}
		else
		{
			$themes = \Theme::instance()->all();
		}

		$assets_folder = DOCROOT . 'public' . DS . \Config::get('theme.assets_folder', 'themes') . DS;

		foreach ($themes as $theme)
		{
			$asset_path = $assets_folder . $theme;
			if (is_link($asset_path))
			{
				$return = unlink($asset_path);

				if ($return)
				{
					\Cli::write(\Cli::color($theme . ' theme is successfully uninstalled', 'green'));
				}
				else
				{
					\Cli::error(\Cli::color($theme . ' theme cannot be uninstalled', 'red'));
				}
			}
			elseif(is_dir($asset_path))
			{
				\Cli::error(\Cli::color($theme . ' theme is manually installed', 'red'));
			}
		}
	}
}