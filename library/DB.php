<?php

use \Database\MySQLi;

class DB 
{
	static public $connections = [];

    /**
     **********************getInstance*******************
     * description
     * 2019/3/133:22 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $instance
     * @return Database\MySQLi
     * @throws Exception
     */
	static public function getInstance($instance = 'master')
	{
        $config = (new Yaf\Config\Ini(APPLICATION_PATH. DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'database.ini'))->database;
        $connection = !empty($config) && isset($config['driver']) ? $config['driver'] : 'mysql';
		if(!isset(static::$connections[$connection][$instance]))
        {
			if(empty($config->{$connection}[$instance]))
			{
				throw new Exception("Database connection is not defined for [$connection-"."$instance].");
            }
            $config = $config->{$connection}[$instance];
			$config = [
				'driver' => $connection,
				'host' => $config->get('host'),
				'port' => $config->get('port'),
				'charset' => $config->get('charset'),
				'prefix' => $config->get('prefix'),
				'username' => $config->get('username'),
				'password' => $config->get('password'),
				'database' => $config->get('database'),
            ];
			static::$connections[$connection][$instance] = new MySQLi($config);
        }
		return static::$connections[$connection][$instance];
	}

    /**
     **********************__callStatic*******************
     * description
     * 2019/3/133:22 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    static public function __callStatic($method,$parameters)
    {
		return call_user_func_array([static::getInstance(),$method],$parameters);
	}

    
}
