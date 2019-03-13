<?php


class Cache
{
    static public $drivers = [];
    static public $registrar = [];

    /**
     **********************getInstance*******************
     * description
     * 2019/3/132:58 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $instance
     * @return mixed
     * @throws Exception
     */
	static public function getInstance($instance = 'master')
    {
        $config = (new \Yaf\Config\Ini(APPLICATION_PATH. DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'cache.ini'))->cache;
        $driver = !empty($config) && isset($config['driver']) ? $config['driver'] : 'redis';
		if(!isset(static::$drivers[$driver]))
        {
            if(!isset($config[$driver][$instance]))
            {
                throw new \Exception("Cache connections is not defined for [$driver-$instance]", 400);
            }
			static::$drivers[$driver][$instance] = static::factory($driver, $instance);
		}
		return static::$drivers[$driver][$instance];
	}

    /**
     **********************factory*******************
     * description
     * 2019/3/132:58 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $driver
     * @param $instance
     * @return \Cache\Instance\Database|\Cache\Instance\File|\Cache\Instance\Redis
     * @throws Exception
     */
	static protected function factory($driver,$instance)
	{
		if(isset(static::$registrar[$driver]))
		{
			$resolver = static::$registrar[$driver];
			return $resolver();
		}
		switch ($driver)
		{
			case 'file':
				return new Cache\Instance\File(\Yaf_Application::app()->getConfig()->get('cache.path'));
			case 'redis':
				return new \Cache\Instance\Redis(\Cache\Connector\RedisConnection::getInstance($instance));
			case 'database':
				return new Cache\Instance\Database(\Yaf_Application::app()->getConfig()->get('cache.key'));
			default:
				throw new \Exception("Cache driver {$driver} is not supported.",-1001);
		}
	}


    /**
     **********************extend*******************
     * description
     * 2019/3/133:06 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $driver
     * @param Closure $resolver
     */
	static public function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}


    /**
     **********************__callStatic*******************
     * description
     * 2019/3/133:06 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
	static public function __callStatic($method, $parameters)
	{
        try {
            return call_user_func_array(array(static::getInstance(), $method), $parameters);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

}
