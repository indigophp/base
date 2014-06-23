<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Tasks;

use Oil\Refine;

/**
 * IndigoPHP framework base task
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Indigo
{
	/**
	 * Basic install task
	 */
	public function install()
	{
		if ($dry_run = \Cli::option('dry-run', false))
		{
			\Cli::write('INFO: Running in dry run mode.', 'yellow');
		}

		$db = \Cli::prompt('Would you like to configure database?', array('y','n'));

		if ($db === 'y')
		{
			\Config::load('db', true);

			\Cli::write('NOTE: Currently only PDO is supported. The only settings you can provide is: dsn, username, password.', 'red');

			$dsn = \Cli::prompt('Please enter a DSN', \Config::get('db.default.connection.dsn'));
			$username = \Cli::prompt('Please enter a username', \Config::get('db.default.connection.username'));
			$password = \Cli::prompt('Please enter a password', \Config::get('db.default.connection.password'));

			if ($dry_run === false)
			{
				\Config::set('db.default.connection.dsn', $dsn);
				\Config::set('db.default.connection.username', $username);
				\Config::set('db.default.connection.password', $password);

				\Config::save('db', 'db');
			}
		}
		else
		{
			\Cli::write('INFO: Database is not configured.', 'yellow');
		}

		$mgr = \Cli::prompt('Would you like to run migrations now?', array('y','n'));

		if ($mgr === 'y' and $dry_run === false)
		{
			\Cli::set_option('all', true);
			Refine::run('migrate');
		}
	}
}
