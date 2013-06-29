<?php

namespace Fuel\Tasks;

class Generate
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r generate
	 *
	 * @return string
	 */
	public static function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Generate:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
	}

	protected static function create($filepath, $contents, $type = 'file')
	{
		\Cli::write("\tCreating {$type}: {$filepath}", 'green');

		if ( ! $handle = @fopen($filepath, 'w+'))
		{
			throw new \Exception('Cannot open file: '. $filepath);
		}

		$result = @fwrite($handle, $contents);

		// Write $somecontent to our opened file.
		if ($result === false)
		{
			throw new \Exception('Cannot write to file: '. $filepath);
		}

		@fclose($handle);

		@chmod($filepath, 0666);

		return $result;
	}



	/**
	 * Generate package
	 *
	 * Usage (from command line):
	 *
	 * php oil r generate:package name
	 *
	 * @return string
	 */
	public static function package($name = null, $drivers = array())
	{
		$name       = str_replace(array('/', '_', '-'), '', \Str::lower($name));
		$class_name = ucfirst($name);
		$composer   = \Cli::option('composer', \Cli::option('c', true));
		$path       = \Cli::option('path', \Cli::option('p', PKGPATH));

		! empty($drivers) && $drivers = explode(',', $drivers);


		if (empty($name))
		{
			throw new \Exception('No package name has been provided.');
		}

		if ( ! in_array($path, \Config::get('package_paths')) && ! in_array(realpath($path), \Config::get('package_paths')) )
		{
			throw new \Exception('Given path is not a valid package path.');
		}

		! \Str::ends_with($path, DS) && $path .= DS;
		$path .= $name . DS;

		if (is_dir($path))
		{
			throw new \Exception('Package already exists.');
		}

		mkdir($path, 0755, TRUE);
		mkdir($path . 'classes', 0755, TRUE);
		mkdir($path . 'classes' . DS . $name, 0755, TRUE);
		mkdir($path . 'config', 0755, TRUE);

		if ($composer)
		{
			$output = <<<COMPOSER
{
	"name": "fuel/{$name}",
	"type": "fuel-package",
	"description": "{$class_name} package",
	"keywords": [""],
	"homepage": "http://fuelphp.com",
	"license": "MIT",
	"authors": [
		{
			"name": "AUTHOR",
			"email": "AUTHOR@example.com"
		}
	],
	"require": {
		"composer/installers": "~1.0"
	},
	"extra": {
		"installer-name": "{$name}"
	}
}
COMPOSER;

			static::create($path . 'composer.json', $output);
		}

		$output = <<<README
# {$class_name} package
Here comes some description
README;

		static::create($path . 'README.md', $output);

		$output = <<<CLASS
<?php

namespace {$class_name};

class {$class_name}Exception extends \FuelException {}

class {$class_name}
{

	/**
	 * loaded instance
	 */
	protected static \$_instance = null;

	/**
	 * array of loaded instances
	 */
	protected static \$_instances = array();

	/**
	 * Default config
	 * @var array
	 */
	protected static \$_defaults = array();

	/**
	 * {$class_name} driver forge.
	 *
	 * @param	array			\$config		Extra config array
	 * @return  {$class_name} instance
	 */
	public static function forge(array \$config = array())
	{
		\$config = \Arr::merge(static::\$_defaults, \Config::load('{$name}', array()), \$config);

		\$class = '\\{$class_name}\\{$class_name}_' . ucfirst(\$config['driver']);

		if( ! class_exists(\$class, true))
		{
			throw new \FuelException('Could not find {$class_name} driver: ' . \$config['driver']);
		}

		$driver = $class($queue, $config);

		static::$_instances[$queue] = $driver;

		return $driver;
	}

	/**
	 * Return a specific driver, or the default instance (is created if necessary)
	 *
	 * @param   string  queue
	 * @return  {$class_name}_Driver
	 */
	public static function instance(\$instance = null)
	{
		if (\$instance !== null)
		{
			if ( ! array_key_exists(\$instance, static::\$_instances))
			{
				return false;
			}

			return static::\$_instances[\$instance];
		}

		if (static::\$_instance === null)
		{
			static::\$_instance = static::forge();
		}

		return static::\$_instance;
	}


}
CLASS;

		static::create($path . 'classes' . DS . $name . '.php', $output);


		$output = <<<DRIVER
<?php

namespace {$class_name};

class {$class_name}_Driver
{
	/**
	* Driver config
	* @var array
	*/
	protected \$config = array();

	/**
	* Driver constructor
	*
	* @param array \$config driver config
	*/
	public function __construct(array \$config = array())
	{
		\$this->config = \$config;
	}

	/**
	* Get a driver config setting.
	*
	* @param string \$key the config key
	* @param mixed  \$default the default value
	* @return mixed the config setting value
	*/
	public function get_config(\$key, \$default = null)
	{
		return \Arr::get(\$this->config, \$key, \$default);
	}

	/**
	* Set a driver config setting.
	*
	* @param string \$key the config key
	* @param mixed \$value the new config value
	* @return object \$this for chaining
	*/
	public function set_config(\$key, \$value)
	{
		\Arr::set(\$this->config, \$key, \$value);

		return \$this;
	}
}
DRIVER;

		static::create($path . 'classes' . DS . $name . DS . 'driver.php', $output);

		$output = <<<CONFIG
<?php

return array(

);
CONFIG;

		static::create($path . 'config' . DS . $name . '.php', $output);


		$bootstrap = "";
		foreach ($drivers as $driver)
		{
			$driver = \Str::lower($driver);
			$driver_name = ucfirst($driver);
			$output = <<<CLASS
<?php

namespace {$class_name};

class {$class_name}_{$driver_name}
{
	/**
	* Driver specific functions
	*/
}
CLASS;
			$bootstrap .= "\n\t'{$class_name}\\\\{$class_name}_{$driver_name}' => __DIR__ . '/classes/{$name}/{$driver}.php',";
			static::create($path . 'classes' . DS . $name . DS . $driver . '.php', $output);
		}

		$output = <<<CLASS
<?php

Autoloader::add_core_namespace('{$class_name}');

Autoloader::add_classes(array(
	'{$class_name}\\\\{$class_name}' => __DIR__ . '/classes/{$name}.php',
	'{$class_name}\\\\{$class_name}Exception' => __DIR__ . '/classes/{$name}.php',

	'{$class_name}\\\\{$class_name}_Driver' => __DIR__ . '/classes/{$name}/driver.php',
{$bootstrap}
));
CLASS;
		static::create($path . 'bootstrap.php', $output);
	}

}
/* End of file tasks/generate.php */
