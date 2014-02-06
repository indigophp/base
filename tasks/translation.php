<?php

namespace Fuel\Tasks;

class Translation
{
	/**
	 * @var	array	list of modules to translate
	 */
	protected static $modules = array();

	/**
	 * @var	array	list of packages to translate
	 */
	protected static $packages = array();

	/**
	 * @var	boolean	check theme paths as well
	 */
	protected static $default = true;

	/**
	 * @var	string	which theme to translate
	 */
	protected static $theme = 'default';

	public function __construct()
	{
		// get Cli options
		$modules = \Cli::option('modules', \Cli::option('m'));
		$packages = \Cli::option('packages', \Cli::option('p'));
		$default = \Cli::option('default');
		static::$theme = \Cli::option('theme', \Cli::option('t', 'default'));
		$all = \Cli::option('all');

		if ($all)
		{
			$modules = true;
			$packages = true;
			$default = true;
		}

		// if modules option set
		if ( ! empty($modules))
		{
			// if true - get all modules
			if ($modules === true)
			{
				// loop through module paths
				foreach (\Config::get('module_paths') as $path)
				{
					// get all modules
					foreach(new \GlobIterator(realpath($path).DS.'*') as $m)
					{
						if (count(new \GlobIterator($m->getPathname().DS.'themes')))
						{
							static::$modules[] = $m->getBasename();
						}
					}
				}
			}
			// else do selected modules
			else
			{
				static::$modules = explode(',', $modules);
			}
		}

		// if packages option set
		if ( ! empty($packages))
		{
			// if true - get all packages
			if ($packages === true)
			{
				// loop through package paths
				foreach (\Config::get('package_paths', array(PKGPATH)) as $path)
				{
					// get all packages
					foreach(new \GlobIterator(realpath($path).DS.'*') as $p)
					{
						if (count(new \GlobIterator($p->getPathname().DS.'themes')))
						{
							static::$packages[] = $p->getBasename();
						}
					}
				}
			}
			// else do selected packages
			else
			{
				static::$packages = explode(',', $packages);
			}
		}

		// if packages or modules are specified, and the app isn't, disable app migrations
		if ( ( ! empty($packages) or ! empty($modules)) and empty($default))
		{
			static::$default = false;
		}
	}

	public function run($lang = 'hu_HU.UTF-8')
	{
		$tplDirs = $this->get_dirs();
// var_dump($tplDirs); exit;
		\Config::load('parser', 'parser');

		$tmpDir = substr(\Config::get('parser.View_Twig.environment.cache'), 0, -1);
		$outDir = \Cli::option('output', \Cli::option('o', APPPATH.'lang'.DS.$lang.DS.'LC_MESSAGES'.DS));
		$outDir = str_replace('APPPATH', APPPATH, $outDir);
		$outDir = rtrim($outDir, DS).DS;

		foreach ($tplDirs as $tplDir)
		{
			$tplDir .= static::$theme;

			try
			{
				if (!is_dir($tplDir))
				{
					continue;
				}

				$loader = new \Twig_Loader_Filesystem($tplDir);

				$twig = new \Twig_Environment($loader, array(
					'cache' => $tmpDir,
					'auto_reload' => true
				));

				$twig->addExtension(new \Twig_Extensions_Extension_I18n());
				$twig->addExtension(new \Twig_Fuel_Extension());
				$twig->addExtension(new \Twig_Indigo_Extension());

				$this->_iterate_dir($tplDir, $twig);
			}
			catch (Exception $e)
			{
				\Cli::error('Exception: ' . $e->getMessage());
			}
		}

		is_file($outDir.'indigoadmin.po') and copy($outDir.'indigoadmin.po', $outDir.'indigoadmin.pot');
		passthru("find \"".APPPATH."../\" \"".$tmpDir."\" -iname \"*.php\" | xargs xgettext --default-domain=indigoadmin -p \"".$outDir."\" --from-code=UTF-8 -n --omit-header -L PHP");
		passthru("msgmerge -U \"".$outDir."indigoadmin.pot\" \"".$outDir."indigoadmin.po\"");
		is_file($outDir.'indigoadmin.pot') and rename($outDir.'indigoadmin.pot', $outDir.'indigoadmin.po');
		shell_exec('poedit "' . $outDir.'indigoadmin.po" &');
	}

	private function _iterate_dir($tplDir, $twig)
	{
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tplDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file)
		{
			// force compilation
			if ($file->isFile() && $file->getExtension() == 'twig')
			{
				try
				{
					$twig->loadTemplate(str_replace($tplDir.'/', '', $file));
				}
				catch (\Twig_Error_Loader $e)
				{
					// Parent templates are not loaded
				}
			}
			elseif ($file->isDir() && $file->getFilename() != '.' && $file->getFilename() != '..')
			{
				echo $file->getPathName();
				$this->_iterate_dir($file->getPathName(), $twig);
			}
		}
	}

	protected function get_dirs()
	{
		$dirs = array();

		foreach (static::$modules as $module)
		{
			if ($module = \Module::exists($module))
			{
				$dirs[] = realpath($module.DS.'themes').DS;
			}
		}

		foreach (static::$packages as $package)
		{
			if ($package = \Package::exists($package))
			{
				$dirs[] = realpath($package.DS.'themes').DS;
			}
		}

		if (static::$default)
		{
			foreach (\Config::get('theme.paths', array()) as $path) {
				$dirs[] = realpath($path).DS;
			}
		}

		return array_unique(array_filter($dirs));
	}

}