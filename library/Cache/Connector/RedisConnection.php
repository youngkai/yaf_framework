<?php

namespace Cache\Connector;


class RedisConnection 
{
	protected $conf;
	protected $connection;
	static protected $databases = [];	
	public function __construct($conf)
	{
        $this->conf = $conf;
		$this->conf['database'] = isset($this->conf['database']) ? $this->conf['database'] : 0;
		if(isset($this->conf['prefix'])){
			$this->conf['prefix'] .= '_';
		}
	}

    /**
     **********************getInstance*******************
     * description
     * 2019/3/133:02 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $instance
     * @return mixed
     * @throws \Exception
     */
    static public function getInstance($instance = 'master')
    {
        if(!isset(static::$databases[$instance]))
        {
            $config = (new \Yaf\Config\Ini(APPLICATION_PATH. DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'cache.ini'))->cache->toArray();
            if (!isset($config['driver']) || $config['driver'] != 'redis') {
                throw new \Exception("Redis driver [$instance] is not defined.");
            }

            if(!isset($config['redis']['master']) || empty($config['redis']['master'])) {
                throw new \Exception("Redis database [$instance] is not defined.");
            }
            static::$databases[$instance] = new static($config['redis']['master']);
        }
        return static::$databases[$instance];
    }

    /**
     **********************connect*******************
     * description
     * 2019/3/133:02 PM
     * author yangkai@rsung.com
     *******************************************
     * @return \Redis
     * @throws \Exception
     */
	protected function connect()
    {
		if(!is_null($this->connection)) return $this->connection;
        $this->connection = new \Redis();
		if(isset($this->conf['sock'])){
			$this->connection->connect($this->conf['sock']);
        }else{
            $func = $this->conf['persistent'] ? 'pconnect' : 'connect';
            try
            {
			    $this->connection->$func($this->conf['host'], $this->conf['port'], $this->conf['timeout']);
            }catch(\Exception $e){
                throw new \Exception($e->getMessage(),$e->getCode());
            }
		}
		if(isset($this->conf['password'])) {
			$this->connection->auth($this->conf['password']);
		}
		$this->connection->select($this->conf['database']);
		if(isset($this->conf['prefix'])) {
			$this->connection->setOption(\Redis::OPT_PREFIX, $this->conf['prefix']);
        }
		return $this->connection;
	}

    /**
     **********************getConnection*******************
     * description
     * 2019/3/133:02 PM
     * author yangkai@rsung.com
     *******************************************
     * @return \Redis
     * @throws \Exception
     */
	public function getConnection()
	{
        try {
            return $this->connect();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     **********************__call*******************
     * description
     * 2019/3/133:02 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
	public function __call($method, $parameters)
	{
        try {
            return call_user_func_array([$this->connect(), $method], $parameters);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
	
	static public function __callStatic($method, $parameters)
	{
		return call_user_func_array([static::db(),$method], $parameters);
	}
	
	public function __destruct()
	{
		if ($this->connection)
		{
			$this->connection->close();
		}
	}

}
