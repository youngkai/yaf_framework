<?php

class Authorize
{
	static public $drivers = [];
    static public $registrar = [];

    /**
     * Driver
     * @param string $driver
     * @return mixed
     * @throws Exception
     */
    static public function driver($driver = null)
    {
        if (is_null($driver)) $driver = Yaf\Registry::get('config')->auth->driver;
        if (!isset(static::$drivers[$driver])) {
			static::$drivers[$driver] = static::factory($driver);
		}
		return static::$drivers[$driver];
    }

    /**
     **********************factory*******************
     * description
     * 2019-04-0913:46
     * author yangkai@rsung.com
     *******************************************
     * @param $driver
     * @return mixed
     * @throws Exception
     */
    static protected function factory($driver)
    {
        if (isset(static::$registrar[$driver])) {
			$resolver = static::$registrar[$driver];
			return $resolver();
		}
        switch($driver) 
        {
			default:
			throw new Exception("Authorize driver {$driver} is not supported.",504);
		}
    }

	/**
	 * @param string $driver
	 * @param Closure $resolver
	 */
    static public function extend($driver, Closure $resolver)
    {
		static::$registrar[$driver] = $resolver;
    }

    /**
     * __callStatic
     * @param array $method
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    static public function __callStatic($method, $parameters)
    {
        $drive = static::driver();
		return call_user_func_array([$drive, $method], $parameters);
	}

}
