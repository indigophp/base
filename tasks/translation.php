<?php

namespace Fuel\Tasks;

class Translation
{
	public function run($lang = 'hu_HU.UTF-8')
	{
		$current_theme = 'default';
		$tplDirs = array_map(function($item) use($current_theme) { return $item.'themes'.DS.$current_theme; }, array_values(\Package::loaded()) + array_values(\Module::loaded()));

		\Config::load('parser', 'parser');

		$tmpDir = substr(\Config::get('parser.View_Twig.environment.cache'), 0, -1);
		$outDir = APPPATH.'lang'.DS.$lang.DS.'LC_MESSAGES'.DS;

		foreach ($tplDirs as $tplDir) {
			try {
				if (!is_dir($tplDir)) {
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
			} catch (Exception $e) {
				\Cli::error('Exception: ' . $e->getMessage());
			}
		}
		copy($outDir.'indigoadmin.po', $outDir.'indigoadmin.pot');
		passthru("find \"".PKGPATH."/base\" \"".$tmpDir."\" -iname \"*.php\" | xargs xgettext --default-domain=indigoadmin -p \"".$outDir."\" --from-code=UTF-8 -n --omit-header -L PHP");
		passthru("msgmerge -U \"".$outDir."/indigoadmin.pot\" \"".$outDir."/indigoadmin.po\"");
		rename($outDir.'indigoadmin.pot', $outDir.'indigoadmin.po');
		shell_exec('poedit "' . $outDir.'indigoadmin.po" &');

	}

	private function _iterate_dir($tplDir, $twig)
	{
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tplDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file)
		{
			// force compilation
			if ($file->isFile() && $file->getExtension() == 'twig') {
				$twig->loadTemplate(str_replace($tplDir.'/', '', $file));
			} else if ($file->isDir() && $file->getFilename() != '.' && $file->getFilename() != '..') {
				echo $file->getPathName();
				$this->_iterate_dir($file->getPathName(), $twig);
			}
		}
	}

}