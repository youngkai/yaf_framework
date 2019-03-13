<?php

namespace Database\Connector;
use PDO;

class MySQLConnector 
{

	protected $options = [
			PDO::ATTR_CASE => PDO::CASE_NATURAL,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			PDO::ATTR_STRINGIFY_FETCHES => false,
			PDO::ATTR_EMULATE_PREPARES => false,
	];

    /**
     **********************options*******************
     * description
     * 2019/3/133:07 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $config
     * @return array
     */
	protected function options($config)
	{
		$options = (isset($config['options'])) ? $config['options'] : array();

		return $options + $this->options;
	}

    /**
     **********************connect*******************
     * description
     * 2019/3/133:07 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $config
     * @return PDO
     */
	public function connect($config)
	{
		$dsn = "mysql:host={$config['host']};dbname={$config['database']}";
		if (!empty($config['port']))
		{
			$dsn .= ";port={$config['port']}";
		}
		if (isset($config['unix_socket']))
		{
			$dsn .= ";unix_socket={$config['unix_socket']}";
		}

		$connection = new \PDO($dsn, $config['username'], $config['password'], $this->options($config));
		$collation = NULL;
		$charset = $config['charset'];
		$names = "set names '$charset'".( ! is_null($collation) ? " collate '$collation'" : '');
		$connection->prepare($names)->execute();
		if (isset($config['strict']) && $config['strict'])
		{
			$connection->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
		}
		return $connection;
	}

}

