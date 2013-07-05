<?php

namespace Fuel\Tasks;

class Base
{
	public static function run()
	{
		
	}

	public static function install()
	{
		\Config::load('../../../composer.json', 'composer', true, true);
		$repositories = \Config::get('composer.repositories', array());
		$repositories[] = array(
			'type' => 'vcs',
			'url'  => 'git@gitlab.firstcomputer.hu:fuel/pdf.git'
		);
		\Config::set('composer.repositories', $repositories);
		\Config::save('../../../composer.json', 'composer');
	}

	public static function bootstrap($url = "http://twitter.github.io/bootstrap/assets/bootstrap.zip")
	{

		\Cli::write('Upgrading bootstrap to the latest version', 'green');
		\Cli::write('Downloading package: ' . $url);

		// Make the folder so we can extract the ZIP to it
		mkdir($tmp_folder = APPPATH . 'tmp' . DS . 'bootstrap-' . time());

		$zip_file = $tmp_folder . '.zip';
		@copy($url, $zip_file);

		if (file_exists($zip_file))
		{
			$unzip = new \Unzip;
			$files = $unzip->extract($zip_file, $tmp_folder);

			// Grab the first folder out of it (we dont know what it's called)
			foreach($bootstrapfolders = new \GlobIterator($tmp_folder.DS.'*') as $bootstrapfolder)
			{
				if ($bootstrapfolder->isDir())
				{
					$tmp_path = $tmp_folder.DS.$bootstrapfolder->getFilename().DS;
					break;
				}
			}

			if (empty($tmp_path))
			{
				throw new \FuelException('The zip file doesn\'t contain any install directory.');
			}

			$path = DOCROOT . 'public' . DS . \Config::get('asset.paths.0', 'assets/');

			// Move that folder into the packages folder
			// rename($tmp_path, $path);
			exec("cp -r $tmp_path $path && rm -rf $tmp_path");

			unlink($zip_file);
			rmdir($tmp_folder);

			foreach ($files as $file)
			{
				$file = str_replace($tmp_path, $path, $file);
				chmod($file, octdec(755));
				\Cli::write("\t" . $file);
			}
		}
		else
		{
			\Cli::write('Bootstrap could not be found', 'red');
		}
	}
}
