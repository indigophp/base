<?php

namespace Fuel\Tasks;

class Server
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r server
	 *
	 * @return string
	 */
	public static function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Server:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
	}



	/**
	 * Start oil server
	 *
	 * Usage (from command line):
	 *
	 * php oil r server:start
	 *
	 * @return string
	 */
	public static function start()
	{
		$php = \Cli::option('php', \Config::get('base.server.php', 'php'));
		$port = \Cli::option('p', \Cli::option('port', \Config::get('base.server.port', '8000')));
		$host = \Cli::option('h', \Cli::option('host', \Config::get('base.server.host', 'localhost')));
		$docroot = \Cli::option('d', \Cli::option('docroot', \Config::get('base.server.docroot', 'public' . DS)));
		$router = \Cli::option('r', \Cli::option('router', \Config::get('base.server.router')));
		$pid = \Config::get('base.server.pid', APPPATH . 'tmp' . DS . 'server.pid');

		\Cli::write("Listening on http://$host:$port");
		\Cli::write("Document root is $docroot");

		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w") // stderr is a file to write to
		);

		$process = proc_open("nohup php -S $host:$port -t $docroot $router & echo $! > $pid", $descriptorspec, $pipes);
		proc_close($process);
	}

	/**
	 * Stop oil server
	 *
	 * Usage (from command line):
	 *
	 * php oil r server:stop
	 *
	 * @return string
	 */
	public static function stop()
	{
		$path = \Config::get('base.server.pid', APPPATH . 'tmp' . DS . 'server.pid');

		$pid = \File::read($path, true);
		if ( ! empty($pid)) {
			exec("kill $pid");
			exec("cat /dev/null > $path");
			\Cli::write('Server stopped');
		}
		else
		{
			\Cli::write('Server is not running');
		}
	}

	/**
	 * Restart oil server
	 *
	 * Usage (from command line):
	 *
	 * php oil r server:restart
	 *
	 * @return string
	 */
	public static function restart()
	{
		static::stop();
		static::start();
	}

	/**
	 * Status of oil server
	 *
	 * Usage (from command line):
	 *
	 * php oil r server:restart
	 *
	 * @return string
	 */
	public static function status()
	{
		$path = \Config::get('base.server.pid', APPPATH . 'tmp' . DS . 'server.pid');
		$pid = \File::read($path, true);
		if ( ! empty($pid)) {
			$process = exec("ps up $pid 2>&1");
			if ($process = strstr($process, "php -S"))
			{
				$process = explode(' ', $process);
				$host = \Arr::get($process, 2);
				$docroot = \Arr::get($process, 4);
				\Cli::write("Listening on http://$host");
				\Cli::write("Document root is $docroot");
			}
			else
			{
				exec("cat /dev/null > $path");
			}
		}
		else
		{
			\Cli::write('Server is not running');
		}
	}

}
/* End of file tasks/server.php */
