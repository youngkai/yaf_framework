<?php


use \Database\Connector\MySQLConnector;
use \Database\Instance\MySQL;
class DataBase 
{
	static public $connections = [];

	static public $registrar = [];


    /**
     **********************getInstance*******************
     * description
     * 2019/3/133:20 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $instance
     * @return mixed
     * @throws Exception
     */
	static public function getInstance($instance = 'master')
	{
        $config = (new \Yaf\Config\Ini(APPLICATION_PATH. DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'database.ini'))->database;
        $connection = !empty($config) && isset($config['driver']) ? $config['driver'] : 'mysql';
		if(!isset(static::$connections[$connection]))
        {
			if(empty($config->{$connection}[$instance]))
			{
				throw new \Exception("Database connection is not defined for [$connection-"."$instance].");
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
			static::$connections[$connection][$instance] = new MySQL(static::connect($config), $config);
		}
		return static::$connections[$connection][$instance];
	}

    /**
     **********************connect*******************
     * description
     * 2019/3/133:20 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $config
     * @return PDO
     * @throws Exception
     */
	static protected function connect($config)
	{
		return static::connector($config['driver'])->connect($config);
	}

    /**
     **********************connector*******************
     * description
     * 2019/3/133:20 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $driver
     * @return MySQLConnector
     * @throws \Exception
     */
    static protected function connector($driver)
    {
        try {
            if (isset(static::$registrar[$driver])) {
                $resolver = static::$registrar[$driver]['connector'];
                return $resolver();
            }
            switch ($driver) {
                case 'mysql':
                    return new MySQLConnector;
                default:
                    return new MySQLConnector;
            }
        }catch (\Exception $e) {
            throw new \Exception("Database driver [$driver] is not supported.", $e->getCode());
        }
	}
	
    static public function extend($name, Closure $connector, $schema = null)
    {
		static::$registrar[$name] = compact('connector','schema');
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
    static public function __callStatic($method, $parameters)
    {
		return call_user_func_array(array(static::getInstance(), $method), $parameters);
	}

    
}
